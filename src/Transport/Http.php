<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Transport;

use Innmind\Neo4j\DBAL\{
    Transport,
    Query,
    Result,
    Server,
    Authentication,
    Translator\HttpTranslator,
    HttpTransport\Transport as HttpTransport,
    Exception\ServerDown,
    Exception\QueryFailed
};
use Innmind\Http\{
    Message\Response,
    Message\Request\Request,
    Message\Method\Method,
    ProtocolVersion\ProtocolVersion
};
use Innmind\Url\Url;

final class Http implements Transport
{
    private $translator;
    private $transport;

    public function __construct(
        HttpTranslator $translator,
        HttpTransport $transport
    ) {
        $this->translator = $translator;
        $this->transport = $transport;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Query $query): Result
    {
        $response = $this->transport->fulfill(
            $this->translator->translate($query)
        );

        if (!$this->isSuccessful($response)) {
            throw new QueryFailed($query, $response);
        }

        $response = json_decode((string) $response->body(), true);
        $result = Result\Result::fromRaw($response['results'][0] ?? []);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function ping(): Transport
    {
        try {
            $code = $this
                ->transport
                ->fulfill(
                    new Request(
                        Url::fromString('/'),
                        new Method(Method::OPTIONS),
                        new ProtocolVersion(1, 1)
                    )
                )
                ->statusCode()
                ->value();
        } catch (\Exception $e) {
            throw new ServerDown(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        if ($code >= 200 && $code < 300) {
            return $this;
        }

        throw new ServerDown;
    }

    /**
     * Check if the response is successful
     *
     * @param Response $response
     *
     * @return bool
     */
    private function isSuccessful(Response $response): bool
    {
        if ($response->statusCode()->value() !== 200) {
            return false;
        }

        $json = json_decode((string) $response->body(), true);

        return count($json['errors']) === 0;
    }
}

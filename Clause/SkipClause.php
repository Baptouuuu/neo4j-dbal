<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

class SkipClause implements ClauseInterface
{
    const IDENTIFIER = 'SKIP';

    private $cypher;

    public function __construct(string $cypher)
    {
        $this->cypher = $cypher;
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return self::IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->cypher;
    }
}

<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Transaction;
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Url\Url;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testInterface()
    {
        $transaction = new Transaction(
            $endpoint = Url::fromString('http://localhost:7474/db/data/transaction/9'),
            $expiration = $this->createMock(PointInTimeInterface::class),
            $commit = Url::fromString('http://localhost:7474/db/data/transaction/9/commit')
        );

        $this->assertSame($endpoint, $transaction->endpoint());
        $this->assertSame($expiration, $transaction->expiration());
        $this->assertSame($commit, $transaction->commitEndpoint());
    }
}

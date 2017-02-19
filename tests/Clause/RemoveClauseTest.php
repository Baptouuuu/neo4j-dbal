<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause\RemoveClause;
use Innmind\Neo4j\DBAL\ClauseInterface;
use PHPUnit\Framework\TestCase;

class RemoveClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new RemoveClause('n:Foo');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('REMOVE', $c->identifier());
        $this->assertSame('n:Foo', (string) $c);
    }
}

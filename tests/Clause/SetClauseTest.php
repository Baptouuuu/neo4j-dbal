<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\SetClause,
    Clause,
    Clause\Parametrable,
    Query\Parameter,
};
use PHPUnit\Framework\TestCase;

class SetClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new SetClause('n.foo = {dumb}');

        $this->assertInstanceOf(Clause::class, $c);
        $this->assertInstanceOf(Parametrable::class, $c);
        $this->assertSame('SET', $c->identifier());
        $this->assertSame('n.foo = {dumb}', (string) $c);
        $this->assertNotSame($c, $c->withParameter('foo', 'bar'));
        $this->assertFalse($c->hasParameters());
        $this->assertTrue($c->withParameter('dumb', 'dumb')->hasParameters());
        $this->assertSame('string', (string) $c->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $c->parameters()->valueType());
        $this->assertCount(1, $c->withParameter('dumb', 'dumb')->parameters());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new SetClause('');
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyParameterKey()
    {
        (new SetClause('foo'))->withParameter('', 'foo');
    }
}

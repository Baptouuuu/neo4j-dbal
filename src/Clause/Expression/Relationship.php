<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Clause\Expression\Relationship\Distance,
    Exception\DomainException,
};
use Innmind\Immutable\{
    MapInterface,
    Map
};

final class Relationship
{
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';
    const BOTH = 'BOTH';

    private $variable;
    private $type;
    private $direction;
    private $distance;
    private $parameters;
    private $properties;

    public function __construct(
        string $variable = null,
        string $type = null,
        string $direction = self::BOTH,
        Distance $distance = null
    ) {
        $this->variable = $variable;
        $this->type = $type;
        $this->direction = $direction;
        $this->distance = $distance ?? new Distance;
        $this->parameters = new Map('string', Parameter::class);
        $this->properties = new Map('string', 'string');
    }

    public function withADistanceOf(int $distance): self
    {
        $self = clone $this;
        $self->distance = Distance::of($distance);

        return $self;
    }

    public function withADistanceBetween(int $min, int $max): self
    {
        $self = clone $this;
        $self->distance = Distance::between($min, $max);

        return $self;
    }

    public function withADistanceOfAtLeast(int $distance): self
    {
        $self = clone $this;
        $self->distance = Distance::atLeast($distance);

        return $self;
    }

    public function withADistanceOfAtMost(int $distance): self
    {
        $self = clone $this;
        $self->distance = Distance::atMost($distance);

        return $self;
    }

    public function withAnyDistance(): self
    {
        $self = clone $this;
        $self->distance = Distance::any();

        return $self;
    }

    public function withParameter(string $key, $value): self
    {
        if (empty($key)) {
            throw new DomainException;
        }

        $relationship = new self($this->variable, $this->type, $this->direction, $this->distance);
        $relationship->parameters = $this->parameters->put(
            $key,
            new Parameter($key, $value)
        );
        $relationship->properties = $this->properties;

        return $relationship;
    }

    public function withProperty(string $property, string $cypher): self
    {
        if (empty($property)) {
            throw new DomainException;
        }

        $relationship = new self($this->variable, $this->type, $this->direction, $this->distance);
        $relationship->parameters = $this->parameters;
        $relationship->properties = $this->properties->put($property, $cypher);

        return $relationship;
    }

    /**
     * @return MapInterface<string, Parameter>
     */
    public function parameters(): MapInterface
    {
        return $this->parameters;
    }

    public function __toString(): string
    {
        $properties = '';

        if ($this->properties->count() > 0) {
            $properties = sprintf(
                ' { %s }',
                $this
                    ->properties
                    ->map(function(string $property, string $value): string {
                        return sprintf(
                            '%s: %s',
                            $property,
                            $value
                        );
                    })
                    ->join(', ')
            );
        }

        return sprintf(
            '%s-[%s%s%s%s]-%s',
            $this->direction === self::LEFT ? '<' : null,
            $this->variable,
            $this->type ? ':'.$this->type : null,
            $this->distance,
            $properties,
            $this->direction === self::RIGHT ? '>' : null
        );
    }
}

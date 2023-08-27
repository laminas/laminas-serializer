<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Serializer\Exception\ExceptionInterface;

interface AdapterInterface
{
    /**
     * Generates a storable representation of a value.
     *
     * @param  mixed $value Data to serialize
     * @throws ExceptionInterface
     */
    public function serialize(mixed $value): string;

    /**
     * Creates a PHP value from a stored representation.
     *
     * @param  string $serialized Serialized string
     * @throws ExceptionInterface
     */
    public function unserialize(string $serialized): mixed;
}

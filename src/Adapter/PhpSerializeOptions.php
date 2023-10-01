<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

final class PhpSerializeOptions extends AdapterOptions
{
    /**
     * The list of allowed classes for unserialization (PHP 7.0+).
     *
     * Possible values:
     *
     * - `array` of class names that are allowed to be unserialized
     * - `true` if all classes should be allowed (behavior pre-PHP 7.0)
     * - `false` if no classes should be allowed
     *
     * @var class-string[]|bool
     */
    protected bool|array $unserializeClassWhitelist = true;

    /**
     * @param class-string[]|bool $unserializeClassWhitelist
     */
    public function setUnserializeClassWhitelist(bool|array $unserializeClassWhitelist): void
    {
        $this->unserializeClassWhitelist = $unserializeClassWhitelist;
    }

    /**
     * @return class-string[]|bool
     */
    public function getUnserializeClassWhitelist(): bool|array
    {
        return $this->unserializeClassWhitelist;
    }
}

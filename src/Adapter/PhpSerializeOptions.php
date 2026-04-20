<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

final class PhpSerializeOptions extends AdapterOptions
{
    /**
     * The list of allowed classes for unserialization (PHP 7.0+).
     *
     * @see https://www.php.net/unserialize
     *
     * Possible values:
     *
     * - `array` of class names that are allowed to be unserialized
     * - `true` if all classes should be allowed (behavior pre-PHP 7.0)
     * - `false` if no classes should be allowed
     *
     * @var list<class-string>|bool
     */
    protected bool|array $allowedClasses = true;

    /**
     * @see https://www.php.net/unserialize
     *
     * @param list<class-string>|bool $allowedClasses
     */
    public function setAllowedClasses(bool|array $allowedClasses): void
    {
        $this->allowedClasses = $allowedClasses;
    }

    /**
     * @see https://www.php.net/unserialize
     *
     * @return list<class-string>|bool
     */
    public function getAllowedClasses(): bool|array
    {
        return $this->allowedClasses;
    }
}

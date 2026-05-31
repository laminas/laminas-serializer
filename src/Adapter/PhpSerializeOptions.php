<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Stdlib\AbstractOptions;

use function iterator_to_array;

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
     * @deprecated Since 3.3.0. Use $allowedClasses instead
     *
     * @var class-string[]|bool
     */
    protected bool|array $unserializeClassWhitelist = false;

    /**
     * The list of allowed classes for unserialization (PHP 7.0+).
     *
     * @see https://www.php.net/unserialize
     *
     * Defaults to `false` (deny all classes) for safety. Set to `true` to
     * restore the legacy behaviour of allowing all classes, or supply an
     * explicit allowlist of class-string names.
     *
     * Possible values:
     *
     * - `array` of class names that are allowed to be unserialized
     * - `true` if all classes should be allowed (legacy behaviour)
     * - `false` if no classes should be allowed (safe default)
     *
     * @var list<class-string>|bool
     */
    protected bool|array $allowedClasses = false;

    /**
     * @param iterable<string, mixed>|AbstractOptions<mixed>|null $options
     */
    public function __construct(iterable|AbstractOptions|null $options = null)
    {
        $options = $options instanceof AbstractOptions
            ? $options->toArray()
            : iterator_to_array($options ?? []);

        if (isset($options['unserialize_class_whitelist']) && ! isset($options['allowed_classes'])) {
            $options['allowed_classes'] = $options['unserialize_class_whitelist'];
        }

        parent::__construct($options);
    }

    /**
     * @deprecated Since 3.3.0. Use {@link setAllowedClasses()} instead
     *
     * @param list<class-string>|bool $unserializeClassWhitelist
     */
    public function setUnserializeClassWhitelist(bool|array $unserializeClassWhitelist): void
    {
        $this->allowedClasses = $unserializeClassWhitelist;
    }

    /**
     * @deprecated Since 3.3.0. Use {@link getAllowedClasses()} instead
     *
     * @return list<class-string>|bool
     */
    public function getUnserializeClassWhitelist(): bool|array
    {
        return $this->allowedClasses;
    }

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

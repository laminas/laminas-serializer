<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Serializer\Exception;

use const PHP_MAJOR_VERSION;

class PhpSerializeOptions extends AdapterOptions
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
     * @var string[]|bool
     */
    protected $unserializeClassWhitelist = true;

    /**
     * @param string[]|bool $unserializeClassWhitelist
     * @return void
     */
    public function setUnserializeClassWhitelist($unserializeClassWhitelist)
    {
        if ($unserializeClassWhitelist !== true && PHP_MAJOR_VERSION < 7) {
            throw new Exception\InvalidArgumentException(
                'Class whitelist for unserialize() is only available on PHP versions 7.0 or higher.'
            );
        }

        $this->unserializeClassWhitelist = $unserializeClassWhitelist;
    }

    /**
     * @return string[]|bool
     */
    public function getUnserializeClassWhitelist()
    {
        return $this->unserializeClassWhitelist;
    }
}

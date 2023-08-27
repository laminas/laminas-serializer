<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Serializer\Exception;
use Laminas\Stdlib\ErrorHandler;

use function extension_loaded;
use function igbinary_serialize;
use function igbinary_unserialize;

final class IgBinary extends AbstractAdapter
{
    /** @var string|null Serialized null value */
    private static string|null $serializedNull = null;

    /**
     * @throws Exception\ExtensionNotLoadedException If igbinary extension is not present.
     */
    public function __construct()
    {
        if (! extension_loaded('igbinary')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "igbinary" is required for this adapter'
            );
        }

        if (self::$serializedNull === null) {
            self::$serializedNull = igbinary_serialize(null);
        }

        parent::__construct(null);
    }

    /**
     * Serialize PHP value to igbinary
     *
     * @throws Exception\RuntimeException On igbinary error.
     */
    public function serialize(mixed $value): string
    {
        ErrorHandler::start();
        $ret = igbinary_serialize($value);
        $err = ErrorHandler::stop();

        if ($ret === false) {
            throw new Exception\RuntimeException('Serialization failed', 0, $err);
        }

        return $ret;
    }

    /**
     * Deserialize igbinary string to PHP value
     *
     * @throws Exception\RuntimeException On igbinary error.
     */
    public function unserialize(string $serialized): mixed
    {
        if ($serialized === self::$serializedNull) {
            return null;
        }

        ErrorHandler::start();
        $ret = igbinary_unserialize($serialized);
        $err = ErrorHandler::stop();

        if ($ret === null) {
            throw new Exception\RuntimeException('Unserialization failed', 0, $err);
        }

        return $ret;
    }
}

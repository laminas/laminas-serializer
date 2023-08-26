<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Serializer\Exception;
use Laminas\Stdlib\ErrorHandler;

use function preg_match;
use function serialize;
use function unserialize;

use const E_NOTICE;

final class PhpSerialize extends AbstractAdapter
{
    /**
     * Serialized boolean false value
     */
    private static null|string $serializedFalse = null;

    /** @var PhpSerializeOptions|null */
    protected AdapterOptions|null $options = null;

    public function __construct(iterable|PhpSerializeOptions|null $options = null)
    {
        // needed to check if a returned false is based on a serialize false
        // or based on failure (igbinary can overwrite [un]serialize functions)
        if (self::$serializedFalse === null) {
            self::$serializedFalse = serialize(false);
        }

        parent::__construct($options);
    }

    /**
     * Set options
     *
     * @param iterable|PhpSerializeOptions $options
     */
    public function setOptions(iterable|AdapterOptions $options): void
    {
        if (! $options instanceof PhpSerializeOptions) {
            $options = new PhpSerializeOptions($options);
        }

        $this->options = $options;
    }

    /**
     * Get options
     */
    public function getOptions(): PhpSerializeOptions
    {
        if ($this->options === null) {
            $this->options = new PhpSerializeOptions();
        }

        return $this->options;
    }

    /**
     * Serialize using serialize()
     *
     * @throws Exception\RuntimeException On serialize error.
     */
    public function serialize(mixed $value): string
    {
        ErrorHandler::start();
        $ret = serialize($value);
        $err = ErrorHandler::stop();
        if ($err) {
            throw new Exception\RuntimeException('Serialization failed', 0, $err);
        }

        return $ret;
    }

    /**
     * Unserialize
     *
     * @todo   Allow integration with unserialize_callback_func
     * @throws Exception\RuntimeException On unserialize error.
     */
    public function unserialize(string $serialized): mixed
    {
        if (! preg_match('/^((s|i|d|b|a|O|C):|N;)/', $serialized)) {
            throw new Exception\RuntimeException(
                'Serialized data must be a string containing serialized PHP code; received a string with incompatible'
                . ' value.',
            );
        }

        // If we have a serialized boolean false value, just return false;
        // prevents the unserialize handler from creating an error.
        if ($serialized === self::$serializedFalse) {
            return false;
        }

        ErrorHandler::start(E_NOTICE);
        // The second parameter to unserialize() is only available on PHP 7.0 or higher
        $ret = unserialize($serialized, ['allowed_classes' => $this->getOptions()->getUnserializeClassWhitelist()]);
        $err = ErrorHandler::stop();

        if ($ret === false) {
            throw new Exception\RuntimeException('Unserialization failed', 0, $err);
        }

        return $ret;
    }
}

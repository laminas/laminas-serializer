<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Serializer\Exception;

/**
 * @deprecated This serializer will get removed in v3.0.0. There is no replacement.
 */
class PythonPickleOptions extends AdapterOptions
{
    /**
     * Pickle protocol version to serialize data
     *
     * @var int
     */
    protected $protocol = 0;

    /**
     * Set pickle protocol version to serialize data
     *
     * Supported versions are 0, 1, 2 and 3
     *
     * @param  int $protocol
     * @return PythonPickleOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setProtocol($protocol)
    {
        $protocol = (int) $protocol;
        if ($protocol < 0 || $protocol > 3) {
            throw new Exception\InvalidArgumentException(
                "Invalid or unknown protocol version '{$protocol}'"
            );
        }

        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get pickle protocol version to serialize data
     *
     * @return int
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}

<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use InvalidArgumentException;
use JsonException;
use Laminas\Serializer\Exception;

use function json_decode;
use function json_encode;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class Json extends AbstractAdapter
{
    /** @var JsonOptions|null */
    protected AdapterOptions|null $options = null;

    /**
     * Set options
     *
     * @param iterable<string,mixed>|JsonOptions $options
     */
    public function setOptions(iterable|AdapterOptions $options): void
    {
        if (! $options instanceof JsonOptions) {
            $options = new JsonOptions($options);
        }

        $this->options = $options;
    }

    /**
     * Get options
     */
    public function getOptions(): JsonOptions
    {
        if ($this->options === null) {
            $this->options = new JsonOptions();
        }

        return $this->options;
    }

    /**
     * Serialize PHP value to JSON
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function serialize(mixed $value): string
    {
        try {
            return $this->encode($value);
        } catch (InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException('Serialization failed: ' . $e->getMessage(), 0, $e);
        } catch (JsonException $e) {
            throw new Exception\RuntimeException('Serialization failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Deserialize JSON to PHP value
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function unserialize(string $serialized): mixed
    {
        try {
            $ret = $this->decode($serialized, $this->getOptions()->isAssocArray());
        } catch (JsonException $e) {
            throw new Exception\RuntimeException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        }

        return $ret;
    }

    private function encode(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
    }

    private function decode(string $value, bool $assoc): mixed
    {
        return json_decode(
            $value,
            $assoc,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}

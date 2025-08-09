<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use InvalidArgumentException;
use JsonException;
use Laminas\Serializer\Exception;

use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function json_decode;
use function json_encode;

use const JSON_THROW_ON_ERROR;

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
        $options    = $this->getOptions();
        $cycleCheck = $options->getCycleCheck();
        $opts       = [
            'enableJsonExprFinder' => $options->getEnableJsonExprFinder(),
            'objectDecodeType'     => $options->isAssocArray(),
        ];

        try {
            return $this->encode($value, $cycleCheck, $opts);
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

    /**
     * @param mixed[] $options
     */
    private function encode(mixed $value, bool $cycleCheck, array $options): string
    {
        if ($cycleCheck) {
            $seen         = [];
            $detectCycles = function (mixed &$val) use (&$seen, &$detectCycles): void {
                if (is_array($val) || is_object($val)) {
                    if (in_array($val, $seen, true)) {
                        throw new InvalidArgumentException("Cycle detected in value to be JSON encoded");
                    }
                    $seen[] = $val;
                    foreach ($val as &$item) {
                        $detectCycles($item);
                    }
                }
            };
            $detectCycles($value);
        }

        $jsonOptions = isset($options['json_encode_options']) && is_int($options['json_encode_options'])
            ? $options['json_encode_options']
            : 0;

        $encoded = json_encode($value, $jsonOptions | JSON_THROW_ON_ERROR);
        if ($encoded === false) {
            throw new JsonException('Syntax error');
        }

        return $encoded;
    }

    private function decode(string $value, bool $assoc): mixed
    {
        return json_decode(
            $value,
            $assoc,
            512, // depth
            JSON_THROW_ON_ERROR
        );
    }
}

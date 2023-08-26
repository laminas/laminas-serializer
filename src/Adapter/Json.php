<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use InvalidArgumentException;
use Laminas\Json\Json as LaminasJson;
use Laminas\Serializer\Exception;

final class Json extends AbstractAdapter
{
    /** @var JsonOptions|null */
    protected AdapterOptions|null $options = null;

    /**
     * Set options
     *
     * @param iterable|JsonOptions $options
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
            'objectDecodeType'     => $options->getObjectDecodeType(),
        ];

        try {
            return LaminasJson::encode($value, $cycleCheck, $opts);
        } catch (InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException('Serialization failed: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
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
            $ret = LaminasJson::decode($serialized, $this->getOptions()->getObjectDecodeType());
        } catch (InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new Exception\RuntimeException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        }

        return $ret;
    }
}

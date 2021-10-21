<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Json\Json as LaminasJson;
use Laminas\Serializer\Exception;

class JsonOptions extends AdapterOptions
{
    /** @var bool */
    protected $cycleCheck = false;

    /** @var bool */
    protected $enableJsonExprFinder = false;

    /** @var int */
    protected $objectDecodeType = LaminasJson::TYPE_ARRAY;

    /**
     * @return JsonOptions
     */
    public function setCycleCheck(bool $flag): self
    {
        $this->cycleCheck = $flag;
        return $this;
    }

    public function getCycleCheck(): bool
    {
        return $this->cycleCheck;
    }

    /**
     * @return JsonOptions
     */
    public function setEnableJsonExprFinder(bool $flag): self
    {
        $this->enableJsonExprFinder = $flag;
        return $this;
    }

    public function getEnableJsonExprFinder(): bool
    {
        return $this->enableJsonExprFinder;
    }

    /**
     * @return JsonOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setObjectDecodeType(int $type): self
    {
        if ($type !== LaminasJson::TYPE_ARRAY && $type !== LaminasJson::TYPE_OBJECT) {
            throw new Exception\InvalidArgumentException(
                'Unknown decode type: ' . $type
            );
        }

        $this->objectDecodeType = $type;

        return $this;
    }

    public function getObjectDecodeType(): int
    {
        return $this->objectDecodeType;
    }
}

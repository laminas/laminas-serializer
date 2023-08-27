<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Json\Json as LaminasJson;
use Laminas\Serializer\Exception;

final class JsonOptions extends AdapterOptions
{
    protected bool $cycleCheck = false;

    protected bool $enableJsonExprFinder = false;

    /** @var LaminasJson::TYPE_* */
    protected int $objectDecodeType = LaminasJson::TYPE_ARRAY;

    public function setCycleCheck(bool $flag): void
    {
        $this->cycleCheck = $flag;
    }

    public function getCycleCheck(): bool
    {
        return $this->cycleCheck;
    }

    public function setEnableJsonExprFinder(bool $flag): void
    {
        $this->enableJsonExprFinder = $flag;
    }

    public function getEnableJsonExprFinder(): bool
    {
        return $this->enableJsonExprFinder;
    }

    /**
     * @param LaminasJson::TYPE_* $type
     * @throws Exception\InvalidArgumentException
     */
    public function setObjectDecodeType(int $type): void
    {
        /**
         * @psalm-suppress DocblockTypeContradiction Due to the way how the options for plugins work, i.e. using
         *                                        {@see AbstractOptions::setFromArray()}, having an additional check
         *                                        here can provide more detailed errors.
         */
        if ($type !== LaminasJson::TYPE_ARRAY && $type !== LaminasJson::TYPE_OBJECT) {
            throw new Exception\InvalidArgumentException(
                'Unknown decode type: ' . $type
            );
        }

        $this->objectDecodeType = $type;
    }

    /**
     * @return LaminasJson::TYPE_*
     */
    public function getObjectDecodeType(): int
    {
        return $this->objectDecodeType;
    }
}

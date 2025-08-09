<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use Laminas\Serializer\Exception;

final class JsonOptions extends AdapterOptions
{
    protected bool $cycleCheck = false;

    protected bool $enableJsonExprFinder = false;
    private bool $assocArray             = true;

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
     * @throws Exception\InvalidArgumentException
     */
    public function setAssocArray(bool $assocArray): void
    {
        $this->assocArray = $assocArray;
    }

    public function isAssocArray(): bool
    {
        return $this->assocArray;
    }
}

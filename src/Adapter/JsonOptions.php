<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

final class JsonOptions extends AdapterOptions
{
    private bool $assocArray = true;

    public function setAssocArray(bool $assocArray): void
    {
        $this->assocArray = $assocArray;
    }

    public function isAssocArray(): bool
    {
        return $this->assocArray;
    }
}

<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

abstract class AbstractAdapter implements AdapterInterface
{
    protected AdapterOptions|null $options = null;

    public function __construct(iterable|AdapterOptions|null $options = null)
    {
        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * Set adapter options
     */
    public function setOptions(iterable|AdapterOptions $options): void
    {
        if (! $options instanceof AdapterOptions) {
            $options = new AdapterOptions($options);
        }

        $this->options = $options;
    }

    /**
     * Get adapter options
     */
    public function getOptions(): AdapterOptions
    {
        if ($this->options === null) {
            $this->options = new AdapterOptions();
        }
        return $this->options;
    }
}

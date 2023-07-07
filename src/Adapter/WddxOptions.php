<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

/**
 * @deprecated This serializer will get removed in v3.0.0. There is no replacement.
 */
class WddxOptions extends AdapterOptions
{
    /**
     * Wddx packet header comment
     *
     * @var string
     */
    protected $comment = '';

    /**
     * Set WDDX header comment
     *
     * @param  string $comment
     * @return WddxOptions
     */
    public function setComment($comment)
    {
        $this->comment = (string) $comment;
        return $this;
    }

    /**
     * Get WDDX header comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}

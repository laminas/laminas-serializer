<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer\Adapter\PhpSerializeOptions;
use PHPUnit\Framework\TestCase;

final class PhpSerializeOptionsTest extends TestCase
{
    public function testSetGetOption(): void
    {
        $options = new PhpSerializeOptions([
            'allowed_classes' => [self::class],
        ]);

        self::assertSame([self::class], $options->getAllowedClasses());

        $options->setAllowedClasses(true);
        self::assertTrue($options->getAllowedClasses());
    }
}

<?php

declare(strict_types=1);

namespace Laminas\Serializer\Adapter;

use ErrorException;
use Laminas\Serializer\Exception;
use Laminas\Stdlib\ErrorHandler;

use function assert;
use function error_get_last;
use function var_export;

use const E_ALL;

final class PhpCode extends AbstractAdapter
{
    /**
     * Serialize PHP using var_export
     */
    public function serialize(mixed $value): string
    {
        return var_export($value, true);
    }

    /**
     * Deserialize PHP string
     *
     * Warning: this uses eval(), and should likely be avoided.
     *
     * @throws Exception\RuntimeException On eval error.
     */
    public function unserialize(string $serialized): mixed
    {
        ErrorHandler::start(E_ALL);
        $ret = null;
        // This suppression is due to the fact that the ErrorHandler cannot
        // catch syntax errors, and is intentionally left in place.
        $eval = @eval('$ret=' . $serialized . ';');
        $err  = ErrorHandler::stop();
        assert($err === null || $err instanceof ErrorException);

        if ($eval === false || $err) {
            $msg = 'eval failed';

            // Error handler doesn't catch syntax errors
            if ($eval === false) {
                $lastErr = error_get_last();
                if (isset($lastErr['message'])) {
                    $msg .= ': ' . $lastErr['message'];
                }
            }

            throw new Exception\RuntimeException($msg, 0, $err);
        }

        return $ret;
    }
}

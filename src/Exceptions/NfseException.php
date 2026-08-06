<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Exceptions;

use RuntimeException;

final class NfseException extends RuntimeException
{
    public const STOP_CRITICAL = 1;
    public const WARNING_MESSAGE = 2;
}

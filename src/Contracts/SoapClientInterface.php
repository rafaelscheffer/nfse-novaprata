<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Contracts;

interface SoapClientInterface
{
    public function post(string $url, string $soapAction, string $envelope, int $timeoutSeconds): string;
}

<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Contracts;

interface CertificateManagerInterface
{
    public function load(
        string $senhaPfxNfse,
        string $codigoMunicipioIBGE,
        string $certificado,
        string $pastacertificado
    ): bool;

    public function getPrivateKeyPath(): string;

    public function getCleanCertificate(): string;
}

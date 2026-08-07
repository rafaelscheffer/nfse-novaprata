<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Contracts;

interface NfseRepositoryInterface
{
    public function listUltimaNota(): array;

    public function listNotas(): array;

    public function cadastrarNfseBanco(
        $numeronota,
        $numerolote,
        $numerorps,
        $protocolo,
        $linknota,
        $codigoverificacao
    ): void;

    public function deletaNfseBanco($cod): void;
}

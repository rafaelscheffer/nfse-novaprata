<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Repositories;

use NovaPrata\Nfse\Config\Config;
use NovaPrata\Nfse\Contracts\NfseRepositoryInterface;
use NovaPrata\Nfse\Exceptions\NfseException;
use PDO;
use PDOException;

final class NfseRepository implements NfseRepositoryInterface
{
    public function __construct(private readonly Config $config)
    {
    }

    private function connect(): PDO
    {
        $pdo = new PDO($this->config->dbDsn(), $this->config->dbUsername(), $this->config->dbPassword());
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    public function listUltimaNota(): array
    {
        $conecta = $this->connect();
        $sql = "SELECT * FROM itensnfse order by id asc";
        try {
            $query = $conecta->prepare($sql);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            throw new NfseException(
                'Erro ao listar os itens da nfse: ' . $erro->getMessage(),
                NfseException::STOP_CRITICAL
            );
        }
    }

    public function listNotas(): array
    {
        $conecta = $this->connect();
        $sql = "SELECT * FROM nfse order by id desc";
        try {
            $query = $conecta->prepare($sql);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            throw new NfseException(
                'Erro ao listar as notas fiscais: ' . $erro->getMessage(),
                NfseException::STOP_CRITICAL
            );
        }
    }

    public function cadastrarNfseBanco(
        $numeronota,
        $numerolote,
        $numerorps,
        $protocolo,
        $linknota,
        $codigoverificacao
    ): void {
        $conecta = $this->connect();

        try {
            $queryItens = $conecta->prepare(
                "INSERT INTO itensnfse VALUES ('0', :numeronota, :numerolote, :numerorps)"
            );
            $queryItens->execute([
                'numeronota' => $numeronota,
                'numerolote' => $numerolote,
                'numerorps' => $numerorps,
            ]);

            $queryNfse = $conecta->prepare(
                "INSERT INTO nfse VALUES ('0', :numeronota, :numerolote, :numerorps, :protocolo, :linknota, :codigoverificacao)"
            );
            $queryNfse->execute([
                'numeronota' => $numeronota,
                'numerolote' => $numerolote,
                'numerorps' => $numerorps,
                'protocolo' => $protocolo,
                'linknota' => $linknota,
                'codigoverificacao' => $codigoverificacao,
            ]);
        } catch (PDOException $erro) {
            throw new NfseException('Erro ao cadastrar a nfse: ' . $erro->getMessage(), NfseException::STOP_CRITICAL);
        }
    }

    public function deletaNfseBanco($cod): void
    {
        $conecta = $this->connect();
        try {
            $query = $conecta->prepare("DELETE FROM nfse WHERE numeronota = :numeronota");
            $query->execute(['numeronota' => (int) $cod]);
        } catch (PDOException $erro) {
            throw new NfseException('Erro ao deletar a nfse: ' . $erro->getMessage(), NfseException::STOP_CRITICAL);
        }
    }
}

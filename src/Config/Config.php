<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Config;

use Dotenv\Dotenv;

final class Config
{
    public function __construct(
        private readonly string $dbDriver,
        private readonly string $dbHost,
        private readonly string $dbDatabase,
        private readonly string $dbUsername,
        private readonly string $dbPassword,
        private readonly string $nfseAmbiente,
        private readonly string $nfseUrlEnvioHomologacao,
        private readonly string $nfseUrlEnvioProducao,
        private readonly string $nfseUrlCancelarHomologacao,
        private readonly string $nfseUrlCancelarProducao,
        private readonly string $nfseUrlConsulta,
        private readonly string $nfseUrlConsultaLoteHomologacao,
        private readonly string $nfseUrlConsultaLoteProducao,
        private readonly string $providerCnpj,
        private readonly string $providerInscricaoMunicipal,
        private readonly string $providerCodigoMunicipio,
        private readonly string $providerRazaoSocial,
        private readonly string $certPath,
        private readonly string $certFile,
        private readonly string $certPassword,
    ) {
    }

    public static function fromEnvironment(?string $basePath = null): self
    {
        $basePath ??= dirname(__DIR__, 2);

        if (is_file($basePath . '/.env')) {
            Dotenv::createImmutable($basePath)->safeLoad();
        }

        return new self(
            dbDriver: self::env('DB_DRIVER', 'mysql'),
            dbHost: self::env('DB_HOST', 'localhost'),
            dbDatabase: self::env('DB_DATABASE', 'bancotestenfse'),
            dbUsername: self::env('DB_USERNAME', 'root'),
            dbPassword: self::env('DB_PASSWORD', ''),
            nfseAmbiente: self::env('NFSE_AMBIENTE', 'homologacao'),
            nfseUrlEnvioHomologacao: self::env(
                'NFSE_URL_ENVIO_HOMOLOGACAO',
                'http://homologaprata.nfse-tecnos.com.br:9091'
            ),
            nfseUrlEnvioProducao: self::env(
                'NFSE_URL_ENVIO_PRODUCAO',
                'http://novaprata.nfse-tecnos.com.br:9091'
            ),
            nfseUrlCancelarHomologacao: self::env(
                'NFSE_URL_CANCELAR_HOMOLOGACAO',
                'http://homologaprata.nfse-tecnos.com.br:9098'
            ),
            nfseUrlCancelarProducao: self::env(
                'NFSE_URL_CANCELAR_PRODUCAO',
                'http://novaprata.nfse-tecnos.com.br:9098'
            ),
            nfseUrlConsulta: self::env(
                'NFSE_URL_CONSULTA',
                'http://novaprata.nfse-tecnos.com.br:9084'
            ),
            nfseUrlConsultaLoteHomologacao: self::env(
                'NFSE_URL_CONSULTALOTE_HOMOLOGACAO',
                'http://homologaprata.nfse-tecnos.com.br:9097'
            ),
            nfseUrlConsultaLoteProducao: self::env(
                'NFSE_URL_CONSULTALOTE_PRODUCAO',
                'http://novaprata.nfse-tecnos.com.br:9097'
            ),
            providerCnpj: self::env('NFSE_PROVIDER_CNPJ', '66530585000160'),
            providerInscricaoMunicipal: self::env('NFSE_PROVIDER_INSCRICAO_MUNICIPAL', '00000000'),
            providerCodigoMunicipio: self::env('NFSE_PROVIDER_CODIGO_MUNICIPIO', '0000000'),
            providerRazaoSocial: self::env('NFSE_PROVIDER_RAZAO_SOCIAL', 'Empresa Ltda - ME'),
            certPath: self::env('NFSE_CERT_PATH', 'cert'),
            certFile: self::env('NFSE_CERT_FILE', 'teste.pfx'),
            certPassword: self::env('NFSE_CERT_PASSWORD', ''),
        );
    }

    private static function env(string $key, string $default): string
    {
        $value = $_ENV[$key] ?? getenv($key);

        return ($value === false || $value === null || $value === '') ? $default : (string) $value;
    }

    public function dbDsn(): string
    {
        return sprintf('%s:host=%s;dbname=%s', $this->dbDriver, $this->dbHost, $this->dbDatabase);
    }

    public function dbUsername(): string
    {
        return $this->dbUsername;
    }

    public function dbPassword(): string
    {
        return $this->dbPassword;
    }

    public function isProducao(): bool
    {
        return $this->nfseAmbiente === 'producao';
    }

    public function nfseUrlEnvio(): string
    {
        return $this->isProducao() ? $this->nfseUrlEnvioProducao : $this->nfseUrlEnvioHomologacao;
    }

    public function nfseUrlCancelar(): string
    {
        return $this->isProducao() ? $this->nfseUrlCancelarProducao : $this->nfseUrlCancelarHomologacao;
    }

    public function nfseUrlConsulta(): string
    {
        // Mesma URL em homologacao e producao, conforme documentado no help.txt original do projeto.
        return $this->nfseUrlConsulta;
    }

    public function nfseUrlConsultaLote(): string
    {
        return $this->isProducao() ? $this->nfseUrlConsultaLoteProducao : $this->nfseUrlConsultaLoteHomologacao;
    }

    public function providerCnpj(): string
    {
        return $this->providerCnpj;
    }

    public function providerInscricaoMunicipal(): string
    {
        return $this->providerInscricaoMunicipal;
    }

    public function providerCodigoMunicipio(): string
    {
        return $this->providerCodigoMunicipio;
    }

    public function providerRazaoSocial(): string
    {
        return $this->providerRazaoSocial;
    }

    public function certPath(): string
    {
        return $this->certPath;
    }

    public function certFile(): string
    {
        return $this->certFile;
    }

    public function certPassword(): string
    {
        return $this->certPassword;
    }
}

<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Tests\Unit\Config;

use NovaPrata\Nfse\Config\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    private const ENV_KEYS = [
        'DB_DRIVER', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
        'NFSE_AMBIENTE',
        'NFSE_URL_ENVIO_HOMOLOGACAO', 'NFSE_URL_ENVIO_PRODUCAO',
        'NFSE_URL_CANCELAR_HOMOLOGACAO', 'NFSE_URL_CANCELAR_PRODUCAO',
        'NFSE_URL_CONSULTA',
        'NFSE_URL_CONSULTALOTE_HOMOLOGACAO', 'NFSE_URL_CONSULTALOTE_PRODUCAO',
        'NFSE_PROVIDER_CNPJ', 'NFSE_PROVIDER_INSCRICAO_MUNICIPAL',
        'NFSE_PROVIDER_CODIGO_MUNICIPIO', 'NFSE_PROVIDER_RAZAO_SOCIAL',
        'NFSE_CERT_PATH', 'NFSE_CERT_FILE', 'NFSE_CERT_PASSWORD',
    ];

    protected function tearDown(): void
    {
        foreach (self::ENV_KEYS as $key) {
            unset($_ENV[$key]);
        }
    }

    public function testDefaultsWhenNoEnvironmentVariablesAreSet(): void
    {
        $config = Config::fromEnvironment(sys_get_temp_dir());

        $this->assertSame('mysql:host=localhost;dbname=bancotestenfse', $config->dbDsn());
        $this->assertSame('root', $config->dbUsername());
        $this->assertFalse($config->isProducao());
        $this->assertSame('66530585000160', $config->providerCnpj());
        $this->assertSame('cert', $config->certPath());
        $this->assertSame('teste.pfx', $config->certFile());
    }

    public function testUsesHomologacaoUrlsByDefault(): void
    {
        $_ENV['NFSE_URL_ENVIO_HOMOLOGACAO'] = 'http://homolog-envio';
        $_ENV['NFSE_URL_ENVIO_PRODUCAO'] = 'http://prod-envio';

        $config = Config::fromEnvironment(sys_get_temp_dir());

        $this->assertFalse($config->isProducao());
        $this->assertSame('http://homolog-envio', $config->nfseUrlEnvio());
    }

    public function testUsesProducaoUrlsWhenAmbienteIsProducao(): void
    {
        $_ENV['NFSE_AMBIENTE'] = 'producao';
        $_ENV['NFSE_URL_ENVIO_HOMOLOGACAO'] = 'http://homolog-envio';
        $_ENV['NFSE_URL_ENVIO_PRODUCAO'] = 'http://prod-envio';
        $_ENV['NFSE_URL_CANCELAR_HOMOLOGACAO'] = 'http://homolog-cancelar';
        $_ENV['NFSE_URL_CANCELAR_PRODUCAO'] = 'http://prod-cancelar';
        $_ENV['NFSE_URL_CONSULTALOTE_HOMOLOGACAO'] = 'http://homolog-lote';
        $_ENV['NFSE_URL_CONSULTALOTE_PRODUCAO'] = 'http://prod-lote';

        $config = Config::fromEnvironment(sys_get_temp_dir());

        $this->assertTrue($config->isProducao());
        $this->assertSame('http://prod-envio', $config->nfseUrlEnvio());
        $this->assertSame('http://prod-cancelar', $config->nfseUrlCancelar());
        $this->assertSame('http://prod-lote', $config->nfseUrlConsultaLote());
    }

    public function testNfseUrlConsultaIsSameRegardlessOfAmbiente(): void
    {
        $_ENV['NFSE_URL_CONSULTA'] = 'http://consulta-sequencia';
        $_ENV['NFSE_AMBIENTE'] = 'producao';

        $config = Config::fromEnvironment(sys_get_temp_dir());

        $this->assertSame('http://consulta-sequencia', $config->nfseUrlConsulta());
    }

    public function testDbDsnUsesConfiguredDriverHostAndDatabase(): void
    {
        $_ENV['DB_DRIVER'] = 'mysql';
        $_ENV['DB_HOST'] = '127.0.0.1';
        $_ENV['DB_DATABASE'] = 'meubanco';

        $config = Config::fromEnvironment(sys_get_temp_dir());

        $this->assertSame('mysql:host=127.0.0.1;dbname=meubanco', $config->dbDsn());
    }

    public function testEmptyStringEnvironmentValueFallsBackToDefault(): void
    {
        $_ENV['NFSE_PROVIDER_CNPJ'] = '';

        $config = Config::fromEnvironment(sys_get_temp_dir());

        $this->assertSame('66530585000160', $config->providerCnpj());
    }
}

<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Tests\Unit\Application\Services;

use NovaPrata\Nfse\Application\Services\NfseService;
use NovaPrata\Nfse\Config\Config;
use NovaPrata\Nfse\Contracts\NfseRepositoryInterface;
use NovaPrata\Nfse\Contracts\SoapClientInterface;
use PHPUnit\Framework\TestCase;

final class NfseServiceTest extends TestCase
{
    private function config(): Config
    {
        return Config::fromEnvironment(sys_get_temp_dir());
    }

    public function testConsultaloteParsesCannedXmlResponse(): void
    {
        $soapClient = new FakeSoapClient(
            '<retornoConsulta>'
            . '<Situacao>4</Situacao>'
            . '<Numero>123</Numero>'
            . '<CodigoVerificacao>ABC123</CodigoVerificacao>'
            . '<DataEmissao>2026-08-07</DataEmissao>'
            . '<LinkNota>http://example.org/nota/123</LinkNota>'
            . '</retornoConsulta>'
        );
        $service = new NfseService($this->config(), soapClient: $soapClient);

        $result = $service->consultalote('Razao Social', '00000000000000', '00000000', 'PROTOCOLO-1');

        $this->assertSame('4', $result['Situacao']);
        $this->assertSame('123', $result['Numero']);
        $this->assertSame('ABC123', $result['CodigoVerificacao']);
        $this->assertSame('2026-08-07', $result['DataEmissao']);
        $this->assertSame('http://example.org/nota/123', $result['LinkNota']);
        $this->assertSame('http://tempuri.org/mConsultaLoteRPS', $soapClient->lastSoapAction);
    }

    public function testConsultarSequenciaLoteNotaRPSEnvioExtractsDigitsFromCrlfSeparatedResponse(): void
    {
        $soapClient = new FakeSoapClient("abc123\r\ndef456\r\nghi789");
        $service = new NfseService($this->config(), soapClient: $soapClient);

        $result = $service->consultarSequenciaLoteNotaRPSEnvio('00000000000000', 'Razao Social', '00000000');

        $this->assertSame(['123', '456', '789'], $result);
    }

    public function testConsultarSequenciaLoteNotaRPSEnvioDoesNotCrashOnInvalidUtf8Byte(): void
    {
        // Regressao: antes do fix (JSON_INVALID_UTF8_SUBSTITUTE) um byte invalido em UTF-8
        // fazia json_encode() devolver false, e o explode() seguinte lancava TypeError
        // sob strict_types.
        $soapClient = new FakeSoapClient("linha1\r\n\xE3\r\nlinha2");
        $service = new NfseService($this->config(), soapClient: $soapClient);

        $result = $service->consultarSequenciaLoteNotaRPSEnvio('00000000000000', 'Razao Social', '00000000');

        $this->assertIsArray($result);
    }

    public function testListUltimaNotaDelegatesToRepository(): void
    {
        $repository = new FakeNfseRepository();
        $repository->ultimaNota = [['id' => 1]];
        $service = new NfseService($this->config(), repository: $repository);

        $this->assertSame([['id' => 1]], $service->listUltimaNota());
    }

    public function testListNotasDelegatesToRepository(): void
    {
        $repository = new FakeNfseRepository();
        $repository->notas = [['id' => 2]];
        $service = new NfseService($this->config(), repository: $repository);

        $this->assertSame([['id' => 2]], $service->listNotas());
    }

    public function testCadastrarNfseBancoDelegatesToRepositoryWithSameArguments(): void
    {
        $repository = new FakeNfseRepository();
        $service = new NfseService($this->config(), repository: $repository);

        $service->cadastrarNfseBanco('1', '2', '3', 'PROTOCOLO', 'http://link', 'VERIFICACAO');

        $this->assertSame(
            ['1', '2', '3', 'PROTOCOLO', 'http://link', 'VERIFICACAO'],
            $repository->cadastradas[0]
        );
    }

    public function testDeletaNfseBancoDelegatesToRepositoryWithSameArgument(): void
    {
        $repository = new FakeNfseRepository();
        $service = new NfseService($this->config(), repository: $repository);

        $service->deletaNfseBanco('42');

        $this->assertSame(['42'], $repository->deletadas);
    }
}

final class FakeSoapClient implements SoapClientInterface
{
    public ?string $lastUrl = null;
    public ?string $lastSoapAction = null;
    public ?string $lastEnvelope = null;

    public function __construct(private readonly string $response)
    {
    }

    public function post(string $url, string $soapAction, string $envelope, int $timeoutSeconds): string
    {
        $this->lastUrl = $url;
        $this->lastSoapAction = $soapAction;
        $this->lastEnvelope = $envelope;

        return $this->response;
    }
}

final class FakeNfseRepository implements NfseRepositoryInterface
{
    public array $ultimaNota = [];
    public array $notas = [];
    public array $cadastradas = [];
    public array $deletadas = [];

    public function listUltimaNota(): array
    {
        return $this->ultimaNota;
    }

    public function listNotas(): array
    {
        return $this->notas;
    }

    public function cadastrarNfseBanco(
        $numeronota,
        $numerolote,
        $numerorps,
        $protocolo,
        $linknota,
        $codigoverificacao
    ): void {
        $this->cadastradas[] = [$numeronota, $numerolote, $numerorps, $protocolo, $linknota, $codigoverificacao];
    }

    public function deletaNfseBanco($cod): void
    {
        $this->deletadas[] = $cod;
    }
}

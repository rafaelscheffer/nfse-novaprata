<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Tests\Unit\Infrastructure\Xml;

use DOMDocument;
use NovaPrata\Nfse\Infrastructure\Xml\RpsLoteXmlBuilder;
use PHPUnit\Framework\TestCase;

final class RpsLoteXmlBuilderTest extends TestCase
{
    private function build(array $overrides = []): DOMDocument
    {
        $args = array_merge([
            'NumeroLote' => '5',
            'NumeroRPS' => '10',
            'Cnpj' => '66530585000160',
            'InscricaoMunicipal' => '12345',
            'RazaoSocial' => 'Empresa Ltda',
            'Valorservico' => '100.00',
            'opcao' => 'CPF',
            'Cnpjcpf' => '06804127942',
            'Endereco' => 'RUA X',
            'Numero' => '700',
            'Bairro' => 'CENTRO',
            'Cepcliente' => '95560000',
            'CodigoMunicipioCliente' => '4313300',
            'Telefone' => '5136263636',
            'Email' => 'cliente@teste.com',
            'CodigoCnae' => '6203100',
            'Aliquota' => 3,
            'Descricao' => 'descricao do trabalho',
            'Nome' => 'Cliente Teste',
            'NumeroParcelas' => 0,
            'CodigoMunicipioEmpresa' => '4313300',
            'UFCliente' => 'RS',
            'data' => '2026-08-07T10:00:00',
            'ano' => '2026',
        ], $overrides);

        $xml = (new RpsLoteXmlBuilder())->build(...array_values($args));

        $doc = new DOMDocument();
        $doc->loadXML($xml);

        return $doc;
    }

    private function nodeValue(DOMDocument $doc, string $tag): ?string
    {
        $node = $doc->getElementsByTagName($tag)->item(0);

        return $node?->nodeValue;
    }

    public function testLoteRpsIdUsesUnpaddedYearAndZeroPaddedSequential(): void
    {
        $doc = $this->build(['NumeroLote' => '5', 'ano' => '2026']);

        $loteRps = $doc->getElementsByTagName('LoteRps')->item(0);

        $this->assertSame('L1' . '2026' . str_pad('5', 16, '0', STR_PAD_LEFT), $loteRps->getAttribute('Id'));
    }

    public function testNumeroLoteElementKeepsRawUnpaddedValue(): void
    {
        $doc = $this->build(['NumeroLote' => '5']);

        $this->assertSame('5', $this->nodeValue($doc, 'NumeroLote'));
    }

    public function testValorIssIsThreePercentWhenAliquotaIsThree(): void
    {
        $doc = $this->build(['Valorservico' => '100.00', 'Aliquota' => 3]);

        $this->assertSame('100.00', $this->nodeValue($doc, 'ValorServicos'));
        $this->assertSame('3.00', $this->nodeValue($doc, 'ValorIss'));
    }

    public function testValorIssIsThreePointFivePercentWhenAliquotaIsNotThree(): void
    {
        $doc = $this->build(['Valorservico' => '100.00', 'Aliquota' => 5]);

        $this->assertSame('3.50', $this->nodeValue($doc, 'ValorIss'));
    }

    public function testCpfOptionUsesCpfTagWithEmptyTomadorRegistrations(): void
    {
        $doc = $this->build(['opcao' => 'CPF', 'Cnpjcpf' => '06804127942']);

        // Prestador sempre tem 2 elementos Cnpj (nivel LoteRps e nivel InfDeclaracaoPrestacaoServico);
        // o Intermediario sempre tem um Cpf vazio; com opcao=CPF, o Tomador soma outro Cpf.
        $this->assertSame(2, $doc->getElementsByTagName('Cnpj')->length);
        $this->assertSame(2, $doc->getElementsByTagName('Cpf')->length);
        $this->assertSame('06804127942', $this->nodeValue($doc, 'Cpf'));
        $this->assertSame('', $this->nodeValue($doc, 'InscricaoEstadual'));
    }

    public function testCnpjOptionUsesCnpjTagWithFixedTomadorRegistrations(): void
    {
        $doc = $this->build(['opcao' => 'CNPJ', 'Cnpjcpf' => '66530585000160']);

        // Com opcao != CPF, o Tomador tambem usa Cnpj: soma-se ao 2 do Prestador, total 3.
        // Sobra 1 Cpf: o do Intermediario, sempre vazio independente da opcao.
        $this->assertSame(3, $doc->getElementsByTagName('Cnpj')->length);
        $this->assertSame(1, $doc->getElementsByTagName('Cpf')->length);
        $this->assertSame('333333333', $this->nodeValue($doc, 'InscricaoEstadual'));
    }

    public function testTelefoneAndEmailAreEmptyElementsWhenBlank(): void
    {
        $doc = $this->build(['Telefone' => '', 'Email' => '']);

        $this->assertSame('', $this->nodeValue($doc, 'Telefone'));
        $this->assertSame('', $this->nodeValue($doc, 'Email'));
    }

    public function testTelefoneAndEmailKeepValueWhenProvided(): void
    {
        $doc = $this->build(['Telefone' => '5136263636', 'Email' => 'cliente@teste.com']);

        $this->assertSame('5136263636', $this->nodeValue($doc, 'Telefone'));
        $this->assertSame('cliente@teste.com', $this->nodeValue($doc, 'Email'));
    }
}

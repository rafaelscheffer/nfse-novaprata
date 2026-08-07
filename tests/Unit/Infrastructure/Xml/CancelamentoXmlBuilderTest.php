<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Tests\Unit\Infrastructure\Xml;

use DOMDocument;
use NovaPrata\Nfse\Infrastructure\Xml\CancelamentoXmlBuilder;
use PHPUnit\Framework\TestCase;

final class CancelamentoXmlBuilderTest extends TestCase
{
    private function build(string $numeroNota, string $cnpj, string $inscricao, string $codigoMunicipio): DOMDocument
    {
        $xml = (new CancelamentoXmlBuilder())->build($numeroNota, $cnpj, $inscricao, $codigoMunicipio);

        $doc = new DOMDocument();
        $doc->loadXML($xml);

        return $doc;
    }

    private function nodeValue(DOMDocument $doc, string $tag): ?string
    {
        return $doc->getElementsByTagName($tag)->item(0)?->nodeValue;
    }

    public function testInfPedidoCancelamentoIdConcatenatesPrefixCnpjAndZeroPaddedNumero(): void
    {
        $doc = $this->build('42', '66530585000160', '12345', '4313300');

        $infPedido = $doc->getElementsByTagName('InfPedidoCancelamento')->item(0);

        $this->assertSame('2' . '66530585000160' . str_pad('42', 16, '0', STR_PAD_LEFT), $infPedido->getAttribute('Id'));
    }

    public function testIdentificacaoNfseFieldsMatchInputs(): void
    {
        $doc = $this->build('42', '66530585000160', '12345', '4313300');

        $this->assertSame('42', $this->nodeValue($doc, 'Numero'));
        $this->assertSame('66530585000160', $this->nodeValue($doc, 'Cnpj'));
        $this->assertSame('12345', $this->nodeValue($doc, 'InscricaoMunicipal'));
        $this->assertSame('4313300', $this->nodeValue($doc, 'CodigoMunicipio'));
    }

    public function testCodigoCancelamentoIsAlwaysOne(): void
    {
        $doc = $this->build('42', '66530585000160', '12345', '4313300');

        $this->assertSame('1', $this->nodeValue($doc, 'CodigoCancelamento'));
    }
}

<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Tests\Unit\Infrastructure\Xml;

use NovaPrata\Nfse\Infrastructure\Xml\ConsultaXmlBuilder;
use PHPUnit\Framework\TestCase;

final class ConsultaXmlBuilderTest extends TestCase
{
    public function testBuildConsultaLoteEnvelopeEmbedsAllInputs(): void
    {
        $xml = (new ConsultaXmlBuilder())->buildConsultaLoteEnvelope(
            'Empresa Ltda',
            '66530585000160',
            '12345',
            '66530585000160000000005'
        );

        $this->assertStringContainsString('<mConsultaLoteRPS', $xml);
        $this->assertStringContainsString('<Cnpj>66530585000160</Cnpj>', $xml);
        $this->assertStringContainsString('<RazaoSocial>Empresa Ltda</RazaoSocial>', $xml);
        $this->assertStringContainsString('<InscricaoMunicipal>12345</InscricaoMunicipal>', $xml);
        $this->assertStringContainsString('<Protocolo>66530585000160000000005</Protocolo>', $xml);
    }

    public function testBuildConsultaLoteEnvelopeStripsNewlinesAndCollapsesSpaces(): void
    {
        $xml = (new ConsultaXmlBuilder())->buildConsultaLoteEnvelope('Empresa', '123', '456', '789');

        $this->assertStringNotContainsString("\n", $xml);
        $this->assertStringNotContainsString('> <', $xml);
    }

    public function testBuildConsultaSequenciaEnvelopeEmbedsPrestadorData(): void
    {
        $xml = (new ConsultaXmlBuilder())->buildConsultaSequenciaEnvelope(
            '66530585000160',
            'Empresa Ltda',
            '12345'
        );

        $this->assertStringContainsString('<mConsultaSequenciaLoteNotaRPS', $xml);
        $this->assertStringContainsString('<Cnpj>66530585000160</Cnpj>', $xml);
        $this->assertStringContainsString('<RazaoSocial>Empresa Ltda</RazaoSocial>', $xml);
        $this->assertStringContainsString('<InscricaoMunicipal>12345</InscricaoMunicipal>', $xml);
    }
}

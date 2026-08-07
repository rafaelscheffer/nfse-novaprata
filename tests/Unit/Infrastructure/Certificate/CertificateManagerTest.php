<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Tests\Unit\Infrastructure\Certificate;

use NovaPrata\Nfse\Exceptions\NfseException;
use NovaPrata\Nfse\Infrastructure\Certificate\CertificateManager;
use PHPUnit\Framework\TestCase;

final class CertificateManagerTest extends TestCase
{
    public function testLoadThrowsWhenNoCertificateNameWasConfigured(): void
    {
        $manager = new CertificateManager();

        try {
            $manager->load('', '', '', '');
            $this->fail('Esperava NfseException por falta de certificado configurado.');
        } catch (NfseException $exception) {
            $this->assertSame(NfseException::STOP_CRITICAL, $exception->getCode());
            $this->assertStringContainsString('certificado deve ser passado', $exception->getMessage());
        }
    }

    public function testLoadThrowsWhenPfxFileDoesNotExist(): void
    {
        $manager = new CertificateManager();

        try {
            $manager->load('senha', '4313300', 'certificado-inexistente.pfx', sys_get_temp_dir());
            $this->fail('Esperava NfseException por arquivo de certificado inexistente.');
        } catch (NfseException $exception) {
            $this->assertSame(NfseException::STOP_CRITICAL, $exception->getCode());
            $this->assertStringContainsString('não encontrado', $exception->getMessage());
        }
    }
}

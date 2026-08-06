<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Infrastructure\Certificate;

use NovaPrata\Nfse\Exceptions\NfseException;

final class CertificateManager
{
    private string $senhaPfx = '';
    private string $certName = '';
    private string $pastaCert = '';
    private string $priKEY = '';
    private string $pubKEY = '';
    private string $certKEY = '';
    private int $pfxTimestamp = 0;

    /**
     * Carrega (ou recria a partir do .pfx) os arquivos PEM usados para assinar o XML.
     */
    public function load(
        string $senhaPfxNfse,
        string $codigoMunicipioIBGE,
        string $certificado,
        string $pastacertificado
    ): bool {
        if ($senhaPfxNfse && $codigoMunicipioIBGE) {
            $this->certName = $certificado;
            $this->senhaPfx = $senhaPfxNfse;
            $this->pastaCert = $pastacertificado . DIRECTORY_SEPARATOR;
        }

        return $this->loadCerts();
    }

    public function getPrivateKeyPath(): string
    {
        return $this->priKEY;
    }

    /**
     * Retorna o certificado publico "limpo" (sem as tags BEGIN/END e sem quebras de linha),
     * no formato exigido dentro da tag X509Certificate da assinatura.
     */
    public function getCleanCertificate(): string
    {
        return $this->cleanCert($this->pubKEY);
    }

    private function loadCerts(bool $testaVal = true): bool
    {
        $msg = "Erro no carregamento dos certificados.<br/>";
        if (!function_exists('openssl_pkcs12_read')) {
            $msg .= "Função não existente: openssl_pkcs12_read!!";
            throw new NfseException($msg, NfseException::STOP_CRITICAL);
        }
        //monta o path completo com o nome da chave privada
        $this->priKEY = $this->pastaCert . 'priKEY.pem';
        //monta o path completo com o nome da chave prublica
        $this->pubKEY = $this->pastaCert . 'pubKEY.pem';
        //monta o path completo com o nome do certificado (chave publica e privada) em formato pem
        $this->certKEY = $this->pastaCert . 'certKEY.pem';
        //verificar se o nome do certificado e
        //o path foram carregados nas variaveis da classe
        if ($this->certName == '') {
            $msg .= "Um certificado deve ser passado para a classe pelo arquivo de configuração!";
            throw new NfseException($msg, NfseException::STOP_CRITICAL);
        }
        //monta o caminho completo ate o certificado pfx
        $pfxCert = $this->pastaCert . $this->certName;
        //verifica se o arquivo existe
        if (!file_exists($pfxCert)) {
            $msg .= "Arquivo do Certificado não encontrado! $pfxCert";
            throw new NfseException($msg, NfseException::STOP_CRITICAL);
        }
        //carrega o certificado em um string
        $pfxContent = file_get_contents($pfxCert);
        //carrega os certificados e chaves para um array denominado $x509certdata
        if (!openssl_pkcs12_read($pfxContent, $x509certdata, $this->senhaPfx)) {
            $msg .= "O certificado não pode ser lido. Pode estar corrompido ou a senha cadastrada está errada!";
            throw new NfseException($msg, NfseException::STOP_CRITICAL);
        }
        //Verifica se o certificado é válido
        if ($testaVal) {
            $this->validCert($x509certdata['cert']);
        }
        //aqui verifica se existem as chaves em formato PEM
        //se existirem pega a data da validade dos arquivos PEM
        //e compara com a data de validade do PFX
        //caso a data de validade do PFX for maior que a data do PEM
        //deleta dos arquivos PEM, recria e prossegue
        $flagNovo = false;
        if (file_exists($this->pubKEY)) {
            $cert = file_get_contents($this->pubKEY);
            if (!$data = openssl_x509_read($cert)) {
                //arquivo não pode ser lido como um certificado
                //entao deletar
                $flagNovo = true;
            } else {
                //pegar a data de validade do mesmo
                $cert_data = openssl_x509_parse($data);
                // reformata a data de validade;
                $ano = substr($cert_data['validTo'], 0, 2);
                $mes = substr($cert_data['validTo'], 2, 2);
                $dia = substr($cert_data['validTo'], 4, 2);
                //obtem o timeestamp da data de validade do certificado
                $dValPubKey = gmmktime(0, 0, 0, (int) $mes, (int) $dia, (int) $ano);
                //compara esse timestamp com o do pfx que foi carregado
                if ($testaVal) {
                    //$this->pfxTimestamp setada em validCert()
                    if ($dValPubKey < $this->pfxTimestamp) {
                        //o arquivo PEM eh de um certificado anterior
                        //entao apagar os arquivos PEM
                        $flagNovo = true;
                    }
                }
            }
        } else {
            //arquivo não localizado
            $flagNovo = true;
        }
        //verificar a chave privada em PEM
        if (!file_exists($this->priKEY)) {
            //arquivo nao encontrado
            $flagNovo = true;
        }
        //verificar o certificado em PEM
        if (!file_exists($this->certKEY)) {
            //arquivo não encontrado
            $flagNovo = true;
        }
        //criar novos arquivos PEM
        if ($flagNovo) {
            if (file_exists($this->pubKEY)) {
                unlink($this->pubKEY);
            }
            if (file_exists($this->priKEY)) {
                unlink($this->priKEY);
            }
            if (file_exists($this->certKEY)) {
                unlink($this->certKEY);
            }
            //recriar os arquivos pem com o arquivo pfx
            if (!file_put_contents($this->priKEY, $x509certdata['pkey'])) {
                $msg .= "Impossivel gravar no diretório! Permissão negada!";
                throw new NfseException($msg, NfseException::STOP_CRITICAL);
            }
            file_put_contents($this->pubKEY, $x509certdata['cert']);
            file_put_contents($this->certKEY, $x509certdata['pkey'] . "\r\n" . $x509certdata['cert']);
        }

        return true;
    }

    private function validCert(string $cert = ''): bool
    {
        $msg = "Erro no carregamento dos certificados.<br/>";
        if ($cert == '') {
            $msg .= "O certificado é um parâmetro obrigatorio.";
            throw new NfseException($msg, NfseException::STOP_CRITICAL);
        }
        if (!$data = openssl_x509_read($cert)) {
            $msg .= "O certificado não pode ser lido pelo SSL - $cert .";
            throw new NfseException($msg, NfseException::STOP_CRITICAL);
        }
        $cert_data = openssl_x509_parse($data);
        // reformata a data de validade;
        $ano = substr($cert_data['validTo'], 0, 2);
        $mes = substr($cert_data['validTo'], 2, 2);
        $dia = substr($cert_data['validTo'], 4, 2);
        //obtem o timestamp da data de validade do certificado
        $dValid = gmmktime(0, 0, 0, (int) $mes, (int) $dia, (int) $ano);
        // obtem o timestamp da data de hoje
        $dHoje = gmmktime(0, 0, 0, (int) date("m"), (int) date("d"), (int) date("Y"));
        // compara a data de validade com a data atual
        if ($dValid < $dHoje) {
            $errorMsg = "A Validade do certificado expirou em " . $dia . '/' . $mes . '/' . $ano . "";
            //alert para validade ultrapassada
            throw new NfseException($errorMsg, NfseException::WARNING_MESSAGE);
        }
        $this->pfxTimestamp = $dValid;

        return true;
    }

    private function cleanCert(string $certFile): string
    {
        $data = '';
        //carregar a chave publica do arquivo pem
        if (!$pubKey = file_get_contents($certFile)) {
            $msg = "Arquivo não encontrado - $certFile .";
            throw new NfseException($msg, NfseException::STOP_CRITICAL);
        }
        //carrega o certificado em um array usando o LF como referencia
        $arCert = explode("\n", $pubKey);
        foreach ($arCert as $curData) {
            //remove a tag de inicio e fim do certificado
            if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 && strncmp($curData, '-----END CERTIFICATE', 20) != 0) {
                //carrega o resultado numa string
                $data .= trim($curData);
            }
        }

        return $data;
    }
}

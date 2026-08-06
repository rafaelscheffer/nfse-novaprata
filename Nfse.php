<?php
require_once __DIR__ . '/vendor/autoload.php';

use NovaPrata\Nfse\Application\Services\NfseService;
use NovaPrata\Nfse\Config\Config;

/**
 * Fachada com a API publica original da classe, mantida para compatibilidade
 * com os scripts da raiz (envio.php, salvar.php, cancelarnota.php, deletarnota.php,
 * consultalote.php, index.php). Toda a implementacao vive em src/, sob o namespace
 * NovaPrata\Nfse (ver src/Application/Services/NfseService.php).
 */
class Nfse
{
    private readonly NfseService $service;

    public function __construct(?Config $config = null)
    {
        $this->service = new NfseService($config ?? Config::fromEnvironment());
    }

    public function criarNfse(
        $NumeroNota,
        $NumeroLote,
        $NumeroRPS,
        $Cnpj,
        $InscricaoMunicipal,
        $RazaoSocial,
        $Valorservico,
        $opcao,
        $Cnpjcpf,
        $Endereco,
        $Numero,
        $Bairro,
        $Cepcliente,
        $CodigoMunicipioCliente,
        $Telefone,
        $Email,
        $TipoNota,
        $CodigoCnae,
        $Aliquota,
        $Descricao,
        $Nome,
        $FormaPagamento,
        $NumeroParcelas,
        $CodigoMunicipioEmpresa,
        $UFCliente,
        $data,
        $ano,
        $pastacertificado,
        $pfxCertPrivado,
        $cert_password
    ) {
        return $this->service->criarNfse(
            $NumeroNota,
            $NumeroLote,
            $NumeroRPS,
            $Cnpj,
            $InscricaoMunicipal,
            $RazaoSocial,
            $Valorservico,
            $opcao,
            $Cnpjcpf,
            $Endereco,
            $Numero,
            $Bairro,
            $Cepcliente,
            $CodigoMunicipioCliente,
            $Telefone,
            $Email,
            $TipoNota,
            $CodigoCnae,
            $Aliquota,
            $Descricao,
            $Nome,
            $FormaPagamento,
            $NumeroParcelas,
            $CodigoMunicipioEmpresa,
            $UFCliente,
            $data,
            $ano,
            $pastacertificado,
            $pfxCertPrivado,
            $cert_password
        );
    }

    public function cancelarNfse(
        $NumeroNota,
        $Cnpj,
        $InscricaoMunicipal,
        $CodigoMunicipio,
        $cert_password,
        $pfxCertPrivado,
        $pastacertificado
    ) {
        return $this->service->cancelarNfse(
            $NumeroNota,
            $Cnpj,
            $InscricaoMunicipal,
            $CodigoMunicipio,
            $cert_password,
            $pfxCertPrivado,
            $pastacertificado
        );
    }

    public function consultalote($razao, $cnpj, $inscricao, $protocolo)
    {
        return $this->service->consultalote($razao, $cnpj, $inscricao, $protocolo);
    }

    public function ConsultarSequenciaLoteNotaRPSEnvio($Cnpj, $RazaoSocial, $InscricaoMunicipal)
    {
        return $this->service->consultarSequenciaLoteNotaRPSEnvio($Cnpj, $RazaoSocial, $InscricaoMunicipal);
    }

    public function geraCodigoIBGE($cep)
    {
        return $this->service->geraCodigoIBGE($cep);
    }

    public function listUltimaNota()
    {
        return $this->service->listUltimaNota();
    }

    public function listNotas()
    {
        return $this->service->listNotas();
    }

    public function cadastrarNfseBanco($numeronota, $numerolote, $numerorps, $protocolo, $linknota, $codigoverificacao)
    {
        return $this->service->cadastrarNfseBanco($numeronota, $numerolote, $numerorps, $protocolo, $linknota, $codigoverificacao);
    }

    public function deletaNfseBanco($cod)
    {
        return $this->service->deletaNfseBanco($cod);
    }
}

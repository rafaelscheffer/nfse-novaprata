<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Application\Services;

use DOMDocument;
use NovaPrata\Nfse\Config\Config;
use NovaPrata\Nfse\Contracts\CertificateManagerInterface;
use NovaPrata\Nfse\Contracts\NfseRepositoryInterface;
use NovaPrata\Nfse\Contracts\SoapClientInterface;
use NovaPrata\Nfse\Helpers\IbgeCodeResolver;
use NovaPrata\Nfse\Infrastructure\Certificate\CertificateManager;
use NovaPrata\Nfse\Infrastructure\Http\SoapClient;
use NovaPrata\Nfse\Infrastructure\Xml\CancelamentoXmlBuilder;
use NovaPrata\Nfse\Infrastructure\Xml\ConsultaXmlBuilder;
use NovaPrata\Nfse\Infrastructure\Xml\RpsLoteXmlBuilder;
use NovaPrata\Nfse\Infrastructure\Xml\XmlSigner;
use NovaPrata\Nfse\Repositories\NfseRepository;

/**
 * Orquestra a geracao/assinatura/envio de NFS-e, delegando para as classes de
 * infraestrutura (certificado, XML, SOAP) e para o repositorio de persistencia.
 */
final class NfseService
{
    private readonly CertificateManagerInterface $certificateManager;
    private readonly XmlSigner $xmlSigner;
    private readonly RpsLoteXmlBuilder $rpsLoteXmlBuilder;
    private readonly CancelamentoXmlBuilder $cancelamentoXmlBuilder;
    private readonly ConsultaXmlBuilder $consultaXmlBuilder;
    private readonly SoapClientInterface $soapClient;
    private readonly NfseRepositoryInterface $repository;
    private readonly IbgeCodeResolver $ibgeCodeResolver;

    public function __construct(
        private readonly Config $config,
        ?CertificateManagerInterface $certificateManager = null,
        ?SoapClientInterface $soapClient = null,
        ?NfseRepositoryInterface $repository = null,
    ) {
        $this->certificateManager = $certificateManager ?? new CertificateManager();
        $this->xmlSigner = new XmlSigner();
        $this->rpsLoteXmlBuilder = new RpsLoteXmlBuilder();
        $this->cancelamentoXmlBuilder = new CancelamentoXmlBuilder();
        $this->consultaXmlBuilder = new ConsultaXmlBuilder();
        $this->soapClient = $soapClient ?? new SoapClient();
        $this->repository = $repository ?? new NfseRepository($config);
        $this->ibgeCodeResolver = new IbgeCodeResolver();
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
    ): string {
        // pega dados certificado
        $this->certificateManager->load($cert_password, $CodigoMunicipioEmpresa, $pfxCertPrivado, $pastacertificado);

        $xml = $this->rpsLoteXmlBuilder->build(
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
            $CodigoCnae,
            $Aliquota,
            $Descricao,
            $Nome,
            $NumeroParcelas,
            $CodigoMunicipioEmpresa,
            $UFCliente,
            $data,
            $ano
        );

        header("Content-Type: text/xml");

        $signed = $this->xmlSigner->sign(
            $xml,
            RpsLoteXmlBuilder::TAG_ID,
            $this->certificateManager->getPrivateKeyPath(),
            $this->certificateManager->getCleanCertificate()
        );

        $envelope = $this->buildEnvioEnvelope($signed);

        return $this->soapClient->post(
            $this->config->nfseUrlEnvio(),
            'http://tempuri.org/mEnvioLoteRPSSincrono',
            $envelope,
            86400
        );
    }

    private function buildEnvioEnvelope(string $stringXML): string
    {
        $soap_msg = '<?xml version="1.0" encoding="utf-8"?>
 <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
    <mEnvioLoteRPSSincrono xmlns="http://tempuri.org/">
      <remessa>
  		<![CDATA[' . $stringXML . ']]>
	  </remessa>
	</mEnvioLoteRPSSincrono>
 </soap:Body>
</soap:Envelope>';

        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="UTF-8" standalone="no"?>', $soap_msg);
        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>', '', $soap_msg);
        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $soap_msg);
        $soap_msg = str_replace("\n", "", $soap_msg);
        $soap_msg = str_replace("  ", " ", $soap_msg);
        $soap_msg = str_replace("> <", "><", $soap_msg);

        return $soap_msg;
    }

    public function cancelarNfse(
        $NumeroNota,
        $Cnpj,
        $InscricaoMunicipal,
        $CodigoMunicipio,
        $cert_password,
        $pfxCertPrivado,
        $pastacertificado
    ): string {
        // pega dados certificado é obrigatorio para assinar nota
        $this->certificateManager->load($cert_password, $CodigoMunicipio, $pfxCertPrivado, $pastacertificado);

        $xml1 = $this->cancelamentoXmlBuilder->build($NumeroNota, $Cnpj, $InscricaoMunicipal, $CodigoMunicipio);

        header("Content-Type: text/xml");

        $signed = $this->xmlSigner->sign(
            $xml1,
            CancelamentoXmlBuilder::TAG_ID,
            $this->certificateManager->getPrivateKeyPath(),
            $this->certificateManager->getCleanCertificate()
        );

        $envelope = $this->buildCancelamentoEnvelope($signed);

        return $this->soapClient->post(
            $this->config->nfseUrlCancelar(),
            'http://tempuri.org/mCancelamentoNFSe',
            $envelope,
            10
        );
    }

    private function buildCancelamentoEnvelope(string $stringXML): string
    {
        $soap_msg = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<mCancelamentoNFSe xmlns="http://tempuri.org/">
			<remessa>
				<![CDATA[' . $stringXML . ']]>
		    </remessa>
	        <cabecalho>
		        <![CDATA[<cabecalho xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.abrasf.org.br/nfse.xsd" <versaoDados>20.01</versaoDados> </cabecalho>]]>
		    </cabecalho>
		</mCancelamentoNFSe>
	</soap:Body>
</soap:Envelope>';

        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="UTF-8" standalone="no"?>', $soap_msg);
        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>', '', $soap_msg);
        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $soap_msg);
        $soap_msg = str_replace("\n", "", $soap_msg);
        $soap_msg = str_replace("  ", " ", $soap_msg);
        $soap_msg = str_replace("> <", "><", $soap_msg);

        return $soap_msg;
    }

    public function consultalote($razao, $cnpj, $inscricao, $protocolo): array
    {
        $envelope = $this->consultaXmlBuilder->buildConsultaLoteEnvelope($razao, $cnpj, $inscricao, $protocolo);

        $retorno = $this->soapClient->post(
            $this->config->nfseUrlConsultaLote(),
            'http://tempuri.org/mConsultaLoteRPS',
            $envelope,
            86400
        );

        //pega os dados do array retornado pelo NuSoap
        $retorno = str_replace('&lt;', '<', $retorno);
        $retorno = str_replace('&gt;', '>', $retorno);
        $retorno = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $retorno);
        if ($retorno == '') {
            echo 'erro';
        }
        //tratar dados de retorno
        $doc = new DOMDocument(); //cria objeto DOM
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($retorno, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        // status do recebimento ou mensagem de erro
        $aRet['Situacao'] = $doc->getElementsByTagName('Situacao')->item(0)->nodeValue;
        $aRet['Numero'] = $doc->getElementsByTagName('Numero')->item(0)->nodeValue;
        $aRet['CodigoVerificacao'] = $doc->getElementsByTagName('CodigoVerificacao')->item(0)->nodeValue;
        $aRet['DataEmissao'] = $doc->getElementsByTagName('DataEmissao')->item(0)->nodeValue;
        $aRet['LinkNota'] = $doc->getElementsByTagName('LinkNota')->item(0)->nodeValue;

        return $aRet;
    }

    public function consultarSequenciaLoteNotaRPSEnvio($Cnpj, $RazaoSocial, $InscricaoMunicipal): array
    {
        $xml = $this->consultaXmlBuilder->buildConsultaSequenciaEnvelope($Cnpj, $RazaoSocial, $InscricaoMunicipal);

        $response = $this->soapClient->post(
            $this->config->nfseUrlConsulta(),
            'http://tempuri.org/mConsultaSequenciaLoteNotaRPS',
            $xml,
            10
        );

        // json_encode() escapes real CRLF bytes into the literal 4-char sequence "\r\n",
        // which is what explode() below splits on to isolate each line of the raw XML response.
        // JSON_INVALID_UTF8_SUBSTITUTE avoids json_encode() returning false (and explode()
        // throwing under strict_types) when the SOAP response isn't valid UTF-8.
        $x = json_encode($response, JSON_INVALID_UTF8_SUBSTITUTE) ?: '';
        $array = explode('\r\n', $x);

        return preg_replace("/[^0-9]/", "", $array);
    }

    public function geraCodigoIBGE($cep): string
    {
        return $this->ibgeCodeResolver->resolve((string) $cep);
    }

    public function listUltimaNota(): array
    {
        return $this->repository->listUltimaNota();
    }

    public function listNotas(): array
    {
        return $this->repository->listNotas();
    }

    public function cadastrarNfseBanco($numeronota, $numerolote, $numerorps, $protocolo, $linknota, $codigoverificacao): void
    {
        $this->repository->cadastrarNfseBanco($numeronota, $numerolote, $numerorps, $protocolo, $linknota, $codigoverificacao);
    }

    public function deletaNfseBanco($cod): void
    {
        $this->repository->deletaNfseBanco($cod);
    }
}

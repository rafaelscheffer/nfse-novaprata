<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Infrastructure\Xml;

/**
 * Monta os envelopes SOAP (ja prontos para envio, sem assinatura) das consultas
 * de lote de RPS e de sequencia de lote/nota/rps.
 */
final class ConsultaXmlBuilder
{
    public function buildConsultaLoteEnvelope($razao, $cnpj, $inscricao, $protocolo): string
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<mConsultaLoteRPS xmlns="http://tempuri.org/">
			<remessa>
				<![CDATA[<ConsultarLoteRpsEnvio xmlns="http://www.abrasf.org.br/nfse.xsd" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Prestador><CpfCnpj><Cnpj>' . $cnpj . '</Cnpj></CpfCnpj><RazaoSocial>' . $razao . '</RazaoSocial><InscricaoMunicipal>' . $inscricao . '</InscricaoMunicipal></Prestador><Protocolo>' . $protocolo . '</Protocolo></ConsultarLoteRpsEnvio>]]></remessa>
			<cabecalho>
				<![CDATA[<cabecalho xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.abrasf.org.br/nfse.xsd" <versaoDados>20.01</versaoDados> </cabecalho>]]></cabecalho>
		</mConsultaLoteRPS>
	</soap:Body>
</soap:Envelope>';

        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="UTF-8" standalone="no"?>', $xml);
        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>', '', $soap_msg);
        $soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $soap_msg);
        $soap_msg = str_replace("\n", "", $soap_msg);
        $soap_msg = str_replace("  ", " ", $soap_msg);
        $soap_msg = str_replace("> <", "><", $soap_msg);

        return $soap_msg;
    }

    public function buildConsultaSequenciaEnvelope($Cnpj, $RazaoSocial, $InscricaoMunicipal): string
    {
        return '<?xml version="1.0" encoding="utf-8"?>
 <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
    <mConsultaSequenciaLoteNotaRPS xmlns="http://tempuri.org/">
      <remessa>
  		<![CDATA[<ConsultarSequenciaLoteNotaRPSEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
          <Prestador>
            <CpfCnpj>
              <Cnpj>' . $Cnpj . '</Cnpj>
            </CpfCnpj>
            <RazaoSocial>' . $RazaoSocial . '</RazaoSocial>
            <InscricaoMunicipal>' . $InscricaoMunicipal . '</InscricaoMunicipal>
          </Prestador>
        </ConsultarSequenciaLoteNotaRPSEnvio>]]>
	  </remessa>
	</mConsultaSequenciaLoteNotaRPS>
 </soap:Body>
</soap:Envelope>';
    }
}

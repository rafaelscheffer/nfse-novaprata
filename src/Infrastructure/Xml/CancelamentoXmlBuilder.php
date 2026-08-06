<?php

namespace NovaPrata\Nfse\Infrastructure\Xml;

use DOMDocument;

/**
 * Monta o XML (ainda nao assinado) do CancelarNfseEnvio, no padrao ABRASF.
 */
final class CancelamentoXmlBuilder
{
    public const TAG_ID = 'InfPedidoCancelamento';

    public function build($NumeroNota, $Cnpj, $InscricaoMunicipal, $CodigoMunicipio): string
    {
        $identificador = 2;
        $NumeroNotaposicao = str_pad($NumeroNota, 16, '0', STR_PAD_LEFT);
        $chavenota = $identificador . $Cnpj . $NumeroNotaposicao;

        $dom = new DOMDocument("1.0", "utf-8");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $root = $dom->createElement("CancelarNfseEnvio");
        $root->setAttribute("xmlns", "http://www.abrasf.org.br/nfse.xsd");
        $root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $root->setAttribute("xmlns:xsd", "http://www.w3.org/2001/XMLSchema");

        $Pedido = $dom->createElement("Pedido");
        $InfPedidoCancelamento = $dom->createElement("InfPedidoCancelamento");
        $InfPedidoCancelamento->setAttribute("Id", $chavenota);
        $Pedido->appendChild($InfPedidoCancelamento);
        $IdentificacaoNfse = $dom->createElement("IdentificacaoNfse");
        $InfPedidoCancelamento->appendChild($IdentificacaoNfse);
        $numero = $dom->createElement("Numero", $NumeroNota);
        $CpfCnpjNovo = $dom->createElement("CpfCnpj");
        $PrestadorCnpj = $dom->createElement("Cnpj", $Cnpj);
        $CpfCnpjNovo->appendChild($PrestadorCnpj);
        $InscricaoM = $dom->createElement("InscricaoMunicipal", $InscricaoMunicipal);
        $CodigoMunicipioEl = $dom->createElement("CodigoMunicipio", $CodigoMunicipio);
        $CodigoCancelamento = $dom->createElement("CodigoCancelamento", "1");

        $IdentificacaoNfse->appendChild($numero);
        $IdentificacaoNfse->appendChild($CpfCnpjNovo);
        $IdentificacaoNfse->appendChild($InscricaoM);
        $IdentificacaoNfse->appendChild($CodigoMunicipioEl);
        $InfPedidoCancelamento->appendChild($CodigoCancelamento);
        $root->appendChild($Pedido);
        $dom->appendChild($root);
        $xml1 = $dom->saveXML();

        $xml1 = str_replace('<?xml version="1.0" encoding="utf-8"?>', '<?xml version="1.0" encoding="utf-8" standalone="no"?>', $xml1);
        $xml1 = str_replace('<?xml version="1.0" encoding="utf-8" standalone="no"?>', '', $xml1);
        $xml1 = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $xml1);
        $xml1 = str_replace("\n", "", $xml1);
        $xml1 = str_replace("  ", " ", $xml1);
        $xml1 = str_replace("  ", " ", $xml1);
        $xml1 = str_replace("  ", " ", $xml1);
        $xml1 = str_replace("  ", " ", $xml1);
        $xml1 = str_replace("  ", " ", $xml1);
        $xml1 = str_replace("> <", "><", $xml1);

        return $xml1;
    }
}

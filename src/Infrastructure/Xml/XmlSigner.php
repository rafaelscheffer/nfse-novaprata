<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Infrastructure\Xml;

use DOMDocument;

final class XmlSigner
{
    /**
     * Assina digitalmente o elemento identificado por $tagId dentro do XML informado,
     * usando a chave privada e o certificado publico (ja "limpo") fornecidos.
     */
    public function sign(string $sXML, string $tagId, string $privateKeyPath, string $cleanCertificate): string
    {
        $fp = fopen($privateKeyPath, "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);

        $order = ["\r\n", "\n", "\r", "\t"];
        $replace = '';
        $sXML = str_replace($order, $replace, $sXML);
        $sXML = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="UTF-8" standalone="no"?>', $sXML);
        $sXML = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>', '', $sXML);
        $sXML = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $sXML);
        $sXML = str_replace('<?xml version="1.0"?>', '', $sXML);
        $sXML = str_replace("\n", "", $sXML);
        $sXML = str_replace("  ", " ", $sXML);
        $sXML = str_replace("> <", "><", $sXML);

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false; //elimina espaços em branco
        $dom->formatOutput = false;
        $dom->loadXML($sXML, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

        $root = $dom->documentElement;
        $node = $dom->getElementsByTagName($tagId)->item(0);
        $Id = trim($node->getAttribute("Id"));

        $dados = $node->C14N(false, false, null, null);
        $dados = str_replace(' >', '>', $dados);
        $hashValue = hash('sha1', $dados, true);
        $digValue = base64_encode($hashValue);

        $Signature = $dom->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature');
        $root->appendChild($Signature);
        $SignedInfo = $dom->createElement('SignedInfo');
        $Signature->appendChild($SignedInfo);

        //Cannocalization
        $newNode = $dom->createElement('CanonicalizationMethod');
        $SignedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');

        //SignatureMethod
        $newNode1 = $dom->createElement('SignatureMethod');
        $SignedInfo->appendChild($newNode1);
        $newNode1->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');

        //Reference
        $Reference = $dom->createElement('Reference');
        $SignedInfo->appendChild($Reference);
        $Reference->setAttribute('URI', '#' . $Id);

        //Transforms
        $Transforms = $dom->createElement('Transforms');
        $Reference->appendChild($Transforms);

        //Transform
        $newNode2 = $dom->createElement('Transform');
        $Transforms->appendChild($newNode2);
        $newNode2->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');

        //Transform
        $newNode3 = $dom->createElement('Transform');
        $Transforms->appendChild($newNode3);
        $newNode3->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');

        //DigestMethod
        $newNode4 = $dom->createElement('DigestMethod');
        $Reference->appendChild($newNode4);
        $newNode4->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');

        //DigestValue
        $newNode5 = $dom->createElement('DigestValue', $digValue);
        $Reference->appendChild($newNode5);

        // extrai os dados a serem assinados para uma string
        $dadosn = $SignedInfo->C14N(false, false, null, null);

        //inicializa a variavel que vai receber a assinatura
        $signaturevar = '';

        openssl_sign($dadosn, $signaturevar, $pkeyid);

        //codifica assinatura para o padrao base64
        $signatureValueN = base64_encode($signaturevar);

        //SignatureValue
        $newNodeSignature = $dom->createElement('SignatureValue', $signatureValueN);
        $Signature->appendChild($newNodeSignature);

        //KeyInfo
        $KeyInfo = $dom->createElement('KeyInfo');
        $Signature->appendChild($KeyInfo);

        //X509Data
        $X509Data = $dom->createElement('X509Data');
        $KeyInfo->appendChild($X509Data);

        //X509Certificate
        $newNode = $dom->createElement('X509Certificate', $cleanCertificate);
        $X509Data->appendChild($newNode);

        //grava na string o objeto DOM
        $returnxml = $dom->saveXML();

        openssl_free_key($pkeyid);

        $returnxml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="UTF-8" standalone="no"?>', $returnxml);
        $returnxml = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>', '', $returnxml);
        $returnxml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $returnxml);
        $returnxml = str_replace('<?xml version="1.0"?>', '', $returnxml);
        $returnxml = str_replace("\n", "", $returnxml);
        $returnxml = str_replace("  ", " ", $returnxml);
        $returnxml = str_replace("> <", "><", $returnxml);

        //retorna o documento assinado
        return $returnxml;
    }
}

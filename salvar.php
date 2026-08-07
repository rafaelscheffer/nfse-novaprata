<?php
session_start();
require_once 'Nfse.php';
$config = \NovaPrata\Nfse\Config\Config::fromEnvironment();
$Criar = new Nfse($config);
$retorno = $_SESSION['retornoenvionovo'];
if($retorno != ''){
    $Cnpj = $config->providerCnpj();
    $InscricaoMunicipal = $config->providerInscricaoMunicipal();
    $RazaoSocial = $config->providerRazaoSocial();

    $retorno=str_replace('&lt;','<',$retorno);
    $retorno=str_replace('&gt;','>',$retorno);
    $retorno=str_replace('<?xml version="1.0" encoding="utf-8"?>','',$retorno);
    $doc = new DOMDocument(); //cria objeto DOM
    $doc->formatOutput = false;
    $doc->preserveWhiteSpace = false;
    $doc->loadXML($retorno,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
    $DataRecebimentor = $doc->getElementsByTagName('DataRecebimento')->item(0)->nodeValue;
    $Codigor = $doc->getElementsByTagName('Codigo')->item(0)->nodeValue;
    $Mensagemr = $doc->getElementsByTagName('Mensagem')->item(0)->nodeValue;
    $Correcaor = $doc->getElementsByTagName('Correcao')->item(0)->nodeValue;
    $NumeroLoter = $doc->getElementsByTagName('NumeroLote')->item(0)->nodeValue;
    $Protocolor = $doc->getElementsByTagName('Protocolo')->item(0)->nodeValue;	
    if($Protocolor != ''){
        $respn = $Criar->consultalote($RazaoSocial,$Cnpj,$InscricaoMunicipal,$Protocolor);  	
        $NumeroNotar = $respn['Numero'];
        $Linknota = $respn['LinkNota'];
        $CodigoVerificacao = $respn['CodigoVerificacao'];
        $Criar->cadastrarNfseBanco($NumeroNotar,$NumeroLoter,$NumeroLoter,$Protocolor,$Linknota,$CodigoVerificacao);
        header("Location: index.php");
    }
}
?>

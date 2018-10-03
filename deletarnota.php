<?php
 session_start();
 require_once 'Nfse.php';
 $Criar = new Nfse();
 $retorno = $_SESSION['resultadonfsecancela'];
 if($retorno != ''){
 $Cnpj = '15454962000198';
 $InscricaoMunicipal = '32106042';
 $CodigoMunicipioEmpresa = '4313300';
 $RazaoSocial = 'NegocieRS Classificados Ltda - ME';
  
 $retorno=str_replace('&lt;','<',$retorno);
 $retorno=str_replace('&gt;','>',$retorno);
 $retorno=str_replace('<?xml version="1.0" encoding="utf-8"?>','',$retorno);
 $xmlresp = utf8_encode($retorno);
 $doc = new DOMDocument(); //cria objeto DOM
 $doc->formatOutput = false;
 $doc->preserveWhiteSpace = false;
 $doc->loadXML($retorno,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
 $NumeroNota = $doc->getElementsByTagName('Numero')->item(0)->nodeValue;
  if($NumeroNota != ''){
   $Criar->deletaNfseBanco($NumeroNota);
   header("Location: index.php");
  }
 }
?>
<?php
  session_start();
  ini_set('memory_limit', '1024M');
  ini_set('max_execution_time', '3600');
  header("Content-type: text/html; charset=utf-8");
  require_once 'Nfse.php';
  $Criar = new Nfse();
  
  $RazaoSocial = 'NegocieRS Classificados Ltda - ME';
  $Cnpj = '15454962000198';
  $InscricaoMunicipal = '32106042';
  $protocolo = '15454962000198000000005';
  $consultalote = $Criar->consultalote($RazaoSocial, $Cnpj,$InscricaoMunicipal,$protocolo);  
  echo $Situacaolote = $consultalote['Situacao'].'<br>';
  // $Numero para consulta de nota
  echo $Numero = $consultalote['Numero'].'<br>';
  echo $Linknota = $consultalote['LinkNota'].'<br>';
  echo $CodigoVerificacao = $consultalote['CodigoVerificacao'].'<br>';
  
  /*codigo da situação do lote de RPS
  1-Não recebido
  2-Não processado
  3-Processado com erro
  4-Processado com sucesso*/
?>
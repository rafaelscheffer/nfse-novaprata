<?php
  session_start();
  ini_set('memory_limit', '1024M');
  ini_set('max_execution_time', '3600');
  header("Content-type: text/html; charset=utf-8");
  require_once 'Nfse.php';
  $Criar = new Nfse();
  
  $Cnpj = '15454962000198';
  $InscricaoMunicipal = '32106042';
  $NumeroNota = $_GET['nrNota'];
  $CodigoMunicipio = '4313300';
    
  // criar uma pasta no servidor com o nome de cert	
  $pastacertificado = "cert";
  $pfxCertPrivado = 'teste.pfx';
  $cert_password  = '1111';
  
  if($NumeroNota != ''){
   $cancelarnota = $Criar->cancelarNfse($NumeroNota, $Cnpj, $InscricaoMunicipal, $CodigoMunicipio, $cert_password, $pfxCertPrivado, $pastacertificado);  
   $_SESSION['resultadonfsecancela'] = $cancelarnota;
   header("Location: deletarnota.php");
  } else {
   echo 'Informe o numero da nota!';   
  }
?>

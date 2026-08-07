<?php
session_start();
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', '3600');
header("Content-type: text/html; charset=utf-8");
require_once 'Nfse.php';
$config = \NovaPrata\Nfse\Config\Config::fromEnvironment();
$Criar = new Nfse($config);

$Cnpj = $config->providerCnpj();
$InscricaoMunicipal = $config->providerInscricaoMunicipal();
$NumeroNota = $_GET['nrNota'];
$CodigoMunicipio = $config->providerCodigoMunicipio();

// certificado digital do prestador (via .env)
$pastacertificado = $config->certPath();
$pfxCertPrivado = $config->certFile();
$cert_password  = $config->certPassword();

if($NumeroNota != ''){
  $cancelarnota = $Criar->cancelarNfse($NumeroNota, $Cnpj, $InscricaoMunicipal, $CodigoMunicipio, $cert_password, $pfxCertPrivado, $pastacertificado);  
  $_SESSION['resultadonfsecancela'] = $cancelarnota;
  header("Location: deletarnota.php");
} else {
  echo 'Informe o numero da nota!';   
}
?>

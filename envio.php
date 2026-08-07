<?php
session_start();
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', '3600');
header("Content-type: text/html; charset=utf-8");
require_once 'Nfse.php';
$config = \NovaPrata\Nfse\Config\Config::fromEnvironment();
$Criar = new Nfse($config);

// DADOS DO PRESTADOR (via .env) //

/******** INFORMAÇÃO *********************/
// CNPJ ou CPF só numeros

date_default_timezone_set('America/Sao_Paulo');
$data = date("Y-m-d")."T".date("H:i:s"); // data do envio
$ano = date("Y"); // ano corrente do envio

$Cnpj = $config->providerCnpj();
$InscricaoMunicipal = $config->providerInscricaoMunicipal();
$CodigoMunicipioEmpresa = $config->providerCodigoMunicipio();
$RazaoSocial = $config->providerRazaoSocial();
$Valorservico = '110.00';

// DADOS DO CLIENTE //
$Nome = 'Teste Cliente';
$opcao = 'CPF';  // Se for cnpj colocar CNPJ 
$Cnpjcpf = '06804127942';
$Endereco = 'RUA OSVALDO ARANHA';
$Numero = '700';
$Bairro = 'CENTRO';
$Cepcliente = '95560000';
$Telefone = '5136263636';
$Email    = 'rafamscheffer@gmail.com';
$CodigoMunicipioCliente = $Criar->geraCodigoIBGE($Cepcliente);
$UFCliente = 'RS';

/*
$Pegadadosambienteproducao = $Criar->ConsultarSequenciaLoteNotaRPSEnvio($Cnpj, $RazaoSocial, $InscricaoMunicipal);
$NumeroNota = $Pegadadosambienteproducao[2] + 1;
$NumeroLote = $Pegadadosambienteproducao[3] + 1;
$NumeroRPS = $Pegadadosambienteproducao[4] + 1;

Esta função só funciona em ambiente de produção
*/


foreach($Criar->listUltimaNota() as $R);
$NumeroNota = $R['numeronota'] + 1;
$NumeroLote = $R['numerolote'] + 1;
$NumeroRPS = $R['numerorps'] + 1;
  
$Aliquota = 3; // aliquota 3 que 3% ou 3.5 que é 3,5%
$ItemListaServico = 1.01; // código de identificação do serviço conforme lei complementar 116
$Descricao = 'descrição do trabalho'; // tamanho do texto até de 2000 caracteres
$FormaPagamento = 1; // 1 - a vista / 2 - apresentação / 3 - a prazo / 4 - cartão de debito / 5 - cartão de credito
// Quantidade de parcelas se informado 3 ou 5 na forma de pagamento
$NumeroParcelas = 0;
$TipoNota = '1'; 
$CodigoCnae = '6203100'; // Código da atividade CNAE

// certificado digital do prestador (via .env)
$pastacertificado = $config->certPath();
$pfxCertPrivado = $config->certFile();
$cert_password  = $config->certPassword();


if($NumeroNota != '' and $NumeroLote != '' and $NumeroRPS != ''){
  $criado = $Criar->criarNfse($NumeroNota, $NumeroLote, $NumeroRPS, $Cnpj, $InscricaoMunicipal, $RazaoSocial, $Valorservico, $opcao, $Cnpjcpf, $Endereco, $Numero, $Bairro, $Cepcliente, $CodigoMunicipioCliente, $Telefone, $Email, $TipoNota, $CodigoCnae, $Aliquota, $Descricao, $Nome, $FormaPagamento, $NumeroParcelas, $CodigoMunicipioEmpresa, $UFCliente, $data, $ano, $pastacertificado, $pfxCertPrivado, $cert_password);
  $_SESSION['retornoenvionovo'] = $criado; 
  header("Location: salvar.php");   
}   
?>
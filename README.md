# nfse-novaprata
Classe em PHP para gerar nota fiscal de serviço da cidade de Nova Prata Rio Grande do Sul

Informações de urls da classe para envio, cancelamento, consulta lote, consulta nfse estão no arquivo help.txt 

O arquivo Nfse.php é a classe para gerar a nfse, o arquivo Dados.php é o arquivo para a conexao com o banco de dados.

Exemplo de como gerar a nota fiscal:

  require_once 'Nfse.php';
  $Criar = new Nfse();
  
  // DADOS DO PRESTADOR //
  
  /******** INFORMAÇÃO *********************/
  // CNPJ ou CPF só numeros
  
  date_default_timezone_set('America/Sao_Paulo');
  $data = date("Y-m-d")."T".date("H:i:s"); // data do envio
  $ano = date("Y"); // ano corrente do envio
  
  $Cnpj = '15454962000198';
  $InscricaoMunicipal = '32106042';
  $CodigoMunicipioEmpresa = '4313300';
  $RazaoSocial = 'NegocieRS Classificados Ltda - ME';
  $Valorservico = '110.00';
  
  // DADOS DO CLIENTE //
  $Nome = 'Rafael Machado Scheffer';
  $opcao = 'CPF';  // Se for cnpj colocar CNPJ 
  $Cnpjcpf = '06804127943';
  $Endereco = 'RUA OSVALDO ARANHA';
  $Numero = '700';
  $Bairro = 'CENTRO';
  $Cepcliente = '95560000';
  $Telefone = '5136263636';
  $Email    = 'email@gmail.com';
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
  
  // criar uma pasta no servidor com o nome de cert
  $pastacertificado = "cert";
  $pfxCertPrivado = 'teste.pfx';
  $cert_password  = '1111';
  
  
  if($NumeroNota != '' and $NumeroLote != '' and $NumeroRPS != ''){
    $criado = $Criar->criarNfse($NumeroNota, $NumeroLote, $NumeroRPS, $Cnpj, $InscricaoMunicipal, $RazaoSocial, $Valorservico, $opcao, $Cnpjcpf, $Endereco, $Numero, $Bairro, $Cepcliente, $CodigoMunicipioCliente, $Telefone, $Email, $TipoNota, $CodigoCnae, $Aliquota, $Descricao, $Nome, $FormaPagamento, $NumeroParcelas, $CodigoMunicipioEmpresa, $UFCliente, $data, $ano, $pastacertificado, $pfxCertPrivado, $cert_password);
	$_SESSION['retornoenvionovo'] = $criado; 
	header("Location: salvar.php");   
  } 


  Exemplo de como consultar lote
  
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
  
  Exemplo de como cancelar nota
  
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

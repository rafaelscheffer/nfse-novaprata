<?php
require_once 'Dados.php';
class Nfse {

public $nfsexml='';
public $arqtxt='';
public $errMsg='';
public $errStatus=false;
public $priKEY='';
public $pubKEY='';
public $certKEY='';
public $certName='';
public $pastacert='';
public $pfxTimestamp='';
public $urlenvio='http://homologaprata.nfse-tecnos.com.br:9091';
public $urlcancelar='http://homologaprata.nfse-tecnos.com.br:9098';
public $urlconsulta='http://novaprata.nfse-tecnos.com.br:9084';
public $consultalote='http://homologaprata.nfse-tecnos.com.br:9097';
 
public function criarNfse($NumeroNota, $NumeroLote, $NumeroRPS, $Cnpj, $InscricaoMunicipal, $RazaoSocial, $Valorservico, $opcao, $Cnpjcpf, $Endereco, $Numero, $Bairro, $Cepcliente, $CodigoMunicipioCliente, $Telefone, $Email, $TipoNota, $CodigoCnae, $Aliquota, $Descricao, $Nome, $FormaPagamento, $NumeroParcelas, $CodigoMunicipioEmpresa, $UFCliente, $data, $ano, $pastacertificado, $pfxCertPrivado, $cert_password){	 
// pega dados certificado
$this->baseCerts($cert_password, $CodigoMunicipioEmpresa, $pfxCertPrivado, $pastacertificado);

$identificador = 1;
$numerosequencial = str_pad($NumeroLote, 16, '0', STR_PAD_LEFT);
$chave = $identificador.$ano.$numerosequencial;

$identificadorprest = 1;
$numerosequencialprest = str_pad($NumeroRPS, 16, '0', STR_PAD_LEFT);
$chaveprestacao = $identificadorprest.$Cnpj.$numerosequencialprest;
$chaveprestacao2 = '#'.$chaveprestacao;

if($Aliquota == 3){
 $porc = 0.03;
} else {
 $porc = 0.035;	
}
$ValorIs = $Valorservico * $porc;
$ValorIss1 = number_format($ValorIs, 2, '.', '');

$ValorDeducoes1 = "0.00";
$ValorPis1 = "0.00";
$ValorCofins1 = "0.00";
$ValorInss1 = "0.00";
$ValorIr1 = "0.00";
$ValorCsll1 = "0.00";
$OutrasRetencoes1 = "0.00";
$DescontoIncondicionado1 = "0.00";
$DescontoCondicionado1 = "0.00";
$IrrfIndenizacao1 = "0.00";

$dom = new DOMDocument("1.0", "utf-8");
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$root = $dom->createElement("EnviarLoteRpsSincronoEnvio");
$root->setAttribute("xmlns", "http://www.abrasf.org.br/nfse.xsd");
$LoteRps = $dom->createElement("LoteRps");
$LoteRps->setAttribute("Id", 'L'.$chave);
$LoteRps->setAttribute("versao", "20.01");
$nrlote = $dom->createElement("NumeroLote", $NumeroLote);
$CpfCnpjnovo = $dom->createElement("CpfCnpj");
$PrestadorNovoCnpj = $dom->createElement("Cnpj", $Cnpj);
$CpfCnpjnovo->appendChild($PrestadorNovoCnpj);   
$Inscricao = $dom->createElement("InscricaoMunicipal", $InscricaoMunicipal);
$QuantidadeRps = $dom->createElement("QuantidadeRps", 1);
$ListaRps = $dom->createElement("ListaRps");
$Rps = $dom->createElement("Rps");
$ListaRps->appendChild($Rps);  
$tcDeclaracaoPrestacaoServico = $dom->createElement("tcDeclaracaoPrestacaoServico");
$Rps->appendChild($tcDeclaracaoPrestacaoServico);
$InfDeclaracaoPrestacaoServico = $dom->createElement("InfDeclaracaoPrestacaoServico");
$InfDeclaracaoPrestacaoServico->setAttribute("Id", $chaveprestacao);
$tcDeclaracaoPrestacaoServico->appendChild($InfDeclaracaoPrestacaoServico);
$Rps2 = $dom->createElement("Rps");
$InfDeclaracaoPrestacaoServico->appendChild($Rps2);
$IdentificacaoRps = $dom->createElement("IdentificacaoRps");
$Rps2->appendChild($IdentificacaoRps);
$NumeroRPSx = $dom->createElement("Numero", $NumeroRPS);
$Seriex = $dom->createElement("Serie", "UNICA");
$Tipox  = $dom->createElement("Tipo", "1");
$IdentificacaoRps->appendChild($NumeroRPSx);
$IdentificacaoRps->appendChild($Seriex);
$IdentificacaoRps->appendChild($Tipox);
$data2  = $dom->createElement("DataEmissao", $data);
$Rps2->appendChild($data2);
$Statusx  = $dom->createElement("Status", "1");
$Rps2->appendChild($Statusx);
$RpsSubstituido  = $dom->createElement("RpsSubstituido");
$Rps2->appendChild($RpsSubstituido);
$Numerosubs  = $dom->createElement("Numero");
$RpsSubstituido->appendChild($Numerosubs);
$Seriesubs  = $dom->createElement("Serie");
$RpsSubstituido->appendChild($Seriesubs);
$Tiposubs  = $dom->createElement("Tipo", "1");
$RpsSubstituido->appendChild($Tiposubs);
$SiglaUF = $dom->createElement("SiglaUF", "RS");
$IdCidade = $dom->createElement("IdCidade", $CodigoMunicipioEmpresa);
$Competencia = $dom->createElement("Competencia", $data);
$Servico = $dom->createElement("Servico");
$InfDeclaracaoPrestacaoServico->appendChild($SiglaUF);
$InfDeclaracaoPrestacaoServico->appendChild($IdCidade);
$InfDeclaracaoPrestacaoServico->appendChild($Competencia);
$InfDeclaracaoPrestacaoServico->appendChild($Servico);
$tcDadosServico = $dom->createElement("tcDadosServico");
$Servico->appendChild($tcDadosServico);
$Valores = $dom->createElement("Valores");
$tcDadosServico->appendChild($Valores);
$ValorServicos = $dom->createElement("ValorServicos", $Valorservico);
$ValorDeducoes = $dom->createElement("ValorDeducoes", $ValorDeducoes1);
$ValorPis = $dom->createElement("ValorPis", $ValorPis1);
$ValorCofins = $dom->createElement("ValorCofins", $ValorCofins1);
$ValorInss = $dom->createElement("ValorInss", $ValorInss1);
$ValorIr = $dom->createElement("ValorIr", $ValorIr1);
$ValorCsll = $dom->createElement("ValorCsll", $ValorCsll1);
$OutrasRetencoes = $dom->createElement("OutrasRetencoes", $OutrasRetencoes1);
$ValorIss = $dom->createElement("ValorIss", $ValorIss1);
$Aliquota1 = $dom->createElement("Aliquota", $Aliquota);
$DescontoIncondicionado = $dom->createElement("DescontoIncondicionado", $DescontoIncondicionado1);
$DescontoCondicionado = $dom->createElement("DescontoCondicionado", $DescontoCondicionado1);
$IrrfIndenizacao = $dom->createElement("IrrfIndenizacao", $IrrfIndenizacao1);
$Valores->appendChild($ValorServicos);
$Valores->appendChild($ValorDeducoes);
$Valores->appendChild($ValorPis);
$Valores->appendChild($ValorCofins);
$Valores->appendChild($ValorInss);
$Valores->appendChild($ValorIr);
$Valores->appendChild($ValorCsll);
$Valores->appendChild($OutrasRetencoes);
$Valores->appendChild($ValorIss);
$Valores->appendChild($Aliquota1);
$Valores->appendChild($DescontoIncondicionado);
$Valores->appendChild($DescontoCondicionado);
$Valores->appendChild($IrrfIndenizacao);
$IssRetidovar = '2'; // 2 para não e 1 para sim
$IssRetido = $dom->createElement("IssRetido", $IssRetidovar);
$ResponsavelRetencaovar = '1'; // 1 se issretidor for 2 senao 2 para tomador ou 3 para intermediário
$ResponsavelRetencao = $dom->createElement("ResponsavelRetencao", $ResponsavelRetencaovar);
$ItemListaServicovar = '1.01';
$ItemListaServico = $dom->createElement("ItemListaServico", $ItemListaServicovar);
$CodidoCnae1 = $dom->createElement("CodidoCnae", $CodigoCnae);
$CodigoTributacaoMunicipio = $dom->createElement("CodigoTributacaoMunicipio", "0");
$Discriminacao = $dom->createElement("Discriminacao", $Descricao);
$CodigoMunicipioNovo = $dom->createElement("CodigoMunicipio", $CodigoMunicipioEmpresa);
$CodigoPaisNovo = $dom->createElement("CodigoPais", "1058");
$ExigibilidadeISS = $dom->createElement("ExigibilidadeISS", "1");
$MunicipioIncidencia = $dom->createElement("MunicipioIncidencia", $CodigoMunicipioEmpresa);

$tcDadosServico->appendChild($IssRetido);
$tcDadosServico->appendChild($ResponsavelRetencao);
$tcDadosServico->appendChild($ItemListaServico);
$tcDadosServico->appendChild($CodidoCnae1);
$tcDadosServico->appendChild($CodigoTributacaoMunicipio);
$tcDadosServico->appendChild($Discriminacao);
$tcDadosServico->appendChild($CodigoMunicipioNovo);
$tcDadosServico->appendChild($CodigoPaisNovo);
$tcDadosServico->appendChild($ExigibilidadeISS);
$tcDadosServico->appendChild($MunicipioIncidencia);

$Prestador = $dom->createElement("Prestador");
$InfDeclaracaoPrestacaoServico->appendChild($Prestador);
$Cnpjpres = $dom->createElement("CpfCnpj");
$Prestador->appendChild($Cnpjpres);
$PrestadorCnpj2 = $dom->createElement("Cnpj", $Cnpj);
$Cnpjpres->appendChild($PrestadorCnpj2); 
$RazaoSocialPrestador = $dom->createElement("RazaoSocial", $RazaoSocial);
$Prestador->appendChild($RazaoSocialPrestador); 
$InscricaoMunicipalpres = $dom->createElement("InscricaoMunicipal", $InscricaoMunicipal);
$Prestador->appendChild($InscricaoMunicipalpres); 

$Tomador = $dom->createElement("Tomador");
$InfDeclaracaoPrestacaoServico->appendChild($Tomador);
$IdentificacaoTomador = $dom->createElement("IdentificacaoTomador");
$Tomador->appendChild($IdentificacaoTomador);
$CpfCnpjtomador = $dom->createElement("CpfCnpj");
$IdentificacaoTomador->appendChild($CpfCnpjtomador);
$TomadorCpf=$dom->createElement("Cpf", $Cnpjcpf);
$TomadorCnpj=$dom->createElement("Cnpj", $Cnpjcpf);
if ($opcao == 'CPF'){
  $CpfCnpjtomador->appendChild($TomadorCpf);
  $InscricaoMunicipalTomador = $dom->createElement("InscricaoMunicipal"); 
  $InscricaoEstadualTomador = $dom->createElement("InscricaoEstadual"); 
} else {
  $CpfCnpjtomador->appendChild($TomadorCnpj);
  $InscricaoMunicipalTomador = $dom->createElement("InscricaoMunicipal","333333333"); 
  $InscricaoEstadualTomador = $dom->createElement("InscricaoEstadual","333333333"); 
}    

$IdentificacaoTomador->appendChild($InscricaoMunicipalTomador);
$IdentificacaoTomador->appendChild($InscricaoEstadualTomador);
$RazaoSocialTomador = $dom->createElement("RazaoSocial", $Nome);
$Tomador->appendChild($RazaoSocialTomador);

$EnderecoTomador = $dom->createElement("Endereco");
$EEnderecoTomador = $dom->createElement("Endereco", $Endereco);
$NumeroTomador = $dom->createElement("Numero", $Numero);
$ComplementoTomador = $dom->createElement("Complemento");
$BairroTomador = $dom->createElement("Bairro", $Bairro);
$CodigoMunicipioTomador = $dom->createElement("CodigoMunicipio", $CodigoMunicipioCliente);
$UFTomador = $dom->createElement("Uf", $UFCliente);
$CodigoPaisTomador = $dom->createElement("CodigoPais", "1058");
$CepTomador = $dom->createElement("Cep", $Cepcliente);
$ContatoTomador = $dom->createElement("Contato");
if($Telefone != ''){
 $TelefoneTomador = $dom->createElement("Telefone", $Telefone);
} else {
 $TelefoneTomador = $dom->createElement("Telefone");	
}

if($Email != ''){
 $EmailTomador = $dom->createElement("Email", $Email);
} else {
 $EmailTomador = $dom->createElement("Email");
}

$Tomador->appendChild($EnderecoTomador);
$EnderecoTomador->appendChild($EEnderecoTomador);
$EnderecoTomador->appendChild($NumeroTomador);
$EnderecoTomador->appendChild($ComplementoTomador);
$EnderecoTomador->appendChild($BairroTomador);
$EnderecoTomador->appendChild($CodigoMunicipioTomador);
$EnderecoTomador->appendChild($UFTomador);
$EnderecoTomador->appendChild($CodigoPaisTomador);
$EnderecoTomador->appendChild($CepTomador);
$Tomador->appendChild($ContatoTomador);
$ContatoTomador->appendChild($TelefoneTomador);
$ContatoTomador->appendChild($EmailTomador);

$Intermediario = $dom->createElement("Intermediario");
$IdentificacaoIntermediario = $dom->createElement("IdentificacaoIntermediario");
$CpfCnpjInt = $dom->createElement("CpfCnpj");
$CpfInt = $dom->createElement("Cpf");
$InscricaoMunicipalInt = $dom->createElement("InscricaoMunicipal");
$RazaoSocialInt = $dom->createElement("RazaoSocial");

$InfDeclaracaoPrestacaoServico->appendChild($Intermediario);
$Intermediario->appendChild($IdentificacaoIntermediario);
$IdentificacaoIntermediario->appendChild($CpfCnpjInt);
$CpfCnpjInt->appendChild($CpfInt);
$IdentificacaoIntermediario->appendChild($InscricaoMunicipalInt);
$Intermediario->appendChild($RazaoSocialInt);

$Construcaocivil = $dom->createElement("ConstrucaoCivil");
$CodigoObra = $dom->createElement("CodigoObra");
$Art = $dom->createElement("Art");
$InfDeclaracaoPrestacaoServico->appendChild($Construcaocivil);
$Construcaocivil->appendChild($CodigoObra);
$Construcaocivil->appendChild($Art);

$RegimeEspecialTributacao = $dom->createElement("RegimeEspecialTributacao", "1");
$NaturezaOperacao = $dom->createElement("NaturezaOperacao", "1");
$OptanteSimplesNacional = $dom->createElement("OptanteSimplesNacional", "2");
$IncentivoFiscal = $dom->createElement("IncentivoFiscal", "2");
$PercentualCargaTributaria = $dom->createElement("PercentualCargaTributaria", "3");
$ValorCargaTributaria = $dom->createElement("ValorCargaTributaria", "30");
$PercentualCargaTributariaEstadual = $dom->createElement("PercentualCargaTributariaEstadual", "3");
$ValorCargaTributariaEstadual = $dom->createElement("ValorCargaTributariaEstadual", "30");
$PercentualCargaTributariaMunicipal = $dom->createElement("PercentualCargaTributariaMunicipal", "3");
$ValorCargaTributariaMunicipal = $dom->createElement("ValorCargaTributariaMunicipal", "30");
$SiglaUF = $dom->createElement("SiglaUF", "RS");
$IdCidade = $dom->createElement("IdCidade", $CodigoMunicipioEmpresa);
$NumeroParcelas = $dom->createElement("NumeroParcelas", "0");
$InfDeclaracaoPrestacaoServico->appendChild($RegimeEspecialTributacao);
$InfDeclaracaoPrestacaoServico->appendChild($NaturezaOperacao);
$InfDeclaracaoPrestacaoServico->appendChild($OptanteSimplesNacional);
$InfDeclaracaoPrestacaoServico->appendChild($IncentivoFiscal);
$InfDeclaracaoPrestacaoServico->appendChild($PercentualCargaTributaria);
$InfDeclaracaoPrestacaoServico->appendChild($ValorCargaTributaria);
$InfDeclaracaoPrestacaoServico->appendChild($PercentualCargaTributariaEstadual);
$InfDeclaracaoPrestacaoServico->appendChild($ValorCargaTributariaEstadual);
$InfDeclaracaoPrestacaoServico->appendChild($PercentualCargaTributariaMunicipal);
$InfDeclaracaoPrestacaoServico->appendChild($ValorCargaTributariaMunicipal);
$InfDeclaracaoPrestacaoServico->appendChild($SiglaUF);
$InfDeclaracaoPrestacaoServico->appendChild($IdCidade);
$InfDeclaracaoPrestacaoServico->appendChild($NumeroParcelas);

$LoteRps->appendChild($nrlote);
$LoteRps->appendChild($CpfCnpjnovo);
$LoteRps->appendChild($Inscricao);
$LoteRps->appendChild($QuantidadeRps);
$LoteRps->appendChild($ListaRps);
$root->appendChild($LoteRps);
$dom->appendChild($root);
header("Content-Type: text/xml");
$xml = $dom->saveXML(); 
$xml = str_replace('<?xml version="1.0" encoding="utf-8"?>','<?xml version="1.0" encoding="utf-8" standalone="no"?>',$xml);
$xml = str_replace('<?xml version="1.0" encoding="utf-8" standalone="no"?>','',$xml);
$xml = str_replace('<?xml version="1.0" encoding="utf-8"?>','',$xml);  
$xml = str_replace("\n","",$xml);
$xml = str_replace("  "," ",$xml);
$xml = str_replace("  "," ",$xml);
$xml = str_replace("  "," ",$xml);
$xml = str_replace("  "," ",$xml);
$xml = str_replace("  "," ",$xml);
$xml = str_replace("> <","><",$xml);
$tag = 'InfDeclaracaoPrestacaoServico';
$xml = html_entity_decode(stripslashes($xml),ENT_QUOTES,'UTF-8');
$assinado = $this->signXML($xml, $tag);
$stringXML = $assinado;
$soap_response = $this->sendSoapMessage($stringXML);
return $soap_response;
 
}


public function sendSoapMessage($stringXML){
 $soap_msg = '<?xml version="1.0" encoding="utf-8"?>
 <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
    <mEnvioLoteRPSSincrono xmlns="http://tempuri.org/">
      <remessa>
  		<![CDATA['.$stringXML.']]>
	  </remessa>
	</mEnvioLoteRPSSincrono>	
 </soap:Body>
</soap:Envelope>'; 

$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8" standalone="no"?>',$soap_msg);
$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>','',$soap_msg);
$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$soap_msg);
$soap_msg = str_replace("\n","",$soap_msg);
$soap_msg = str_replace("  "," ",$soap_msg);
$soap_msg = str_replace("> <","><",$soap_msg);
		
$headers = array(
 "Content-type: text/xml; charset=\"utf-8\"",
 "Accept: text/xml",
 "Cache-Control: no-cache",
 "Pragma: no-cache",
 "SOAPAction: http://tempuri.org/mEnvioLoteRPSSincrono", 
 "Content-length: ".strlen($soap_msg),
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $this->urlenvio);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 86400);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_msg);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
return $response;
}


public function baseCerts($senhaPfxNfse, $codigoMunicipioIBGE, $certificado, $pastacertificado){
        if ($senhaPfxNfse && $codigoMunicipioIBGE){
          $this->certName = $certificado;
		  $this->senhapfx = $senhaPfxNfse;
		  $this->pastacert = $pastacertificado. DIRECTORY_SEPARATOR;
        } 
        if($this->__loadCerts()){
            return true;
        }else{
            return false;
        }
} 
	

protected function __loadCerts($testaVal=true){
        $msg = "Erro no carregamento dos certificados.<br/>";
        if(!function_exists('openssl_pkcs12_read')){
            $msg .= "Função não existente: openssl_pkcs12_read!!";
            throw new nfsephpException($msg,self::STOP_CRITICAL);
            return false;
        }
        //monta o path completo com o nome da chave privada
        $this->priKEY = $this->pastacert.'priKEY.pem';
        //monta o path completo com o nome da chave prublica
        $this->pubKEY = $this->pastacert.'pubKEY.pem';
        //monta o path completo com o nome do certificado (chave publica e privada) em formato pem
        $this->certKEY = $this->pastacert.'certKEY.pem';
        //verificar se o nome do certificado e
        //o path foram carregados nas variaveis da classe
        if ($this->certName == '') {
            $msg .= "Um certificado deve ser passado para a classe pelo arquivo de configuração!";
            throw new nfsephpException($msg,self::STOP_CRITICAL);
        }
        //monta o caminho completo ate o certificado pfx
		$pfxCert = $this->pastacert.$this->certName;
        //verifica se o arquivo existe
        if(!file_exists($pfxCert)){
            $msg .= "Arquivo do Certificado não encontrado! $pfxCert";
            throw new nfsephpException($msg,self::STOP_CRITICAL);
        }
        //carrega o certificado em um string
        $pfxContent = file_get_contents($pfxCert);
        //carrega os certificados e chaves para um array denominado $x509certdata
        if (!openssl_pkcs12_read($pfxContent,$x509certdata,$this->senhapfx)){
            $msg .= "O certificado não pode ser lido. Pode estar corrompido ou a senha cadastrada está errada!";
            throw new nfsephpException($msg,self::STOP_CRITICAL);
        }
	//Verifica se o certificado é válido
        if ($testaVal){
            try {
                $this->__validCerts($x509certdata['cert']);
            }  catch (nfsephpException $e){
                //rethrow, fazer o mesmo throw, para a mesma classe usando a mesa execao, sem criar uma nova
                throw $e;
                //se for erro ou warning
                if($e->getCode()==self::STOP_CRITICAL){
                    return false;
                }
            }
        }
        //aqui verifica se existem as chaves em formato PEM
        //se existirem pega a data da validade dos arquivos PEM 
        //e compara com a data de validade do PFX
        //caso a data de validade do PFX for maior que a data do PEM
        //deleta dos arquivos PEM, recria e prossegue
        $flagNovo = false;
        if(file_exists($this->pubKEY)){
            $cert = file_get_contents($this->pubKEY);
            if (!$data = openssl_x509_read($cert)){
                //arquivo não pode ser lido como um certificado 
                //entao deletar
                $flagNovo = true;
            } else {
                //pegar a data de validade do mesmo
                $cert_data = openssl_x509_parse($data);
                // reformata a data de validade;
                $ano = substr($cert_data['validTo'],0,2);
                $mes = substr($cert_data['validTo'],2,2);
                $dia = substr($cert_data['validTo'],4,2);
                //obtem o timeestamp da data de validade do certificado
                $dValPubKey = gmmktime(0,0,0,$mes,$dia,$ano);
                //var_dump(date('d/m/Y',$dValPubKey));exit();
                //compara esse timestamp com o do pfx que foi carregado
                if ($testaVal){
                    //$this->pfxTimestamp global setada na funcao __validCerts()
                    if( $dValPubKey < $this->pfxTimestamp){
                        //o arquivo PEM eh de um certificado anterior 
                        //entao apagar os arquivos PEM
                        $flagNovo = true;
                    }//fim teste timestamp
                }
            }//fim read pubkey
        } else {
            //arquivo não localizado
            $flagNovo = true;
        }//fim if file pubkey
        //verificar a chave privada em PEM
        if(!file_exists($this->priKEY)){
            //arquivo nao encontrado
            $flagNovo = true;
        }
        //verificar o certificado em PEM
        if(!file_exists($this->certKEY)){
            //arquivo não encontrado
            $flagNovo = true;
        }
        //criar novos arquivos PEM
        if ($flagNovo){
            if(file_exists($this->pubKEY)){
                unlink($this->pubKEY);
            }
            if (file_exists($this->priKEY)){	
                unlink($this->priKEY);
            }
            if (file_exists($this->certKEY)){
                unlink($this->certKEY);
            }
            //recriar os arquivos pem com o arquivo pfx
            if (!file_put_contents($this->priKEY,$x509certdata['pkey'])) {
                $msg .= "Impossivel gravar no diretório! Permissão negada!";
                throw new nfsephpException($msg,self::STOP_CRITICAL);
                return false;
            }    
            $n = file_put_contents($this->pubKEY,$x509certdata['cert']);
            $n = file_put_contents($this->certKEY,$x509certdata['pkey']."\r\n".$x509certdata['cert']);                    
        }
        return true;
} 
    
	
protected function __validCerts($cert='', $aRetorno=''){
        $msg = "Erro no carregamento dos certificados.<br/>";
        if ($cert == ''){
            $msg .= "O certificado é um parâmetro obrigatorio.";
            throw new nfsephpException($msg,self::STOP_CRITICAL);
            return false;
        }
        if (!$data = openssl_x509_read($cert)){
            $msg .= "O certificado não pode ser lido pelo SSL - $cert .";
            throw new nfsephpException($msg,self::STOP_CRITICAL);
            return false;
        }
        $flagOK = true;
        $errorMsg = "";
        $cert_data = openssl_x509_parse($data);
        // reformata a data de validade;
        $ano = substr($cert_data['validTo'],0,2);
        $mes = substr($cert_data['validTo'],2,2);
        $dia = substr($cert_data['validTo'],4,2);
        //obtem o timestamp da data de validade do certificado
        $dValid = gmmktime(0,0,0,$mes,$dia,$ano);
        // obtem o timestamp da data de hoje
        $dHoje = gmmktime(0,0,0,date("m"),date("d"),date("Y"));
        //var_dump("$dia/$mes/$ano");exit();
        // compara a data de validade com a data atual
        if ($dValid < $dHoje ){
            $flagOK = false;
            $errorMsg = "A Validade do certificado expirou em ".$dia.'/'.$mes.'/'.$ano."";
            //alert para validade ultrapassada
            throw new nfsephpException($errorMsg, self::WARNING_MESSAGE);
        }else{
            $flagOK = $flagOK && true;
        }
        //diferenca em segundos entre os timestamp
        $diferenca = $dValid - $dHoje;
        // convertendo para dias
        $diferenca = round($diferenca /(60*60*24),0);
        //carregando a propriedade
        $daysToExpire = $diferenca;
        // convertendo para meses e carregando a propriedade
        $m = ($ano * 12 + $mes);
        $n = (date("y") * 12 + date("m"));
        //numero de meses até o certificado expirar
        $monthsToExpire = ($m-$n);
        $this->pfxTimestamp = $dValid;
        $aRetorno = array('status'=>$flagOK,'error'=>$errorMsg,'meses'=>$monthsToExpire,'dias'=>$daysToExpire);
        return true;
} 

public function consultalote($razao, $cnpj, $inscricao, $protocolo){
$xml = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<mConsultaLoteRPS xmlns="http://tempuri.org/">
			<remessa>
				<![CDATA[<ConsultarLoteRpsEnvio xmlns="http://www.abrasf.org.br/nfse.xsd" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Prestador><CpfCnpj><Cnpj>'.$cnpj.'</Cnpj></CpfCnpj><RazaoSocial>'.$razao.'</RazaoSocial><InscricaoMunicipal>'.$inscricao.'</InscricaoMunicipal></Prestador><Protocolo>'.$protocolo.'</Protocolo></ConsultarLoteRpsEnvio>]]></remessa>
			<cabecalho>
				<![CDATA[<cabecalho xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.abrasf.org.br/nfse.xsd" <versaoDados>20.01</versaoDados> </cabecalho>]]></cabecalho>
		</mConsultaLoteRPS>
	</soap:Body>
</soap:Envelope>';

$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8" standalone="no"?>',$xml);
$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>','',$soap_msg);
$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$soap_msg);
$soap_msg = str_replace("\n","",$soap_msg);
$soap_msg = str_replace("  "," ",$soap_msg);
$soap_msg = str_replace("> <","><",$soap_msg);
		
$headers = array(
 "Content-type: text/xml; charset=\"utf-8\"",
 "Accept: text/xml",
 "Cache-Control: no-cache",
 "Pragma: no-cache",
 "SOAPAction: http://tempuri.org/mConsultaLoteRPS", 
 "Content-length: ".strlen($soap_msg),
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $this->consultalote);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 86400);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_msg);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$retorno = curl_exec($ch);
 //pega os dados do array retornado pelo NuSoap
 $retorno=str_replace('&lt;','<',$retorno);
 $retorno=str_replace('&gt;','>',$retorno);
 $retorno=str_replace('<?xml version="1.0" encoding="utf-8"?>','',$retorno);
 $xmlresp = utf8_encode($retorno);
 if ($xmlresp == ''){
  echo 'erro';
 }
 //tratar dados de retorno
 $doc = new DOMDocument(); //cria objeto DOM
 $doc->formatOutput = FALSE;
 $doc->preserveWhiteSpace = FALSE;
 $doc->loadXML($retorno,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
 // status do recebimento ou mensagem de erro
 $aRet['Situacao'] = $doc->getElementsByTagName('Situacao')->item(0)->nodeValue;
 $aRet['Numero'] = $doc->getElementsByTagName('Numero')->item(0)->nodeValue;
 $aRet['CodigoVerificacao'] = $doc->getElementsByTagName('CodigoVerificacao')->item(0)->nodeValue;
 $aRet['DataEmissao'] = $doc->getElementsByTagName('DataEmissao')->item(0)->nodeValue;
 $aRet['LinkNota'] = $doc->getElementsByTagName('LinkNota')->item(0)->nodeValue;	 
 return $aRet;

}	

public function cancelarNfse($NumeroNota, $Cnpj, $InscricaoMunicipal, $CodigoMunicipio, $cert_password, $pfxCertPrivado, $pastacertificado){
// pega dados certificado é obrigatorio para assinar nota 
$this->baseCerts($cert_password, $CodigoMunicipio, $pfxCertPrivado, $pastacertificado);

$identificador = 2;
$NumeroNotaposicao = str_pad($NumeroNota, 16, '0', STR_PAD_LEFT);
$chavenota = $identificador.$Cnpj.$NumeroNotaposicao;
$tag = 'InfPedidoCancelamento';

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
$PrestadorCnpj = $dom->createElement("Cnpj",$Cnpj);
$CpfCnpjNovo->appendChild($PrestadorCnpj); 
$InscricaoM = $dom->createElement("InscricaoMunicipal", $InscricaoMunicipal);
$CodigoMunicipio = $dom->createElement("CodigoMunicipio", $CodigoMunicipio);
$CodigoCancelamento = $dom->createElement("CodigoCancelamento", "1");

$IdentificacaoNfse->appendChild($numero);
$IdentificacaoNfse->appendChild($CpfCnpjNovo);
$IdentificacaoNfse->appendChild($InscricaoM);
$IdentificacaoNfse->appendChild($CodigoMunicipio);
$InfPedidoCancelamento->appendChild($CodigoCancelamento);
$root->appendChild($Pedido);
$dom->appendChild($root);
header("Content-Type: text/xml");
$xml1 = $dom->saveXML();

$xml1 = str_replace('<?xml version="1.0" encoding="utf-8"?>','<?xml version="1.0" encoding="utf-8" standalone="no"?>',$xml1);
$xml1 = str_replace('<?xml version="1.0" encoding="utf-8" standalone="no"?>','',$xml1);
$xml1 = str_replace('<?xml version="1.0" encoding="utf-8"?>','',$xml1);  
$xml1 = str_replace("\n","",$xml1);
$xml1 = str_replace("  "," ",$xml1);
$xml1 = str_replace("  "," ",$xml1);
$xml1 = str_replace("  "," ",$xml1);
$xml1 = str_replace("  "," ",$xml1);
$xml1 = str_replace("  "," ",$xml1);
$xml1 = str_replace("> <","><",$xml1);
$assinaxml1 = $this->signXML($xml1, $tag);
$stringXML = $assinaxml1;

$soap_msg = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<mCancelamentoNFSe xmlns="http://tempuri.org/">
			<remessa>
				<![CDATA['.$stringXML.']]>
		    </remessa>
	        <cabecalho>
		        <![CDATA[<cabecalho xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.abrasf.org.br/nfse.xsd" <versaoDados>20.01</versaoDados> </cabecalho>]]>
		    </cabecalho>
		</mCancelamentoNFSe>
	</soap:Body>
</soap:Envelope>'; 

$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8" standalone="no"?>',$soap_msg);
$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>','',$soap_msg);
$soap_msg = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$soap_msg);
$soap_msg = str_replace("\n","",$soap_msg);
$soap_msg = str_replace("  "," ",$soap_msg);
$soap_msg = str_replace("> <","><",$soap_msg);

$headers = array(
 "Content-type: text/xml;charset=\"utf-8\"",
 "Accept: text/xml",
 "Cache-Control: no-cache",
 "Pragma: no-cache",
 "SOAPAction: http://tempuri.org/mCancelamentoNFSe", 
 "Content-length: ".strlen($soap_msg),
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $this->urlcancelar);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_msg);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$retorno = curl_exec($ch); 
return $retorno;

}


public function ConsultarSequenciaLoteNotaRPSEnvio($Cnpj, $RazaoSocial, $InscricaoMunicipal){
 $xml = '<?xml version="1.0" encoding="utf-8"?>
 <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
    <mConsultaSequenciaLoteNotaRPS xmlns="http://tempuri.org/">
      <remessa>
  		<![CDATA[<ConsultarSequenciaLoteNotaRPSEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
          <Prestador>
            <CpfCnpj>
              <Cnpj>'.$Cnpj.'</Cnpj>
            </CpfCnpj>
            <RazaoSocial>'.$RazaoSocial.'</RazaoSocial>
            <InscricaoMunicipal>'.$InscricaoMunicipal.'</InscricaoMunicipal>
          </Prestador>
        </ConsultarSequenciaLoteNotaRPSEnvio>]]>
	  </remessa>
	</mConsultaSequenciaLoteNotaRPS>	
 </soap:Body>
</soap:Envelope>';	

$headers = array(
 "Content-type: text/xml; charset=\"utf-8\"",
 "Accept: text/xml",
 "Cache-Control: no-cache",
 "Pragma: no-cache",
 "SOAPAction: http://tempuri.org/mConsultaSequenciaLoteNotaRPS", 
 "Content-length: ".strlen($xml),
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $this->urlconsulta);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch); 
curl_close($ch);
$x = json_encode($response);
$array = explode('\r\n', $x);
$arrayfinal = preg_replace("/[^0-9]/", "", $array);
return $arrayfinal;
}


public function geraCodigoIBGE($cep){
 $cep = preg_replace("/[^0-9]/", "", $cep);
 $url = "http://viacep.com.br/ws/$cep/xml/";
 $xml = simplexml_load_file($url);
 $codigoibge = $xml->ibge;	
 return $codigoibge;
}


public function signXML($sXML, $tagid) {
        $fp = fopen($this->priKEY, "r");
        $priv_key=fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);
		
		$order = array("\r\n", "\n", "\r", "\t");
        $replace = '';
        $sXML = str_replace($order, $replace, $sXML);
		$sXML=str_replace('<?xml version="1.0" encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8" standalone="no"?>',$sXML);
        $sXML=str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>','',$sXML);
        $sXML=str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$sXML);
        $sXML=str_replace('<?xml version="1.0"?>','',$sXML);
        $sXML=str_replace("\n","",$sXML);
        $sXML=str_replace("  "," ",$sXML);
        $sXML=str_replace("> <","><",$sXML);
		
        $dom = new DOMDocument('1.0', 'utf-8');
		$dom->preservWhiteSpace=false; //elimina espaços em branco
        $dom->formatOutput = false;
		$dom->loadXML($sXML,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

        $root = $dom->documentElement;
		$node = $dom->getElementsByTagName($tagid)->item(0);
        $Id = trim($node->getAttribute("Id"));
        $idnome = preg_replace('/[^0-9]/','', $Id);

        $dados = $node->C14N(FALSE, FALSE, NULL, NULL);
		$dados = str_replace(' >', '>', $dados);
        $hashValue = hash('sha1', $dados, TRUE);
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
        $Reference->setAttribute('URI', '#'.$Id);

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
        
		$resp = openssl_sign($dadosn, $signaturevar, $pkeyid);
			
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
		$cert = $this->__cleanCerts($this->pubKEY);
        $newNode = $dom->createElement('X509Certificate', $cert);
        $X509Data->appendChild($newNode);

        //grava na string o objeto DOM
        $returnxml = $dom->saveXML();	
		
		openssl_free_key($pkeyid);
		
		$returnxml = str_replace('<?xml version="1.0" encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8" standalone="no"?>',$returnxml);
        $returnxml = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>','',$returnxml);
        $returnxml = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$returnxml);
        $returnxml = str_replace('<?xml version="1.0"?>','',$returnxml);
        $returnxml = str_replace("\n","",$returnxml);
        $returnxml = str_replace("  "," ",$returnxml);
        $returnxml = str_replace("> <","><",$returnxml);
		 
        //retorna o documento assinado
        return $returnxml;
}


protected function __cleanCerts($certFile){
        try {
            //inicializa variavel
            $data = '';
            //carregar a chave publica do arquivo pem
            if (!$pubKey = file_get_contents($certFile)){
                $msg = "Arquivo não encontrado - $certFile .";
                throw new nfsephpException($msg);
            }
            //carrega o certificado em um array usando o LF como referencia
            $arCert = explode("\n", $pubKey);
            foreach ($arCert AS $curData) {
                //remove a tag de inicio e fim do certificado
                if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 && strncmp($curData, '-----END CERTIFICATE', 20) != 0 ) {
                    //carrega o resultado numa string
                    $data .= trim($curData);
                }
            }
        } catch (nfephpException $e) {
            $this->__setError($e->getMessage());
            if ($this->exceptions) {
                throw $e;
            }
            return false;
        }
        return $data;
}
    
		
public function retiraespacosCDATA($string){
  $string = str_replace(" ", '', $string);
  return $string;
}  

public function retira($string){
 $string = (str_replace(array('-----BEGIN PUBLIC KEY-----',
            '-----END PUBLIC KEY-----', "\n"), '', $string));
 return $string;
}


public function listUltimaNota(){
        $Conexao = NAMEDB.':host='.HOST.';dbname='.BASE;
        $Conecta = new PDO($Conexao, USER, PASS);
        $sqlListUsuarioAll = "SELECT * FROM itensnfse order by id asc";
        try{
            $queryListUsuarioAll = $Conecta->prepare($sqlListUsuarioAll);
            $queryListUsuarioAll->execute();
            $restultListUsuarioAll = $queryListUsuarioAll->fetchAll(PDO::FETCH_ASSOC);
            return $restultListUsuarioAll;
        }  catch (PDOException $erroListUsuarioAll){
            echo 'Erro ao listar todos os usuarios '.$erroListUsuarioAll->getMessage();
        }
}

public function listNotas(){
        $Conexao = NAMEDB.':host='.HOST.';dbname='.BASE;
        $Conecta = new PDO($Conexao, USER, PASS);
        $sqlListUsuarioAll = "SELECT * FROM nfse order by id desc";
        try{
            $queryListUsuarioAll = $Conecta->prepare($sqlListUsuarioAll);
            $queryListUsuarioAll->execute();
            $restultListUsuarioAll = $queryListUsuarioAll->fetchAll(PDO::FETCH_ASSOC);
            return $restultListUsuarioAll;
        }  catch (PDOException $erroListUsuarioAll){
            echo 'Erro ao listar todos os usuarios '.$erroListUsuarioAll->getMessage();
        }
}

public function cadastrarNfseBanco($numeronota,$numerolote,$numerorps,$protocolo,$linknota,$codigoverificacao){
    $Conexao = NAMEDB.':host='.HOST.';dbname='.BASE;
    $Conecta = new PDO($Conexao, USER, PASS);
	
	$sqlCreateUsuarioP1 = "INSERT INTO itensnfse VALUES ('0','$numeronota','$numerolote','$numerorps')"; 
    $queryCreateUsuarioP1 = $Conecta->prepare($sqlCreateUsuarioP1);
    $queryCreateUsuarioP1->execute();
    $restultCreateUsuarioPag1 = $queryCreateUsuarioP1->fetchAll(PDO::FETCH_ASSOC);
	 
	$sqlCreateUsuarioP = "INSERT INTO nfse VALUES ('0','$numeronota','$numerolote','$numerorps','$protocolo','$linknota','$codigoverificacao')"; 
     try{
      $queryCreateUsuarioP = $Conecta->prepare($sqlCreateUsuarioP);
      $queryCreateUsuarioP->execute();
      $restultCreateUsuarioPag = $queryCreateUsuarioP->fetchAll(PDO::FETCH_ASSOC);
      return $restultCreateUsuarioPag;
     }  catch (PDOException $erroCreateUsuario){
      echo 'Erro ao cadastrar o usuario '.$erroCreateUsuario->getMessage();
     }
} 


public function deletaNfseBanco($cod){
        $Conexao = NAMEDB.':host='.HOST.';dbname='.BASE;
        $Conecta = new PDO($Conexao, USER, PASS);
        $sqlDeleteUsuario = "DELETE FROM nfse WHERE numeronota=".intval($cod);
        try{
            $queryDeleteUsuario = $Conecta->prepare($sqlDeleteUsuario);
            $queryDeleteUsuario->execute();
        } 
        catch (PDOException $erroDeleteUsuario){
            echo 'Erro ao deletar o usuario '.$erroDeleteUsuario->getMessage();
        }
}
	
}
?>
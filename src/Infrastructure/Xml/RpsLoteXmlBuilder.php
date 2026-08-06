<?php

namespace NovaPrata\Nfse\Infrastructure\Xml;

use DOMDocument;

/**
 * Monta o XML (ainda nao assinado) do EnviarLoteRpsSincronoEnvio, no padrao ABRASF.
 */
final class RpsLoteXmlBuilder
{
    public const TAG_ID = 'InfDeclaracaoPrestacaoServico';

    public function build(
        $NumeroLote,
        $NumeroRPS,
        $Cnpj,
        $InscricaoMunicipal,
        $RazaoSocial,
        $Valorservico,
        $opcao,
        $Cnpjcpf,
        $Endereco,
        $Numero,
        $Bairro,
        $Cepcliente,
        $CodigoMunicipioCliente,
        $Telefone,
        $Email,
        $CodigoCnae,
        $Aliquota,
        $Descricao,
        $Nome,
        $NumeroParcelas,
        $CodigoMunicipioEmpresa,
        $UFCliente,
        $data,
        $ano
    ): string {
        $identificador = 1;
        $numerosequencial = str_pad($NumeroLote, 16, '0', STR_PAD_LEFT);
        $chave = $identificador . $ano . $numerosequencial;

        $identificadorprest = 1;
        $numerosequencialprest = str_pad($NumeroRPS, 16, '0', STR_PAD_LEFT);
        $chaveprestacao = $identificadorprest . $Cnpj . $numerosequencialprest;

        if ($Aliquota == 3) {
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
        $LoteRps->setAttribute("Id", 'L' . $chave);
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
        $Tipox = $dom->createElement("Tipo", "1");
        $IdentificacaoRps->appendChild($NumeroRPSx);
        $IdentificacaoRps->appendChild($Seriex);
        $IdentificacaoRps->appendChild($Tipox);
        $data2 = $dom->createElement("DataEmissao", $data);
        $Rps2->appendChild($data2);
        $Statusx = $dom->createElement("Status", "1");
        $Rps2->appendChild($Statusx);
        $RpsSubstituido = $dom->createElement("RpsSubstituido");
        $Rps2->appendChild($RpsSubstituido);
        $Numerosubs = $dom->createElement("Numero");
        $RpsSubstituido->appendChild($Numerosubs);
        $Seriesubs = $dom->createElement("Serie");
        $RpsSubstituido->appendChild($Seriesubs);
        $Tiposubs = $dom->createElement("Tipo", "1");
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
        $TomadorCpf = $dom->createElement("Cpf", $Cnpjcpf);
        $TomadorCnpj = $dom->createElement("Cnpj", $Cnpjcpf);
        if ($opcao == 'CPF') {
            $CpfCnpjtomador->appendChild($TomadorCpf);
            $InscricaoMunicipalTomador = $dom->createElement("InscricaoMunicipal");
            $InscricaoEstadualTomador = $dom->createElement("InscricaoEstadual");
        } else {
            $CpfCnpjtomador->appendChild($TomadorCnpj);
            $InscricaoMunicipalTomador = $dom->createElement("InscricaoMunicipal", "333333333");
            $InscricaoEstadualTomador = $dom->createElement("InscricaoEstadual", "333333333");
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
        if ($Telefone != '') {
            $TelefoneTomador = $dom->createElement("Telefone", $Telefone);
        } else {
            $TelefoneTomador = $dom->createElement("Telefone");
        }

        if ($Email != '') {
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
        $xml = $dom->saveXML();
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '<?xml version="1.0" encoding="utf-8" standalone="no"?>', $xml);
        $xml = str_replace('<?xml version="1.0" encoding="utf-8" standalone="no"?>', '', $xml);
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $xml);
        $xml = str_replace("\n", "", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("> <", "><", $xml);
        $xml = html_entity_decode(stripslashes($xml), ENT_QUOTES, 'UTF-8');

        return $xml;
    }
}

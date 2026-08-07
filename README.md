# nfse-novaprata

Biblioteca PHP para emissão, cancelamento e consulta de NFS-e (padrão ABRASF) do
município de Nova Prata/RS, com scripts de exemplo (`index.php`, `envio.php`, etc.)
que usam um banco MySQL local para guardar as notas emitidas.

## Requisitos

- PHP 8.2+
- Extensões: `openssl`, `curl`, `dom`, `pdo`
- Composer

## Instalação

1. `composer install`
2. Copie `.env.example` para `.env` e ajuste os valores (banco de dados, ambiente
   homologação/produção, dados do prestador e do certificado). Veja a seção
   [Configuração](#configuração) abaixo.
3. Crie o banco de dados e as tabelas `nfse` e `itensnfse` usadas por
   `src/Repositories/NfseRepository.php` (colunas usadas: `numeronota`,
   `numerolote`, `numerorps`, `protocolo`, `linknota`, `codigoverificacao`).
4. Coloque o certificado digital (`.pfx`, tipo A1) dentro da pasta apontada por
   `NFSE_CERT_PATH` (padrão: `cert/`), com o nome de arquivo definido em
   `NFSE_CERT_FILE`. Nunca commite o `.pfx` real — veja `cert/README.md`.
   Essa pasta precisa ter permissão de escrita: na primeira assinatura, os
   arquivos `.pem` (`priKEY.pem`, `pubKEY.pem`, `certKEY.pem`) são derivados do
   `.pfx` e gravados ali, sendo reaproveitados até o certificado vencer.

## Configuração

Toda a configuração vem de variáveis de ambiente (carregadas do `.env` via
`vlucas/phpdotenv`), lidas por `src/Config/Config.php`:

- `DB_DRIVER`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — conexão PDO.
- `NFSE_AMBIENTE` — `homologacao` ou `producao`; decide qual conjunto de URLs do
  webservice é usado (`NFSE_URL_ENVIO_*`, `NFSE_URL_CANCELAR_*`,
  `NFSE_URL_CONSULTALOTE_*`). `NFSE_URL_CONSULTA` é a mesma em ambos os ambientes.
- `NFSE_PROVIDER_CNPJ`, `NFSE_PROVIDER_INSCRICAO_MUNICIPAL`,
  `NFSE_PROVIDER_CODIGO_MUNICIPIO`, `NFSE_PROVIDER_RAZAO_SOCIAL` — dados do
  prestador (emitente) das notas.
- `NFSE_CERT_PATH`, `NFSE_CERT_FILE`, `NFSE_CERT_PASSWORD` — certificado digital.

Veja `.env.example` para os valores padrão e comentários de cada variável.

> CPF e CNPJ devem ser informados sem formatação, só números.

## Estrutura do projeto

- `Nfse.php` — fachada com a API pública original da classe (mantida por
  compatibilidade com os scripts abaixo), delegando para `src/`.
- `src/Config/Config.php` — carrega e expõe a configuração vinda do `.env`.
- `src/Application/Services/NfseService.php` — orquestra a geração, assinatura
  e envio da NFS-e, delegando para as classes de infraestrutura.
- `src/Infrastructure/Xml/` — monta os XMLs/envelopes SOAP (RPS, cancelamento,
  consulta) no padrão ABRASF, ainda não assinados.
- `src/Infrastructure/Xml/XmlSigner.php` — assina o XML com o certificado.
- `src/Infrastructure/Certificate/CertificateManager.php` — carrega o `.pfx` e
  deriva/reaproveita os `.pem` usados na assinatura.
- `src/Infrastructure/Http/SoapClient.php` — cliente HTTP/cURL genérico usado
  para chamar o webservice de NFS-e.
- `src/Repositories/NfseRepository.php` — persistência das notas emitidas
  (tabelas `nfse` e `itensnfse`) via PDO.
- `src/Helpers/IbgeCodeResolver.php` — resolve o código IBGE do município a
  partir de um CEP, via API pública do ViaCEP.
- `src/Exceptions/NfseException.php` — exceção usada nas falhas de certificado.
- `index.php`, `envio.php`, `salvar.php`, `cancelarnota.php`, `deletarnota.php`,
  `consultalote.php` — scripts de exemplo/uso na raiz do projeto.

## Uso

Os scripts da raiz são o exemplo de uso real e mais atualizado da biblioteca.
Resumo do fluxo (veja `envio.php` para o exemplo completo):

```php
require_once 'Nfse.php';

$config = \NovaPrata\Nfse\Config\Config::fromEnvironment();
$Criar = new Nfse($config);

// Dados do prestador vêm do .env
$Cnpj = $config->providerCnpj();
$InscricaoMunicipal = $config->providerInscricaoMunicipal();
$CodigoMunicipioEmpresa = $config->providerCodigoMunicipio();
$RazaoSocial = $config->providerRazaoSocial();

// Certificado digital, também via .env
$pastacertificado = $config->certPath();
$pfxCertPrivado = $config->certFile();
$cert_password = $config->certPassword();

$criado = $Criar->criarNfse(
    $NumeroNota, $NumeroLote, $NumeroRPS, $Cnpj, $InscricaoMunicipal, $RazaoSocial,
    $Valorservico, $opcao, $Cnpjcpf, $Endereco, $Numero, $Bairro, $Cepcliente,
    $CodigoMunicipioCliente, $Telefone, $Email, $TipoNota, $CodigoCnae, $Aliquota,
    $Descricao, $Nome, $FormaPagamento, $NumeroParcelas, $CodigoMunicipioEmpresa,
    $UFCliente, $data, $ano, $pastacertificado, $pfxCertPrivado, $cert_password
);
```

Consulta de lote:

```php
$consultalote = $Criar->consultalote($RazaoSocial, $Cnpj, $InscricaoMunicipal, $protocolo);
echo $consultalote['Situacao'];        // código da situação do lote de RPS:
                                        // 1-Não recebido / 2-Não processado /
                                        // 3-Processado com erro / 4-Processado com sucesso
echo $consultalote['Numero'];          // número da nota, se processado com sucesso
echo $consultalote['LinkNota'];
echo $consultalote['CodigoVerificacao'];
```

Cancelamento:

```php
$cancelarnota = $Criar->cancelarNfse(
    $NumeroNota, $Cnpj, $InscricaoMunicipal, $CodigoMunicipio,
    $cert_password, $pfxCertPrivado, $pastacertificado
);
```

## Testes

```
composer test    # roda a suite PHPUnit (tests/Unit, tests/Integration)
composer cs      # verifica PSR-12 em src/
composer cs-fix  # corrige automaticamente o que der em src/
```

A suite hoje cobre `Config` e as classes de montagem de XML/certificado que não
dependem de rede, banco ou um certificado `.pfx` real. `SoapClient`,
`NfseRepository`, `IbgeCodeResolver` e a orquestração completa em `NfseService`
ainda não têm testes automatizados — veja os arquivos em `tests/Unit/` para o
que já está coberto.

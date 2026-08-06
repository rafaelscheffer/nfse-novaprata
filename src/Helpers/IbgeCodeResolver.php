<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Helpers;

/**
 * Resolve o codigo IBGE do municipio a partir de um CEP, usando a API publica do ViaCEP.
 */
final class IbgeCodeResolver
{
    public function resolve(string $cep): string
    {
        $cep = preg_replace("/[^0-9]/", "", $cep);
        $url = "http://viacep.com.br/ws/$cep/xml/";
        $xml = simplexml_load_file($url);

        return (string) $xml->ibge;
    }
}

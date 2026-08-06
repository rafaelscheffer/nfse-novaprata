<?php

declare(strict_types=1);

namespace NovaPrata\Nfse\Infrastructure\Http;

/**
 * Cliente SOAP/cURL generico usado por todas as operacoes do webservice de NFS-e
 * (envio, cancelamento, consulta e consulta de lote).
 */
final class SoapClient
{
    public function post(string $url, string $soapAction, string $envelope, int $timeoutSeconds): string
    {
        $headers = [
            'Content-type: text/xml; charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ' . $soapAction,
            'Content-length: ' . strlen($envelope),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutSeconds);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $envelope);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        return (string) $response;
    }
}

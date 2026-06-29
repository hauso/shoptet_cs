<?php

declare(strict_types=1);

namespace App\Provider\Balikovna;

use App\Mapper\Balikovna\BalikovnaPickupPointMapper;
use App\Model\PickupPoint\ImportedPickupPoint;
use App\Provider\PickupPoint\PickupPointProviderInterface;
use App\Service\HttpClient;

final readonly class BalikovnaPickupPointProvider implements PickupPointProviderInterface
{
    public function __construct(private HttpClient $httpClient, private BalikovnaPickupPointMapper $mapper, private string $feedUrl)
    {
    }

    public function getCarrier(): string
    {
        return 'balikovna';
    }

    /** @return list<ImportedPickupPoint> */
    public function provide(): array
    {
        $body = trim($this->httpClient->get($this->feedUrl));
        if ($body === '') {
            throw new \RuntimeException('Balikovna feed response is empty.');
        }

        $xml = $this->parseXml($body);
        $items = $xml->xpath('//*[local-name() = "row"]') ?: [];
        if ($items === []) {
            throw new \RuntimeException('Balikovna feed does not contain any pickup point rows.');
        }

        $points = [];
        foreach ($items as $item) {
            $points[] = $this->mapper->map($item);
        }

        return $points;
    }

    private function parseXml(string $body): \SimpleXMLElement
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $xml = simplexml_load_string($body, \SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
            if (!$xml instanceof \SimpleXMLElement) {
                $messages = array_map(static fn (\LibXMLError $error): string => trim($error->message), libxml_get_errors());
                throw new \RuntimeException('Balikovna feed is not valid XML: ' . implode('; ', array_filter($messages)));
            }

            return $xml;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }
}

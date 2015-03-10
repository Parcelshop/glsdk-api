<?php
/**
 * This file is part of the Lsv\GlsDk
 */
namespace Lsv\GlsDk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Lsv\GlsDk\Entity\Opening;
use Lsv\GlsDk\Entity\Parcelshop as Entity;

/**
 * Parcelshop
 *
 * @author Martin Aarhof <martin.aarhof@gmail.com>
 */
class ParcelShop
{

    /**
     * Webservice url for denmark
     *
     * @var string
     */
    const DK_WEBSERVICE = 'http://www.gls.dk/webservices_v2/wsPakkeshop.asmx';

    /**
     * HTTP URL
     *
     * @var string
     */
    private $url;

    /**
     * HTTP client
     *
     * @var Client
     */
    private $client;

    /**
     * Construct parcel
     *
     * @param Client $client
     * @param string $url
     */
    public function __construct(Client $client = null, $url = self::DK_WEBSERVICE)
    {
        $this->client = ($client ? $client : new Client());
        $this->url = $url;
    }

    /**
     * Get parcel from ID
     *
     * @param int $parcelnumber
     * @return Entity
     * @throws Exceptions\ParcelNotFoundException
     */
    public function getParcelshop($parcelnumber)
    {
        $url = sprintf(
            '%s/GetOneParcelShop?ParcelShopNumber=%s',
            $this->url,
            $parcelnumber
        );
        try {
            $request = $this->client->get($url);
            return $this->generateParcels($request->xml(), true);
        } catch (ServerException $e) {
            throw new Exceptions\ParcelNotFoundException($parcelnumber);
        }
    }

    /**
     * Get all parcels in Denmark
     *
     * @return Parcelshop[]
     */
    public function getAllParcelshops()
    {
        $url = sprintf(
            '%s/GetAllParcelShops',
            $this->url
        );
        $request = $this->client->get($url);
        return $this->generateParcels($request->xml());
    }

    /**
     * Get parcels from zipcode
     *
     * @param string $zipcode
     * @return Entity[]
     * @throws Exceptions\NoParcelsFoundInZipcodeException
     */
    public function getParcelshopsFromZipcode($zipcode)
    {
        $url = sprintf(
            '%s/GetParcelShopsInZipcode?zipcode=%s',
            $this->url,
            $zipcode
        );
        $request = $this->client->get($url);
        $parcels = $this->generateParcels($request->xml());
        if (! $parcels) {
            throw new Exceptions\NoParcelsFoundInZipcodeException($zipcode);
        }
        return $parcels;
    }

    /**
     * Get nearest parcels from a address
     *
     * @param string $street
     * @param string $zipcode
     * @param int $limit
     * @return Entity[]
     * @throws Exceptions\MalformedAddressException
     */
    public function getParcelshopsNearAddress($street, $zipcode, $limit = 20)
    {
        $url = sprintf(
            '%s/GetNearstParcelShops?street=%s&zipcode=%s&Amount=%s',
            $this->url,
            $street,
            $zipcode,
            $limit
        );
        try {
            $request = $this->client->get($url);
            $xml = $request->xml();
            if (isset($xml->parcelshops) && isset($xml->parcelshops->PakkeshopData)) {
                return $this->generateParcels($xml->parcelshops->PakkeshopData);
            }
            throw new Exceptions\MalformedAddressException($street, $zipcode);
        } catch (ServerException $e) {
            throw new Exceptions\MalformedAddressException($street, $zipcode);
        }
    }

    /**
     * Parse parcels from xml
     *
     * @param \SimpleXMLElement $xml
     * @param bool $single
     * @return Entity[]|Entity
     */
    private function generateParcels(\SimpleXMLElement $xml, $single = false)
    {
        if ($single) {
            $xml = [$xml];
        }

        $shops = [];
        foreach ($xml as $shop) {
            $parcel = new Entity();
            $parcel
                ->setNumber(self::xmlString($shop->Number))
                ->setCompanyname(self::xmlString($shop->CompanyName))
                ->setStreetname(self::xmlString($shop->Streetname))
                ->setZipcode(self::xmlString($shop->ZipCode))
                ->setCity(self::xmlString($shop->CityName))
                ->setCountrycode(self::xmlString($shop->CountryCode))
                ->setCountrycodeIso(self::xmlString($shop->CountryCodeISO3166A2))
                ->setTelephone(self::xmlString($shop->Telephone))
            ;

            if (isset($shop->Longitude) && isset($shop->Latitude)) {
                $parcel->setCoordinate(self::xmlString($shop->Longitude), self::xmlString($shop->Latitude));
            }

            if (isset($shop->Streetname2)) {
                $parcel->setStreetname2(self::xmlString($shop->Streetname2));
            }

            if (isset($shop->OpeningHours)) {
                $parcel->setOpenings($this->parseOpenings($shop->OpeningHours));
            }

            $shops[] = $parcel;
        }

        if ($single) {
            return $shops[0];
        }

        return $shops;
    }

    /**
     * Parse openings from XML
     *
     * @param \SimpleXMLElement $xml
     * @return Opening[]
     */
    private function parseOpenings(\SimpleXMLElement $xml)
    {
        $openings = [];
        if (isset($xml->Weekday)) {
            foreach ($xml->Weekday as $weekday) {
                /** @var \SimpleXMLElement $weekday */
                if (isset($weekday->day) && isset($weekday->openAt)) {
                    $open = new Opening();
                    $open->setDay(self::xmlString($weekday->day))
                        ->setOpenFrom(self::xmlString($weekday->openAt->From))
                        ->setOpenTo(self::xmlString($weekday->openAt->To));
                    $openings[] = $open;
                }
            }
        }
        return $openings;
    }

    /**
     * Convert simplexml element to string
     *
     * @param mixed $xml
     * @return string
     */
    private static function xmlString($xml)
    {
        return (string)$xml;
    }
}

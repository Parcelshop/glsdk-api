<?php
namespace Lsv\GlsDkTest;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use Lsv\GlsDk\ParcelShop;

abstract class AbstractParcelShopTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return ParcelShop
     */
    protected function getParser(Mock $mock = null)
    {
        return new ParcelShop($this->getClient($mock));
    }

    protected function getReturnXml($xmlfile)
    {
        return file_get_contents(__DIR__ . '/returns/' . $xmlfile);
    }

    protected function getClient(Mock $mock = null)
    {
        $client = new Client();

        if ($mock) {
            $client->getEmitter()->attach($mock);
        }

        return $client;
    }

    protected function getExceptionNamespace($exception)
    {
        return sprintf('Lsv\GlsDk\Exceptions\%s', $exception);
    }

}

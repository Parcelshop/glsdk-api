<?php
namespace Lsv\GlsDkTest;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Lsv\GlsDk\ParcelShop;

abstract class AbstractParcelShopTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param MockHandler|null $mock
     *
     * @return ParcelShop
     */
    protected function getParser(MockHandler $mock = null)
    {
        return new ParcelShop($this->getClient($mock));
    }

    protected function getReturnXml($xmlfile)
    {
        return file_get_contents(__DIR__ . '/returns/' . $xmlfile);
    }

    protected function getClient(MockHandler $mock = null)
    {
        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    protected function getExceptionNamespace($exception)
    {
        return sprintf('Lsv\GlsDk\Exceptions\%s', $exception);
    }

}

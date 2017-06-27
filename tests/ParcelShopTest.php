<?php
namespace Lsv\GlsDkTest;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ParcelShopTest extends AbstractParcelShopTest
{

    public function test_one_parcel_not_found()
    {
        $this->setExpectedException($this->getExceptionNamespace('ParcelNotFoundException'), '', 210);

        $mock = new MockHandler([
            new Response(500)
        ]);

        $this->getParser($mock)->getParcelshop('unknown');
    }

    public function test_one_parcel_found()
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getReturnXml('oneparcel.xml'))
        ]);

        $parcel = $this->getParser($mock)->getParcelshop(123456);

        $this->assertInstanceOf('Lsv\GlsDk\Entity\Parcelshop', $parcel);
        $this->assertEquals('123456', $parcel->getNumber());
        $this->assertEquals('Companyname', $parcel->getCompanyname());
        $this->assertEquals('City', $parcel->getCity());
        $this->assertEquals('008', $parcel->getCountrycode());
        $this->assertEquals('DK', $parcel->getCountrycodeIso());
        $this->assertEquals('Somewhere', $parcel->getStreetname());
        $this->assertEquals('Somewhere2', $parcel->getStreetname2());
        $this->assertEquals('-', $parcel->getTelephone());
        $this->assertEquals('1000', $parcel->getZipcode());
        $this->assertEquals('10.0000,56.000', $parcel->getCoordinate());
        $this->assertCount(7, $parcel->getOpenings());

        foreach($parcel->getOpenings() as $opening) {
            $this->assertTrue(in_array($opening->getDay(), ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']));
            $this->assertEquals('06:30', $opening->getOpenFrom());
            $this->assertEquals('22:00', $opening->getOpenTo());
        }

    }

    public function test_get_all_parcels()
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getReturnXml('allparcels.xml'))
        ]);

        $parcels = $this->getParser($mock)->getAllParcelshops();
        $this->assertCount(2, $parcels);
        foreach($parcels as $parcel) {
            $this->assertInstanceOf('Lsv\GlsDk\Entity\Parcelshop', $parcel);
        }

    }

    public function test_get_parcels_from_zipcode_zipcode_not_found()
    {
        $this->setExpectedException($this->getExceptionNamespace('NoParcelsFoundInZipcodeException'), '', 220);
        $mock = new MockHandler([
            new Response(200, [], $this->getReturnXml('parcelszipcode_notfound.xml'))
        ]);
        $this->getParser($mock)->getParcelshopsFromZipcode(1000);
    }

    public function test_get_parcels_from_zipcode()
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getReturnXml('parcelszipcode.xml'))
        ]);

        $parcels = $this->getParser($mock)->getParcelshopsFromZipcode(1000);
        $this->assertCount(2, $parcels);
        foreach($parcels as $parcel) {
            $this->assertInstanceOf('Lsv\GlsDk\Entity\Parcelshop', $parcel);
        }

    }

    public function test_get_nearst_parcel_wrong_address()
    {
        $this->setExpectedException($this->getExceptionNamespace('MalformedAddressException'), '', 230);

        $mock = new MockHandler([
            new Response(500)
        ]);

        $this->getParser($mock)->getParcelshopsNearAddress('unknown address', 10000);
    }

    public function test_get_nearst_parcels_malformed()
    {
        $this->setExpectedException($this->getExceptionNamespace('MalformedAddressException'), '', 230);
        $mock = new MockHandler([
            new Response(200, [], $this->getReturnXml('nearst_malformed.xml'))
        ]);
        $this->getParser($mock)->getParcelshopsNearAddress('correct address', 1000);
    }

    public function test_get_nearst_parcel()
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getReturnXml('nearstparcels.xml'))
        ]);

        $parcels = $this->getParser($mock)->getParcelshopsNearAddress('correct address', 1000);
        $this->assertCount(2, $parcels);
        foreach($parcels as $parcel) {
            $this->assertInstanceOf('Lsv\GlsDk\Entity\Parcelshop', $parcel);
        }
    }

}

<?php

use Resources\Action\Scrape;

class ScrapeTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->obj = new Scrape();
    }

    public function setSourceDataProvider()
    {
        return array(
            array('https://testurl.co.uk/a/b/c'),
            array('https://testurl.co.uk/Test_Test.html'),
            array('https://testurl.co.uk/Test-Test'),
        );
    }

    /**
     * @dataProvider setSourceDataProvider
     * string $source
     */
    public function testSetSource($source)
    {
        $class      = new ReflectionClass($this->obj);
        $property   = $class->getProperty('source');
        $property->setAccessible(true);

        $this->obj->setSource($source);
        $this->assertEquals($source, $property->getValue($this->obj));
    }

    public function testGetScrapedDataException()
    {
        try {
            $this->obj->getScrapedData();
        } catch (Exception $e){
            $this->assertEquals('No URL parsed',$e->getMessage());
        }
    }
}
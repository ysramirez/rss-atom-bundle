<?php

namespace Debril\RssAtomBundle\Protocol;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-27 at 00:18:14.
 */
class FeedReaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var FeedReader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FeedReader(new \Debril\RssAtomBundle\Driver\FileDriver);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::__construct
     */
    public function testConstruct()
    {
        $reader = new FeedReader(new \Debril\RssAtomBundle\Driver\FileDriver);

        $this->assertAttributeInstanceOf("\Debril\RssAtomBundle\Driver\FileDriver", 'driver', $reader);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::addParser
     */
    public function testAddParser()
    {
        $parser = new Parser\AtomParser;
        $this->object->addParser($parser);

        $this->assertAttributeEquals(array($parser), 'parsers', $this->object);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::getDriver
     * @todo   Implement testGetDriver().
     */
    public function testGetDriver()
    {
        $this->assertInstanceOf(
                "\Debril\RssAtomBundle\Driver\HttpDriver", $this->object->getDriver()
        );
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::getFeedContent
     * @expectedException Debril\RssAtomBundle\Protocol\Parser\ParserException
     */
    public function testGetFeedContentException()
    {
        $url = dirname(__FILE__) . '/../../Resources/sample-rss.xml';

        $this->object->getFeedContent($url, new \DateTime);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::getFeedContent
     */
    public function testGetRssFeedContent()
    {
        $url = dirname(__FILE__) . '/../../Resources/sample-rss.xml';

        $this->object->addParser(new Parser\RssParser);
        $date = \DateTime::createFromFormat("Y-m-d", "2005-10-10");
        $feed = $this->object->getFeedContent($url, $date);

        $this->assertInstanceOf("\Debril\RssAtomBundle\Protocol\FeedContent", $feed);

        $item = current($feed->getItems());
        $this->assertInstanceOf("\Debril\RssAtomBundle\Protocol\Item", $item);

        $this->assertNotNull($item->getId());
        $this->assertNotNull($item->getLink());
        $this->assertNotNull($item->getTitle());
        $this->assertNotNull($item->getSummary());
        $this->assertInstanceOf("\DateTime", $item->getUpdated());
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::getFeedContent
     */
    public function testGetAtomFeedContent()
    {
        $url = dirname(__FILE__) . '/../../Resources/sample-atom.xml';
        $this->object->addParser(new Parser\AtomParser);

        $date = \DateTime::createFromFormat("Y-m-d", "2002-10-10");
        $feed = $this->object->getFeedContent($url, $date);

        $this->assertInstanceOf("\Debril\RssAtomBundle\Protocol\FeedContent", $feed);

        $item = current($feed->getItems());
        $this->assertInstanceOf("\Debril\RssAtomBundle\Protocol\Item", $item);

        $this->assertNotNull($item->getId());
        $this->assertNotNull($item->getLink());
        $this->assertNotNull($item->getTitle());
        $this->assertNotNull($item->getSummary());
        $this->assertInstanceOf("\DateTime", $item->getUpdated());
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::getResponse
     */
    public function testGetResponse()
    {
        $url = dirname(__FILE__) . '/../../Resources/sample-atom.xml';
        $this->object->addParser(new Parser\AtomParser);
        $response = $this->object->getResponse($url, new \DateTime);

        $this->assertInstanceOf("Debril\RssAtomBundle\Driver\HttpDriverResponse", $response);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::parseBody
     */
    public function testParseBody()
    {
        $url = dirname(__FILE__) . '/../../Resources/sample-rss.xml';
        $this->object->addParser(new Parser\RssParser);

        $date = new \DateTime;
        $response = $this->object->getResponse($url, $date);

        $feed = $this->object->parseBody($response, $date);

        $this->assertInstanceOf("\Debril\RssAtomBundle\Protocol\FeedContent", $feed);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::getAccurateParser
     */
    public function testGetAccurateParser()
    {
        $this->object->addParser(new Parser\RssParser);
        $this->object->addParser(new Parser\AtomParser);

        $url = dirname(__FILE__) . '/../../Resources/sample-rss.xml';

        $rssBody = $this->object->getResponse($url, new \DateTime)->getBody();

        $this->assertInstanceOf(
                "Debril\RssAtomBundle\Protocol\Parser\RssParser", $this->object->getAccurateParser(new \SimpleXMLElement($rssBody))
        );

        $url = dirname(__FILE__) . '/../../Resources/sample-atom.xml';

        $atomBody = $this->object->getResponse($url, new \DateTime)->getBody();

        $this->assertInstanceOf(
                "Debril\RssAtomBundle\Protocol\Parser\AtomParser", $this->object->getAccurateParser(new \SimpleXMLElement($atomBody))
        );
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::getAccurateParser
     * @expectedException Debril\RssAtomBundle\Protocol\Parser\ParserException
     */
    public function testGetAccurateParserException()
    {
        $url = dirname(__FILE__) . '/../../Resources/sample-rss.xml';
        $rssBody = $this->object->getResponse($url, new \DateTime)->getBody();
        $this->object->getAccurateParser(new \SimpleXMLElement($rssBody));
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\FeedReader::parseBody
     * @expectedException Debril\RssAtomBundle\Protocol\FeedCannotBeReadException
     */
    public function testParseBody304()
    {
        $mock = $this->getMock("\Debril\RssAtomBundle\Driver\HttpCurlDriver");

        $response = new \Debril\RssAtomBundle\Driver\HttpDriverResponse();
        $response->setHttpCode(304);

        $mock->expects($this->any())
                ->method('getResponse')
                ->will($this->returnValue($response));

        $reader = new FeedReader($mock);

        $reader->getFeedContent('http://afakeurl', new \DateTime);
    }

}

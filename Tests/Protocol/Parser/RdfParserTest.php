<?php

namespace Debril\RssAtomBundle\Protocol\Parser;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-27 at 00:26:56.
 */
class RdfParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RdfParser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RdfParser;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\RdfParser::canHandle
     */
    public function testCannotHandle()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-atom.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->assertFalse($this->object->canHandle($xmlBody));
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\RdfParser::canHandle
     */
    public function testCanHandle()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-rdf.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->assertTrue($this->object->canHandle($xmlBody));
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\RdfParser::checkBodyStructure
     * @expectedException \Debril\RssAtomBundle\Protocol\Parser\ParserException
     */
    public function testParseError()
    {
        $file = dirname(__FILE__) . '/../../../Resources/truncated-rss.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $filters = array(new \Debril\RssAtomBundle\Protocol\Filter\ModifiedSince(new \DateTime()));
        $this->object->parse($xmlBody, new FeedContent, $filters);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\RdfParser::parseBody
     */
    public function testParse()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-rdf.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));

        $date = \DateTime::createFromFormat("Y-m-d", "2005-10-10");
        $filters = array(new \Debril\RssAtomBundle\Protocol\Filter\ModifiedSince($date));
        $feed = $this->object->parse($xmlBody, new FeedContent, $filters);

        $this->assertInstanceOf("Debril\RssAtomBundle\Protocol\FeedIn", $feed);

        $this->assertNotNull($feed->getPublicId(), "feed->getPublicId() should not return an empty value");

        $this->assertGreaterThan(0, $feed->getItemsCount());
        $this->assertInstanceOf("\DateTime", $feed->getLastModified());
        $this->assertNotNull($feed->getLink());
        $this->assertNotNull($feed->getTitle());
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::setDateFormats
     * @covers Debril\RssAtomBundle\Protocol\Parser\RdfParser::__construct
     */
    public function testSetDateFormats()
    {
        $default = array(
            \DateTime::RFC3339,
            \DateTime::RSS,
        );

        $this->object->setdateFormats($default);
        $this->assertEquals($default, $this->readAttribute($this->object, 'dateFormats'));
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::guessDateFormat
     */
    public function testGuessDateFormat()
    {
        $default = array(
            \DateTime::RFC3339,
            \DateTime::RSS,
        );

        $this->object->setdateFormats($default);

        $date = 'Mon, 06 Sep 2009 16:45:00 GMT';
        $format = $this->object->guessDateFormat($date);

        $this->assertEquals(\DateTime::RSS, $format);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::guessDateFormat
     * @expectedException \Debril\RssAtomBundle\Protocol\Parser\ParserException
     */
    public function testGuessDateFormatException()
    {
        $default = array(
            \DateTime::RFC3339,
            \DateTime::RSS,
        );

        $this->object->setdateFormats($default);

        $date = '2003-13T18:30:02Z';
        $this->object->guessDateFormat($date);
    }

}

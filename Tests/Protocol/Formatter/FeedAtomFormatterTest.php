<?php

namespace Debril\RssAtomBundle\Protocol\Formatter;

use \Debril\RssAtomBundle\Protocol\Parser\FeedContent;
use \Debril\RssAtomBundle\Protocol\Parser\Item;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-02-12 at 21:51:18.
 */
class FeedAtomFormatterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var FeedAtomFormatter
     */
    protected $object;

    /**
     * @var FeedContent
     */
    protected $feed;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FeedAtomFormatter;

        $this->feed = new FeedContent();

        $this->feed->setId('feed id');
        $this->feed->setLink('http://example.com');
        $this->feed->setTitle('feed title');
        $this->feed->setSubtitle('feed subtitle');
        $this->feed->setLastModified(new \DateTime);

        $item = new Item;
        $item->setId('item id');
        $item->setLink('http://example.com/1');
        $item->setSummary('lorem ipsum');
        $item->setTitle('title 1');
        $item->setUpdated(new \DateTime);

        $this->feed->addItem($item);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Formatter\FeedAtomFormatter::toString
     */
    public function testToString()
    {
        $string = $this->object->toString($this->feed);

        $this->assertInternalType('string', $string);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Formatter\FeedAtomFormatter::toDom
     */
    public function testToDom()
    {
        $element = $this->object->toDom($this->feed);

        $this->assertInstanceOf("\DomDocument", $element);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Formatter\FeedAtomFormatter::getRootElement
     */
    public function testGetRootElement()
    {
        $element = $this->object->getRootElement();

        $this->assertInstanceOf("\DomDocument", $element);
        $this->assertEquals('feed', $element->firstChild->nodeName);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Formatter\FeedAtomFormatter::setMetas
     */
    public function testSetMetas()
    {
        $element = $this->object->getRootElement();

        $this->object->setMetas($element, $this->feed);
        $this->assertInstanceOf("\DomDocument", $element);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Formatter\FeedAtomFormatter::setEntries
     */
    public function testSetEntries()
    {
        $element = $this->object->getRootElement();

        $this->object->setEntries($element, $this->feed);

        foreach ($element->childNodes as $entry)
        {
            $this->assertInstanceOf("\DomNode", $entry);
        }
    }

}

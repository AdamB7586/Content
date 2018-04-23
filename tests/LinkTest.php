<?php
namespace Link\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Content\Link;

class LinkTest extends TestCase {
    protected $db;
    protected $links;

    public function setUp() {
        $this->db = new Database($GLOBALS['hostname'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        if(!$this->db->isConnected()) {
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        $this->db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/mysql_database.sql'));
        $this->db->query(file_get_contents(dirname(__FILE__).'/sample_data/links.sql'));
        $this->links = new Link($this->db, dirname(__FILE__).'/uploads/');
    }
    
    public function tearDown() {
        $this->db = null;
        $this->links = null;
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::setLinkTable
     * @covers Content\Link::getLinkTable
     */
    public function testChangeDatabaseTable() {
        $this->assertEquals('links', $this->links->getLinkTable());
        $this->assertObjectHasAttribute('siteID', $this->links->setLinkTable('my_links_table'));
        $this->assertEquals('my_links_table', $this->links->getLinkTable());
        $this->assertObjectHasAttribute('siteID', $this->links->setLinkTable(42));
        $this->assertNotEquals(42, $this->links->getLinkTable());
        $this->assertObjectHasAttribute('siteID', $this->links->setLinkTable(false));
        $this->assertNotEquals(false, $this->links->getLinkTable());
        $this->assertEquals('my_links_table', $this->links->getLinkTable());
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::addLink
     * @covers Content\Link::getLinkInfo
     * @covers Content\Link::getSiteID
     * @covers Content\Link::getLinkTable
     */
    public function testAddLink() {
        $this->assertTrue($this->links->addLink(array('link' => 'https://www.google.co.uk', 'link_text' => 'Google UK')));
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::getLinkInfo
     * @covers Content\Link::getSiteID
     * @covers Content\Link::getLinkTable
     */
    public function testGetLinkInfo() {
        $this->markTestIncomplete();
    }

    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::editLink
     * @covers Content\Link::
     * @covers Content\Link::getSiteID
     * @covers Content\Link::getLinkTable
     */
    public function testEditLink() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::disableLink
     * @covers Content\Link::getSiteID
     * @covers Content\Link::getLinkTable
     */
    public function testChangeLinkStatus() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::deleteLink
     * @covers Content\Link::getSiteID
     * @covers Content\Link::getLinkTable
     */
    public function testDeleteLink() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::listLinks
     * @covers Content\Link::getSiteID
     * @covers Content\Link::getLinkTable
     */
    public function testListLinks() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::setImageFolder
     * @covers Content\Link::getImageFolder
     */
    public function testChangeImageUploadFolder() {
        $this->assertEquals('/tests/uploads/', $this->links->getImageFolder());
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::getSiteID
     * @covers Content\Link::setSiteID
     * @covers Content\Link::listLinks
     */
    public function testSiteID() {
        $this->assertEquals(3, count($this->links->listLinks()));
        $this->assertEmpty($this->links->getSiteID());
        $this->assertObjectHasAttribute('siteID', $this->links->setSiteID(1));
        $this->assertEquals(1, $this->links->getSiteID());
        $this->assertEquals(1, count($this->links->listLinks()));
    }
}

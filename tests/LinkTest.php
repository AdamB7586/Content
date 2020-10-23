<?php
namespace Link\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Configuration\Config;
use Content\Link;

class LinkTest extends TestCase
{
    protected $db;
    protected $links;

    public function setUp() : void
    {
        $this->db = new Database($GLOBALS['hostname'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        if (!$this->db->isConnected()) {
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        $this->db->query(file_get_contents(dirname(dirname(__FILE__)).'/vendor/adamb/config/database/database_mysql.sql'));
        $this->db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/mysql_database.sql'));
        $this->db->query(file_get_contents(dirname(__FILE__).'/sample_data/links.sql'));
        $this->links = new Link($this->db, new Config($this->db), dirname(__FILE__).'/uploads/');
    }
    
    public function tearDown() : void
    {
        $this->db = null;
        $this->links = null;
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::addLink
     * @covers Content\Link::getLinkInfo
     */
    public function testAddLink()
    {
        $this->assertTrue($this->links->addLink(array('link' => 'https://www.google.co.uk', 'link_text' => 'Google UK')));
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::getLinkInfo
     */
    public function testGetLinkInfo()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::editLink
     */
    public function testEditLink()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::disableLink
     */
    public function testChangeLinkStatus()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::deleteLink
     */
    public function testDeleteLink()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::listLinks
     */
    public function testListLinks()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Link::__construct
     * @covers Content\Link::setImageFolder
     * @covers Content\Link::getImageFolder
     */
    public function testChangeImageUploadFolder()
    {
        $origFolder = $this->links->getImageFolder();
        $this->links->setImageFolder(45645);
        $this->assertNotEquals(45645, $this->links->getImageFolder());
        $this->assertEquals($origFolder, $this->links->getImageFolder());
        $this->links->setImageFolder('/images/links/');
        $this->assertEquals('/images/links/', $this->links->getImageFolder());
        $this->assertNotEquals($this->links->getImageFolder(), $origFolder);
    }
}

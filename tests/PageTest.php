<?php
namespace Content\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Content\Page;

class PageTest extends TestCase {
    protected $db;
    protected $page;

    public function setUp() {
        $this->db = new Database($GLOBALS['hostname'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        if(!$this->db->isConnected()) {
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        $this->db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/mysql_database.sql'));
        $this->db->query(file_get_contents(dirname(__FILE__).'/sample_data/pages.sql'));
        $this->page = new Page($this->db);
    }
    
    public function tearDown() {
        $this->db = null;
        $this->page = null;
    }
    
    /**
     * @covers Content\Page::__construct
     * @covers Content\Page::getContentTable
     * @covers Content\Page::setContentTable
     */
    public function testChangeDatabaseTable() {
        $this->assertEquals('pages', $this->page->getContentTable());
        $this->assertObjectHasAttribute('siteID', $this->page->setContentTable('my_pages_table'));
        $this->assertEquals('my_pages_table', $this->page->getContentTable());
        $this->assertObjectHasAttribute('siteID', $this->page->setContentTable(42));
        $this->assertNotEquals(42, $this->page->getContentTable());
        $this->assertObjectHasAttribute('siteID', $this->page->setContentTable(false));
        $this->assertNotEquals(false, $this->page->getContentTable());
        $this->assertEquals('my_pages_table', $this->page->getContentTable());
    }
    
    /**
     * @covers Content\Page::__construct
     * @covers Content\Page::
     * @covers Content\Page::
     */
    public function testAddPage() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Page::__construct
     * @covers Content\Page::
     * @covers Content\Page::
     */
    public function testEditPage() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Page::__construct
     * @covers Content\Page::
     * @covers Content\Page::
     */
    public function testGetPage(){
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Page::__construct
     * @covers Content\Page::
     * @covers Content\Page::
     */
    public function testDisablePage() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Page::__construct
     * @covers Content\Page::
     * @covers Content\Page::
     */
    public function testDeletePage() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers Content\Page::__construct
     * @covers Content\Page::
     * @covers Content\Page::
     */
    public function testSearchPages(){
        $this->markTestIncomplete();
    }
}

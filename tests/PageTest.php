<?php
namespace Content\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Configuration\Config;
use Content\Page;

class PageTest extends TestCase {
    protected $db;
    protected $page;

    public function setUp() : void {
        $this->db = new Database($GLOBALS['hostname'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        if(!$this->db->isConnected()) {
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        $this->db->query(file_get_contents(dirname(dirname(__FILE__)).'/vendor/adamb/config/database/database_mysql.sql'));
        $this->db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/mysql_database.sql'));
        $this->db->query(file_get_contents(dirname(__FILE__).'/sample_data/pages.sql'));
        $this->page = new Page($this->db, new Config($this->db));
    }
    
    public function tearDown() : void {
        $this->db = null;
        $this->page = null;
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

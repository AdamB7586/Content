<?php

namespace Content\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Content\Page;
use Content\Links;

class PageTest extends TestCase{
    protected $db;
    protected $page;
    protected $links;

    public function setUp(){
        $this->db = new Database($GLOBALS['hostname'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        if(!$this->db->isConnected()) {
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        $this->page = new Page($this->db);
        $this->links = new Links($this->db);
    }
    
    public function tearDown(){
        $this->db = null;
        $this->page = null;
        $this->links = null;
    }
    
    public function testExample(){
        $this->markTestIncomplete();
    }
}

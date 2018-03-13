<?php

namespace Content\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Content\Page;

class PageTest extends TestCase{
    protected $db;
    protected $page;

    public function setUp(){
        $this->db = new Database($GLOBALS['hostname'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        if(!$this->db->isConnected()) {
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        $this->page = new Page($this->db);
    }
    
    public function tearDown(){
        $this->db = null;
        $this->page = null;
    }
    
    public function testExample(){
        $this->markTestIncomplete();
    }
}

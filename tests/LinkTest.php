<?php

namespace Content\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Content\Links;

class LinkTest extends TestCase{
    protected $db;
    protected $links;

    public function setUp(){
        $this->db = new Database($GLOBALS['hostname'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        if(!$this->db->isConnected()) {
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        $this->links = new Links($this->db);
    }
    
    public function tearDown(){
        $this->db = null;
        $this->links = null;
    }
    
}

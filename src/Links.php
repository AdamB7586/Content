<?php

namespace Content;

use DBAL\Database;
use ImgUpload\ImageUpload;

class Links {
    protected $db;
    protected $siteID;

    public function __construct(Database $db, $siteID = false) {
        $this->db = $db;
        if(is_numeric($siteID)){
            $this->siteID = intval($siteID);
        }
    }
    
    
}

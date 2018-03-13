<?php

namespace Content;

use DBAL\Database;
use ImgUpload\ImageUpload;

class Link {
    protected $db;
    protected $image;
    protected $siteID;
    
    protected $link_table = 'pages';

    /**
     * 
     * @param Database $db
     * @param int|false $siteID
     */
    public function __construct(Database $db, $siteID = false) {
        $this->db = $db;
        if(is_numeric($siteID)) {
            $this->siteID = intval($siteID);
        }
        $this->image = new ImageUpload();
    }
    
    /**
     * 
     * @param type $table
     * @return $this
     */
    public function setLinkTable($table){
        if(is_string($table)){
            $this->link_table = trim($table);
        }
        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function getLinkTable(){
        return $this->link_table;
    }
    
    /**
     * 
     * @param type $linkInfo
     * @param type $image
     * @return type
     */
    public function addLink($linkInfo, $image = NULL) {
        return $this->db->insert($this->getLinkTable(), $linkInfo);
    }
    
    /**
     * 
     * @param type $linkID
     * @param type $linkInfo
     * @param type $image
     * @return boolean
     */
    public function editLink($linkID, $linkInfo, $image = NULL) {
        if(is_numeric($linkID) && is_array($linkInfo)){
            return $this->db->update($this->getLinkTable(), $linkInfo, array('id' => $linkID));
        }
        return false;
    }
    
    /**
     * 
     * @param type $linkID
     * @return boolean
     */
    public function deleteLink($linkID) {
        if(is_numeric($linkID)){
            return $this->db->delete($this->getLinkTable(), array('id' => $linkID));
        }
        return false;
    }
    
    /**
     * 
     * @param type $linkID
     * @param type $status
     * @return boolean
     */
    public function disableLink($linkID, $status = 0) {
        if(is_numeric($linkID) && is_numeric($status)){
            return $this->db->update($this->getLinkTable(), array('active' => $status), array('id' => $linkID));
        }
        return false;
    }
}

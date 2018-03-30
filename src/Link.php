<?php

namespace Content;

use DBAL\Database;
use ImgUpload\ImageUpload;

class Link {
    protected $db;
    protected $image;
    protected $siteID;
    
    protected $link_table = 'links';

    /**
     * Constructor
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
     * Sets the table name where the links will be stored
     * @param string $table This should be the table where you want the links to be stored
     * @return $this
     */
    public function setLinkTable($table){
        if(is_string($table) && !empty(trim($table))){
            $this->link_table = trim($table);
        }
        return $this;
    }
    
    /**
     * Return the link table string
     * @return string
     */
    public function getLinkTable(){
        return $this->link_table;
    }
    
    /**
     * Set the folder where the images will be uploaded to 
     * @param string $folder This should be the name of the folder that the main images will be uploaded to
     * @return $this
     */
    public function setImageFolder($folder){
        $this->image->setImageFolder($folder);
        return $this;
    }
    
    /**
     * 
     * @param array $linkInfo
     * @param array $image
     * @return boolean If successfully added will return true else returns false
     */
    public function addLink($linkInfo, $image = NULL) {
        return $this->db->insert($this->getLinkTable(), $linkInfo);
    }
    
    /**
     * Edit a link and its information
     * @param int $linkID
     * @param array $linkInfo
     * @param array $image
     * @return boolean If successfully updated will return true else returns false
     */
    public function editLink($linkID, $linkInfo, $image = NULL) {
        if(is_numeric($linkID) && is_array($linkInfo)){
            return $this->db->update($this->getLinkTable(), $linkInfo, array('id' => $linkID));
        }
        return false;
    }
    
    /**
     * Delete a link and any associated files
     * @param int $linkID This should be the unique link ID
     * @return boolean If successfully deleted will return true else returns false
     */
    public function deleteLink($linkID) {
        if(is_numeric($linkID)){
            return $this->db->delete($this->getLinkTable(), array('id' => $linkID));
        }
        return false;
    }
    
    /**
     * Change the active status of a link in the database
     * @param int $linkID This should be the unique link ID
     * @param int $status This should be the new status to assign to the link
     * @return boolean If successfully updated will return true else returns false
     */
    public function disableLink($linkID, $status = 0) {
        if(is_numeric($linkID) && is_numeric($status)){
            return $this->db->update($this->getLinkTable(), array('active' => $status), array('id' => $linkID));
        }
        return false;
    }
}

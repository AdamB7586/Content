<?php

namespace Content;

use DBAL\Database;
use ImgUpload\ImageUpload;

class Link {
    protected $db;
    protected $image;
    public $siteID;
    
    protected $link_table = 'links';

    /**
     * Constructor
     * @param Database $db
     * @param int|false $siteID
     */
    public function __construct(Database $db, $imageFolder = '/images/links', $siteID = false) {
        $this->db = $db;
        if(is_numeric($siteID)) {
            $this->siteID = intval($siteID);
        }
        $this->image = new ImageUpload();
        $this->setImageFolder($imageFolder);
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
     * Sets the site ID if multiple site contents are stored within the same database
     * @param int $siteID This should be the site ID for the site you are getting content for
     * @return $this
     */
    public function setSiteID($siteID){
        if(is_numeric($siteID)){
            $this->siteID = intval($siteID);
        }
        return $this;
    }
    
    /**
     * Returns the site ID if it is set else will return false
     * @return int|boolean If the site ID is set will return the ID else will return false
     */
    public function getSiteID(){
        if(is_int($this->siteID)){
            return $this->siteID;
        }
        return false;
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
     * Returns the image folder
     * @return string Returns the image folder string
     */
    public function getImageFolder(){
        return $this->image->getImageFolder();
    }
    
    /**
     * Returns a list of all of the relevant links
     * @param boolean $active If you only want to display active links set to true (default) else set to false
     * @return array|false If any link items exist they will be returned as an array else will return false if no links exist
     */
    public function listLinks($active = true){
        $where = [];
        if($active === true){$where['active'] = 1;}
        if($this->getSiteID() !== false){$where['site_id'] = $this->getSiteID();}
        return $this->db->selectAll($this->getLinkTable(), $where);
    }
    
    /**
     * Returns the information for a single link
     * @param int $linkID This should be the unique link ID
     * @return array|boolean If the link exists it will be returned as an array else will return false
     */
    public function getLinkInfo($linkID) {
        if(is_numeric($linkID)){
            if($this->getSiteID() !== false){$where['site_id'] = $this->getSiteID();}
            return $this->db->select($this->getLinkTable(), array_merge($where, array('id' => $linkID)));
        }
        return false;
    }
    
    /**
     * Add a link to the database
     * @param array $linkInfo This should be the link information that you are adding
     * @param array $image If you would like to upload an image file include the $_FILES information here
     * @return boolean If successfully added will return true else returns false
     */
    public function addLink($linkInfo, $image = NULL) {
        $imageInfo = [];
        $imageupload = false;
        if(is_array($image)){$imageupload = $this->image->uploadImage($image);}
        if($imageupload === true && file_exists($this->image->getImageFolder().$image['name'])){
            list($width, $height) = getimagesize($this->image->getImageFolder().$image['name']);
            $imageInfo = array('image' => $image['name'], 'image_width' => $width, 'image_height' => $height);
        }
        if($this->getSiteID() !== false){$linkInfo['site_id'] = $this->getSiteID();}
        return $this->db->insert($this->getLinkTable(), array_merge($linkInfo, $imageInfo));
    }
    
    /**
     * Edit a link and its information
     * @param int $linkID This should be the unique link id in the database 
     * @param array $linkInfo This should be the link information that you are editing
     * @param array $image If you would like to upload an image file include the $_FILES information here
     * @return boolean If successfully updated will return true else returns false
     */
    public function editLink($linkID, $linkInfo, $image = NULL) {
        if(is_numeric($linkID) && is_array($linkInfo)){
            $imageInfo = [];
            $imageupload = false;
            if(is_array($image)){$imageupload = $this->image->uploadImage($image);}
            if($imageupload === true && file_exists($this->image->getImageFolder().$image['name'])){
                list($width, $height) = getimagesize($this->image->getImageFolder().$image['name']);
                $imageInfo = array('image' => $image['name'], 'image_width' => $width, 'image_height' => $height);
            }
            if($this->getSiteID() !== false){$where['site_id'] = $this->getSiteID();}else{$where = [];}
            return $this->db->update($this->getLinkTable(), array_merge($linkInfo, $imageInfo), array_merge($where, array('id' => $linkID)));
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
            if($this->getSiteID() !== false){$where['site_id'] = $this->getSiteID();}else{$where = [];}
            return $this->db->delete($this->getLinkTable(), array_merge($where, array('id' => $linkID)));
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
            if($this->getSiteID() !== false){$where['site_id'] = $this->getSiteID();}else{$where = [];}
            return $this->db->update($this->getLinkTable(), array('active' => $status), array_merge($where, array('id' => $linkID)));
        }
        return false;
    }
}

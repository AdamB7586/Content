<?php

namespace Content;

use DBAL\Database;
use Configuration\Config;
use ImgUpload\ImageUpload;

class Link {
    protected $db;
    protected $config;
    protected $image;

    /**
     * Constructor
     * @param DBAL\Database $db
     * @param Configuration\Config $config
     * @param string $imageFolder
     */
    public function __construct(Database $db, Config $config, $imageFolder = '/images/links') {
        $this->db = $db;
        $this->config = $config;
        $this->image = new ImageUpload();
        $this->setImageFolder($imageFolder);
    }
    
    /**
     * Set the folder where the images will be uploaded to 
     * @param string $folder This should be the name of the folder that the main images will be uploaded to
     * @return $this
     */
    public function setImageFolder($folder){
        if(is_string($folder)){
            $this->image->setImageFolder($folder);
        }
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
    public function listLinks($active = true, $additional = []){
        $where = [];
        if($active === true){$where['active'] = 1;}
        return $this->db->selectAll($this->config->links_table, array_merge($additional, $where));
    }
    
    /**
     * Returns the information for a single link
     * @param int $linkID This should be the unique link ID
     * @return array|boolean If the link exists it will be returned as an array else will return false
     */
    public function getLinkInfo($linkID, $additional = []) {
        if(is_numeric($linkID)){
            return $this->db->select($this->config->links_table, array_merge($additional, ['id' => $linkID]));
        }
        return false;
    }
    
    /**
     * Add a link to the database
     * @param array $linkInfo This should be the link information that you are adding
     * @param array $image If you would like to upload an image file include the $_FILES information here
     * @return boolean If successfully added will return true else returns false
     */
    public function addLink($linkInfo, $image = NULL, $additional = []) {
        $imageInfo = [];
        $imageupload = false;
        if(is_array($image)){$imageupload = $this->image->uploadImage($image);}
        if($imageupload === true && file_exists($this->image->getImageFolder().$image['name'])){
            list($width, $height) = getimagesize($this->image->getImageFolder().$image['name']);
            $imageInfo = ['image' => $image['name'], 'image_width' => $width, 'image_height' => $height];
        }
        return $this->db->insert($this->config->links_table, array_merge($additional, $linkInfo, $imageInfo));
    }
    
    /**
     * Edit a link and its information
     * @param int $linkID This should be the unique link id in the database 
     * @param array $linkInfo This should be the link information that you are editing
     * @param array $image If you would like to upload an image file include the $_FILES information here
     * @return boolean If successfully updated will return true else returns false
     */
    public function editLink($linkID, $linkInfo, $image = NULL, $additional = []) {
        if(is_numeric($linkID) && is_array($linkInfo)){
            $imageInfo = [];
            $imageupload = false;
            if(is_array($image)){$imageupload = $this->image->uploadImage($image);}
            if($imageupload === true && file_exists($this->image->getImageFolder().$image['name'])){
                list($width, $height) = getimagesize($this->image->getImageFolder().$image['name']);
                $imageInfo = ['image' => $image['name'], 'image_width' => $width, 'image_height' => $height];
            }
            return $this->db->update($this->config->links_table, array_merge($linkInfo, $imageInfo), array_merge($additional, ['id' => $linkID]));
        }
        return false;
    }
    
    /**
     * Delete a link and any associated files
     * @param int $linkID This should be the unique link ID
     * @return boolean If successfully deleted will return true else returns false
     */
    public function deleteLink($linkID, $additional = []) {
        if(is_numeric($linkID)){
            return $this->db->delete($this->config->links_table, array_merge($additional, ['id' => $linkID]));
        }
        return false;
    }
    
    /**
     * Change the active status of a link in the database
     * @param int $linkID This should be the unique link ID
     * @param int $status This should be the new status to assign to the link
     * @return boolean If successfully updated will return true else returns false
     */
    public function changeLinkStatus($linkID, $status = 0, $additional = []) {
        if(is_numeric($linkID) && is_numeric($status)){
            return $this->db->update($this->config->links_table, ['active' => $status], array_merge($additional, ['id' => $linkID]));
        }
        return false;
    }
}

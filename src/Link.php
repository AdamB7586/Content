<?php

namespace Content;

use DBAL\Database;
use Configuration\Config;
use ImgUpload\ImageUpload;
use Gumlet\ImageResize;

class Link {
    protected $db;
    protected $config;
    protected $image;
    
    public $maxImageWidth = false;
    public $storeFolder = false;

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
    public function setImageFolder($folder) {
        if(is_string($folder)){
            $this->image->setImageFolder($folder);
        }
        return $this;
    }
    
    /**
     * Returns the image folder
     * @return string Returns the image folder string
     */
    public function getImageFolder() {
        return $this->image->getImageFolder();
    }
    
    /**
     * Sets the maximum allows width for an image
     * @param int|false $width This should be the maximum allowed with for the image if it need to be set else set to false for no maximum
     * @return $this
     */
    public function setMaxImageWidth($width) {
        if(is_numeric($width) || $width === false) {
            $this->image->setMinWidth($width);
            $this->maxImageWidth = $width;
        }
        return $this;
    }
    
    /**
     * Returns the maximum image width
     * @return int|false This will return the maximum image width if set else will return false
     */
    public function getMaxImageWidth() {
        if(is_numeric($this->maxImageWidth)){
            return $this->maxImageWidth;
        }
        return false;
    }
    
    /**
     * Sets the option if the folder location should be stored in the database with the filename
     * @param boolean $boolean This should be either true of false
     * @return $this
     */
    public function setStoreFolder($boolean = true){
        if(is_bool($boolean)){
            $this->storeFolder = $boolean;
        }
        return $this;
    }
    
    /**
     * Returns a boolean whether to store the folder location in the database or not
     * @return boolean Returns true if the folder should be stored else returns false
     */
    public function getStoreFolder(){
        return $this->storeFolder;
    }

    /**
     * Returns a list of all of the relevant links
     * @param boolean $active If you only want to display active links set to true (default) else set to false
     * @return array|false If any link items exist they will be returned as an array else will return false if no links exist
     */
    public function listLinks($active = true, $additional = []) {
        $where = [];
        if($active === true){$where['active'] = 1;}
        return $this->db->selectAll($this->config->table_links, array_merge($additional, $where));
    }
    
    /**
     * Returns the information for a single link
     * @param int $linkID This should be the unique link ID
     * @return array|boolean If the link exists it will be returned as an array else will return false
     */
    public function getLinkInfo($linkID, $additional = []) {
        if(is_numeric($linkID)){
            return $this->db->select($this->config->table_links, array_merge($additional, ['id' => $linkID]));
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
        return $this->db->insert($this->config->table_links, array_merge($additional, $linkInfo, $this->imageUpload($image)));
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
            return $this->db->update($this->config->table_links, array_merge($linkInfo, $this->imageUpload($image)), array_merge($additional, ['id' => $linkID]));
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
            return $this->db->delete($this->config->table_links, array_merge($additional, ['id' => $linkID]));
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
            return $this->db->update($this->config->table_links, ['active' => $status], array_merge($additional, ['id' => $linkID]));
        }
        return false;
    }
    
    /**
     * Removed the image from the link information and deletes the image file from the server
     * @param int $linkID This should be the unique link ID 
     * @return boolean Returns true if successfully removed the image information
     */
    public function deleteImage($linkID) {
        $info = $this->getLinkInfo($linkID);
        if(file_exists($this->image->getRootFolder().($this->getStoreFolder() ? $info['image'] : '/'.str_replace('\\', '/', $this->image->getImageFolder()).$info['name']))){
            unlink($this->image->getRootFolder().($this->getStoreFolder() ? $info['image'] : '/'.str_replace('\\', '/', $this->image->getImageFolder()).$info['name']));
        }
        return $this->db->update($this->config->table_links, ['image' => NULL, 'image_width' => 0, 'image_height' => 0], ['id' => $linkID]);
    }
    
    /**
     * Upload an image if one is set
     * @param array|NULL $image This should be the image information array
     * @return array Returns the image information to insert into the database
     */
    protected function imageUpload($image) {
        $imageInfo = [];
        $imageupload = false;
        if(is_array($image)){
            if($this->getMaxImageWidth()){
                $resize = new ImageResize($image['tmp_name']);
                $resize->resizeToWidth($this->getMaxImageWidth());
                $resize->save($this->getImageFolder().$image['name']);
                $imageupload = true;
            }
            else{
                $imageupload = $this->image->uploadImage($image);
            }
        }
        if($imageupload === true && file_exists($this->image->getImageFolder().$image['name'])){
            list($width, $height) = getimagesize($this->image->getImageFolder().$image['name']);
            $imageInfo = ['image' => ($this->getStoreFolder() ? '/'.str_replace('\\', '/', $this->image->getImageFolder()).$image['name'] : $image['folder']), 'image_width' => $width, 'image_height' => $height];
        }
        return $imageInfo;
    }
}

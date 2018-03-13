<?php

namespace Content;

use DBAL\Database;
use Sunra\PhpSimple\HtmlDomParser;
use Content\Utilities\PageUtil;

class Page {
    protected $db;
    protected $htmlParser;
    protected $siteID;
    
    protected $content_table = 'pages';

    /**
     * 
     * @param Database $db
     * @param int $siteID
     */
    public function __construct(Database $db, $siteID = false) {
        $this->db = $db;
        $this->htmlParser = new HtmlDomParser();
        if(is_numeric($siteID)){
            $this->setSiteID($siteID);
        }
    }
    
    /**
     * Gets the content database name variable
     * @return string|boolean  If content table value is set the value will be returned else will return false
     */
    public function getContentTable(){
        if(!empty($this->content_table)){
            return $this->content_table;
        }
    }
    
    /**
     * Sets the content database name variable
     * @param string $table This should be the table name of the contents table
     * @return $this
     */
    public function setContentTable($table){
        if(!empty(trim($table))){
            $this->content_table = trim($table);
        }
        return $this;
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
     * Returns the page content
     * @param string $pageURI This should be th URI of the page you are retrieving content for
     * @param boolean $onlyActive If you only wish to retrieve active content then set this value to true
     * @return array|false If the content exists will return an array containing the information else will return false
     */
    public function getPage($pageURI, $onlyActive = true){
        $where = array();
        $where['url'] = $pageURI;
        if($onlyActive == true){$where['active'] = 1;}
        if($this->getSiteID() !== false){$where['site_id'] = $this->getSiteID();}
        return $this->db->select($this->getContentTable(), $where);
    }
    
    /**
     * Adds page content
     * @param array $content This should be the page content information as an array
     * @return boolean Returns true on success and false on failure
     */
    public function addPage($content){
        if(is_array($content) && $this->checkIfURLExists($content['url']) === 0){
            return $this->db->insert($this->getContentTable(), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('title' => $content['title'], 'content' => $content['content'], 'description' => $content['description'], 'url' => PageUtil::cleanURL($content['url']))));
        }
    }
    
    /**
     * Updates page content
     * @param int $pageID This should be the unique page ID of the page that you are updating
     * @param array $content This should be all of the page information as an array
     * @return boolean Returns true on success and false on failure
     */
    public function updatePage($pageID, $content = []){
        if(is_numeric($pageID) && is_array($content)){
            return $this->db->update($this->getContentTable(), array('title' => $content['title'], 'content' => $content['content'], 'description' => $content['description'], 'url' => PageUtil::cleanURL($content['url'])), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('id' => $pageID)), 1);
        }
        return false;
    }

    /**
     * Disables a page so it's no longer active
     * @param int $pageID This should be the unique page ID of the page you are disabling
     * @return boolean Returns true on success and false on failure
     */
    public function disablePage($pageID){
        if(is_numeric($pageID)){
            return $this->db->update($this->getContentTable(), array('active' => 0), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('id' => intval($pageID))), 1);
        }
        return false;
    }
    
    /**
     * Delete a given page
     * @param type $pageID This should be the unique page ID of the page you are deleting
     * @return boolean Returns true on success and false on failure
     */
    public function deletePage($pageID){
        if(is_numeric($pageID)){
            return $this->db->delete($this->getContentTable(), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('id' => intval($pageID))), 1);
        }
        return false;
    }
    
    /**
     * Search page content
     * @param string $search This should be the text you wish to search the content on
     * @return array|false If any information exists they will be returned as an array else will return false
     */
    public function searchPages($search){
        return $this->db->query("SELECT `title`, `content`, `url`, MATCH(`title`, `content`) AGAINST(:search) AS `score` FROM `{$this->getContentTable()}` WHERE `site_id` = :siteid AND MATCH(`title`,`content`) AGAINST(:search IN BOOLEAN MODE)", array(':siteid' => $this->siteID, ':search' => $search));
    }
    
    /**
     * Checks to see if a URL exists
     * @param string $url This should be the URL you are checking if it exists
     * @return int Will return the number of matching URLs (1 if exists and 0 if it doesn't)
     */
    protected function checkIfURLExists($url){
        return $this->db->count($this->getContentTable(), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('url' => PageUtil::cleanURL($url))));
    }
}

<?php

namespace Content;

use DBAL\Database;
use Configuration\Config;
use Content\Utilities\PageUtil;

class Page {
    protected $db;
    protected $config;
    protected $htmlParser;
    
    public $siteID;

    /**
     * 
     * @param Database $db
     * @param int $siteID
     */
    public function __construct(Database $db, Config $config, $siteID = false) {
        $this->db = $db;
        $this->config = $config;
        $this->htmlParser = new HtmlDomParser();
        if(is_numeric($siteID)){
            $this->setSiteID($siteID);
        }
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
    public function getPage($pageURI, $onlyActive = true, $additional = []){
        $where = array();
        $where['uri'] = $pageURI;
        if($onlyActive == true){$where['active'] = 1;}
        if($this->getSiteID() !== false){$where['site_id'] = $this->getSiteID();}
        return $this->db->select($this->config->content_table, array_merge($where, $additional));
    }
    
    /**
     * Adds page content
     * @param array $content This should be the page content information as an array
     * @return boolean Returns true on success and false on failure
     */
    public function addPage($content, $additional = []){
        if(is_array($content) && $this->checkIfURLExists($content['uri']) === 0){
            return $this->db->insert($this->config->content_table, array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('title' => $content['title'], 'content' => $content['content'], 'description' => $content['description'], 'uri' => PageUtil::cleanURL($content['uri'])), $additional));
        }
    }
    
    /**
     * Updates page content
     * @param int $pageID This should be the unique page ID of the page that you are updating
     * @param array $content This should be all of the page information as an array
     * @return boolean Returns true on success and false on failure
     */
    public function updatePage($pageID, $content = [], $additional = []){
        if(is_numeric($pageID) && is_array($content)){
            return $this->db->update($this->config->content_table, array('title' => $content['title'], 'content' => $content['content'], 'description' => $content['description'], 'uri' => PageUtil::cleanURL($content['uri'])), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('id' => $pageID)), 1);
        }
        return false;
    }

    /**
     * Disables a page so it's no longer active
     * @param int $pageID This should be the unique page ID of the page you are disabling
     * @return boolean Returns true on success and false on failure
     */
    public function changePageStatus($pageID, $status = 0, $additional = []){
        if(is_numeric($pageID) && is_numeric($status)){
            return $this->db->update($this->config->content_table, array('active' => $status), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('id' => intval($pageID)), $additional), 1);
        }
        return false;
    }
    
    /**
     * Delete a given page
     * @param type $pageID This should be the unique page ID of the page you are deleting
     * @return boolean Returns true on success and false on failure
     */
    public function deletePage($pageID, $additional = []){
        if(is_numeric($pageID)){
            return $this->db->delete($this->config->content_table, array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array_merge($additional, array('id' => intval($pageID)))), 1);
        }
        return false;
    }
    
    /**
     * Search page content
     * @param string $search This should be the text you wish to search the content on
     * @return array|false If any information exists they will be returned as an array else will return false
     */
    public function searchPages($search){
        return $this->db->query("SELECT `title`, `content`, `uri`, MATCH(`title`, `content`) AGAINST(:search) AS `score` FROM `{$this->config->content_table}` WHERE ".(s_numeric($this->getSiteID()) ? "`site_id` = :siteid AND " : "")."MATCH(`title`,`content`) AGAINST(:search IN BOOLEAN MODE)", array(':siteid' => $this->getSiteID(), ':search' => $search));
    }
    
    /**
     * Checks to see if a URL exists
     * @param string $uri This should be the URL you are checking if it exists
     * @return int Will return the number of matching URLs (1 if exists and 0 if it doesn't)
     */
    protected function checkIfURLExists($uri){
        return $this->db->count($this->config->content_table, array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('uri' => PageUtil::cleanURL($uri))));
    }
}

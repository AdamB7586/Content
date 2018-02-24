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
     * 
     * @return string|boolean
     */
    public function getContentTable(){
        if(!empty($this->content_table)){
            return $this->content_table;
        }
        return false;
    }
    
    /**
     * 
     * @param string $table
     * @return $this
     */
    public function setContentTable($table){
        if(!empty(trim($table))){
            $this->content_table = trim($table);
        }
        return $this;
    }
    
    /**
     * 
     * @param int $siteID
     * @return $this
     */
    public function setSiteID($siteID){
        if(is_numeric($siteID)){
            $this->siteID = intval($siteID);
        }
        return $this;
    }
    
    /**
     * 
     * @return int|boolean
     */
    public function getSiteID(){
        if(is_int($this->siteID)){
            return $this->siteID;
        }
        return false;
    }

    /**
     * 
     * @param type $pageURI
     * @param boolean $onlyActive
     * @return array|false
     */
    public function getPage($pageURI, $onlyActive = true){
        $where = array();
        $where['url'] = $pageURI;
        if($onlyActive == true){$where['active'] = 1;}
        if($this->getSiteID() !== false){$where['site_id'] = $this->getSiteID();}
        return $this->db->select($this->getContentTable(), $where);
    }
    
    /**
     * 
     * @param type $content
     * @return type
     */
    public function addPage($content){
        if(is_array($content) && $this->checkIfURLExists($content['url']) === 0){
            return $this->db->insert($this->getContentTable(), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('title' => $content['title'], 'content' => $content['content'], 'description' => $content['description'], 'url' => PageUtil::cleanURL($content['url']))));
        }
    }
    
    public function updatePage($pageID, $content = []){
        if(is_numeric($pageID) && is_array($content)){
            return $this->db->update($this->getContentTable(), array('title' => $content['title'], 'content' => $content['content'], 'description' => $content['description'], 'url' => PageUtil::cleanURL($content['url'])), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('id' => $pageID)), 1);
        }
        return false;
    }

    public function disablePage($pageID){
        if(is_numeric($pageID)){
            return $this->db->update($this->getContentTable(), array('active' => 0), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('id' => intval($pageID))), 1);
        }
        return false;
    }
    
    public function deletePage($pageID){
        if(is_numeric($pageID)){
            return $this->db->delete($this->getContentTable(), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('id' => intval($pageID))), 1);
        }
        return false;
    }
    
    public function searchPages($search){
        return $this->db->query("SELECT `title`, `content`, `url`, MATCH(`title`, `content`) AGAINST(:search) AS `score` FROM `{$this->getContentTable()}` WHERE `site_id` = :siteid AND MATCH(`title`,`content`) AGAINST(:search IN BOOLEAN MODE)", array(':siteid' => $this->siteID, ':search' => $search));
    }
    
    protected function checkIfURLExists($url){
        return $this->db->count($this->getContentTable(), array_merge(($this->getSiteID() !== false ? array('site_id' => $this->getSiteID()) : array()), array('url' => PageUtil::cleanURL($url))));
    }
}

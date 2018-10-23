<?php

namespace Content;

use DBAL\Database;
use Configuration\Config;
use Content\Utilities\PageUtil;
use Sunra\PhpSimple\HtmlDomParser;

class Page {
    protected $db;
    protected $config;
    protected $htmlParser;

    /**
     * 
     * @param Database $db
     * @param Config $config
     */
    public function __construct(Database $db, Config $config) {
        $this->db = $db;
        $this->config = $config;
        $this->htmlParser = new HtmlDomParser();
    }

    /**
     * Returns the page content
     * @param string $pageURI This should be the URI of the page you are retrieving content for
     * @param boolean $onlyActive If you only wish to retrieve active content then set this value to true
     * @param array $additional Any additional fields to limit the search should be entered as an array
     * @return array|false If the content exists will return an array containing the information else will return false
     */
    public function getPage($pageURI, $onlyActive = true, $additional = []){
        $where = [];
        $where['uri'] = $pageURI;
        if($onlyActive == true){$where['active'] = 1;}
        return $this->buildPageInfo($this->db->select($this->config->content_table, array_merge($where, $additional)));
    }
    
    /**
     * Returns the page content based on the page ID
     * @param int $pageID This should be the unique page ID
     * @return array|false If the page ID exists will return the page information as an array else will return false
     */
    public function getPageByID($pageID){
        return $this->buildPageInfo($this->db->select($this->config->content_table, ['page_id' => intval($pageID)]));
    }
    
    /**
     * Returns the formatted page information
     * @param array|false $page This should be the page information array if it exists
     * @return array|false The page information will be returned if it exists else false will be returned 
     */
    protected function buildPageInfo($page){
        if(is_array($page)) {
            if($page['additional'] !== NULL){
                $page['additional'] = unserialize($page['additional']);
            }
            return $page;
        }
        return false;
    }
    
    /**
     * Adds page content
     * @param array $content This should be the page content information as an array
     * @param array $additional Any additional items to add should be entered as an array
     * @return boolean Returns true on success and false on failure
     */
    public function addPage($content, $additional = []){
        if(is_array($content) && $this->checkIfURLExists($content['uri'], $additional) === 0){
            return $this->db->insert($this->config->content_table, array_merge(['title' => $content['title'], 'content' => $content['content'], 'description' => $content['description'], 'uri' => PageUtil::cleanURL($content['uri'])], $additional));
        }
    }
    
    /**
     * Updates page content
     * @param int $pageID This should be the unique page ID of the page that you are updating
     * @param array $content This should be all of the page information as an array
     * @param array $additional Any additional items to limit the update should be entered as an array
     * @return boolean Returns true on success and false on failure
     */
    public function updatePage($pageID, $content = [], $additional = []){
        if(is_numeric($pageID) && is_array($content)){
            return $this->db->update($this->config->content_table, array_merge(['title' => $content['title'], 'content' => $content['content'], 'description' => $content['description'], 'uri' => PageUtil::cleanURL($content['uri'])], $additional), ['page_id' => $pageID], 1);
        }
        return false;
    }

    /**
     * Disables a page so it's no longer active
     * @param int $pageID This should be the unique page ID of the page you are disabling
     * @param array $additional Any additional items to limit the update should be entered as an array
     * @return boolean Returns true on success and false on failure
     */
    public function changePageStatus($pageID, $status = 0, $additional = []){
        if(is_numeric($pageID) && is_numeric($status)){
            return $this->db->update($this->config->content_table, ['active' => $status], array_merge(['page_id' => intval($pageID)], $additional), 1);
        }
        return false;
    }
    
    /**
     * Delete a given page
     * @param int $pageID This should be the unique page ID of the page you are deleting
     * @param array $additional Any additional items to limit the delete should be entered as an array
     * @return boolean Returns true on success and false on failure
     */
    public function deletePage($pageID, $additional = []){
        if(is_numeric($pageID)){
            return $this->db->delete($this->config->content_table, array_merge(['page_id' => intval($pageID)], $additional), 1);
        }
        return false;
    }
    
    /**
     * Search page content
     * @param string $search This should be the text you wish to search the content on
     * @param array $additional Any additional items to search on should be provided as an array
     * @return array|false If any information exists they will be returned as an array else will return false
     */
    public function searchPages($search, $additional = []){
        $sql = '';
        $values = [':search' => $search];
        if(!empty($additional)){
            foreach($additional as $field => $value) {
                $fieldVal = SafeString::makeSafe($field);
                $sql.= " AND `{$fieldVal}` = :{$fieldVal}";
                $values[':'.$fieldVal] = $value;
            }
        }
        return $this->db->query("SELECT `title`, `content`, `uri`, MATCH(`title`, `content`) AGAINST(:search) AS `score` FROM `{$this->config->content_table}` WHERE MATCH(`title`,`content`) AGAINST(:search IN BOOLEAN MODE){$sql} ORDER BY `score` DESC;", $values);
    }
    
    /**
     * List all of the pages
     * @param boolean $onlyActive If you only want active pages set to true else set to false
     * @param int $start This should be the start point you want to start returning in the number of records
     * @param int $limit This should be the maximum number of records to display
     * @param array $order This should be how you wish to order the search results
     * @return array|false If any results exist they will be returned as an array else will return false
     */
    public function listPages($onlyActive = false, $start = 0, $limit = 50, $order = []){
        $where = [];
        if($onlyActive == true){$where['active'] = 1;}
        return $this->db->selectAll($this->config->content_table, $where, '*', $order, [intval($start) => intval($limit)]);
    }
    
    /**
     * Returns the total number of pages that exits in the database
     * @param boolean $onlyActive If you only want to count active pages set to true else leave as the default false
     * @return int Returns the total number of pages
     */
    public function countPages($onlyActive = false){
        $where = [];
        if($onlyActive == true){$where['active'] = 1;}
        return $this->db->count($this->config->content_table, $where);
    }
    
    /**
     * Checks to see if a URL exists
     * @param string $uri This should be the URL you are checking if it exists
     * @param array $additional Any additional items to search on should be provided as an array
     * @return int Will return the number of matching URLs (1 if exists and 0 if it doesn't)
     */
    protected function checkIfURLExists($uri, $additional = []){
        return $this->db->count($this->config->content_table, array_merge(['uri' => PageUtil::cleanURL($uri)], $additional));
    }
}

<?php

namespace Content;

use DBAL\Database;
use Configuration\Config;
use Content\Utilities\Validator;

class Social {
    protected $db;
    
    /**
     * Constructor
     * @param Database $db Should be an instance of the database class
     */
    public function __construct(Database $db, Config $config){
        $this->db = $db;
        $this->config = $config;
    }
    
    /**
     * Lists the social bookmarks
     * @param string $domain This should be the domain to add to the social bookmark link
     * @param string $title This should be the page title to add to the bookmark link
     * @param int $active If you only want to get active links set to true else to get all bookmarks set to false
     * @param boolean $orig If you want to show the original link set to true else leave as false
     * @return array|false If any links exist will return an array else return false
     */
    public function getSocialBookmarks($domain, $title, $active = true, $orig = false){
        $where = [];
        if($active === true){
            $where['active'] = 1;
        }
        $bookmarks = $this->db->selectAll($this->config->table_social_bookmarks, $where);
        if(is_array($bookmarks) && $orig === false){
            foreach($bookmarks as $i => $item){
                $bookmarks[$i]['location'] = $this->buildLink($item['location'], $domain, $title);
            }
        }
        return $bookmarks;
    }
    
    /**
     * Gets the bookmark information for a single link
     * @param int $bookmarkID This should be the unique bookmark id
     * @param boolean $active If you only want to get it if it is active
     * @param boolean $orig If you want the original link from the database set to true else set to false
     * @param string $domain This should be the domain to add to the social bookmark link
     * @param string $title This should be the page title to add to the bookmark link
     * @return array|false If any information exists returns an array else returns false
     */
    public function getBookmarkInfo($bookmarkID, $active = false, $orig = false, $domain = '', $title = '') {
        if(is_numeric($bookmarkID)){
            $where = [];
            $where['id'] = $bookmarkID;
            if($active === true){
                $where['active'] = 1;
            }
            $bookmarkInfo = $this->db->select($this->config->table_social_bookmarks, $where);
            if(is_array($bookmarkInfo) && $orig === false){
                $bookmarkInfo['location'] = $this->buildLink($bookmarkInfo['location'], $domain, $title);
            }
            return $bookmarkInfo;
        }
        return false;
    }
    
    /**
     * Add a social bookmark link
     * @param array $bookmarkInfo This should be the social bookmark information
     * @return boolean Returns true if successfully added else will return false
     */
    public function addSocialBookmark($bookmarkInfo){
        $bookmarkInfo['active'] = Validator::setZeroOnEmpty($bookmarkInfo['active']);
        return $this->db->insert($this->config->table_social_bookmarks, $bookmarkInfo);
    }
    
    /**
     * Update social bookmark information
     * @param int $bookmarkID This should be the bookmark ID
     * @param array $bookmarkInfo This should be the information as an array
     * @return boolean If the information has been updated will return true else returns false
     */
    public function updateSocialBookmark($bookmarkID, $bookmarkInfo = []){
        if(is_numeric($bookmarkID) && is_array($bookmarkInfo) && !empty($bookmarkInfo)){
            $bookmarkInfo['active'] = Validator::setZeroOnEmpty($bookmarkInfo['active']);
            return $this->db->update($this->config->table_social_bookmarks, $bookmarkInfo, ['id' => $bookmarkID], 1);
        }
        return false;
    }
    
    /**
     * Deletes social bookmark information from the database for a given id 
     * @param int $bookmarkID This should be the bookmark ID that you wish to delete
     * @return boolean Returns true if deleted else will return false
     */
    public function deleteSocialBookmark($bookmarkID) {
        if(is_numeric($bookmarkID)) {
            return $this->db->delete($this->config->table_social_bookmarks, ['id' => $bookmarkID]);
        }
        return false;
    }
    
    /**
     * Build the link for the URL
     * @param string $link This should be the link string for use with the sprintf function
     * @param string $url This should be the URL to add to the URL
     * @param string $title This should be the page title to add to the string
     * @return string This will be the URL string
     */
    protected function buildLink($link, $url, $title) {
        return str_replace(' ', '%20', sprintf($link, rawurlencode($url), rawurlencode($title)));
    }
}

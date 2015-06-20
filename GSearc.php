<?php

/**
 * Google Search API for PHP
 * @author Yevhen Matasar <matasar.ei@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version 20150611
 */
class GSearch {
    
    /**
     * @var int Cache time in seconds
     */
    private $_cacheTime;
    
    /**
     * @var string Cache path
     */
    private $_cachePath;
    
    /**
     * @var string Google API url
     */
    private $_APIURL = 'https://ajax.googleapis.com/ajax/services/search/web?v=1.0';
    
    /**
     * @var string Referer url
     */
    private $_referer = null;
    
    /**
     * @param type $cacheTime Cache time in hours
     * @param type $cachePath Cache path 
     */
    function __construct($cacheTime = 24, $cachePath = './cache/') {
        $this->setCachePath($cachePath);
        $this->setCacheTime($cacheTime);
    }
    
    /**
     * Set referer
     */
    function setRefferer($url) {
        $this->_referer = (string)$url;
    }
    
    /**
     * Get referer
     */
    function getRefferer() {
        return $this->_referer;
    }
    
    /**
     * Cache path setter
     * @param string $path
     */
    function setCachePath($path) {
        !$path && $this->_cachePath = null;
        if (is_writable($path)) {
            $this->_cachePath = $path;
        } else {
            trigger_error("Cache path does not exist or not writable");
            $this->_cachePath = null;
        }
    }
    
    /**
     * Set cache time
     * @param type $time Time in minutes
     */
    function setCacheTime($time) {
        $this->_cacheTime = 3600 * (int)$time;
    }
    
    /**
     * Send and process query
     * @param string $query Query text
     * @param array $userip End user's IP address (optional)
     * @return stdClass Result
     */
    function query($query, $userip = 'USERS-IP-ADDRESS') {
        $url = "{$this->_APIURL}&q=" . urlencode($query) . "&userip={$userip}";
        $cacheKey = md5($url);
        $filePath = "{$this->_cachePath}{$cacheKey}.tmp";
        $cache = file_exists($filePath);
        
        if ($cache && (filemtime($filePath) + $this->_cacheTime > time())) {
            $result = file_get_contents($filePath);
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $this->_referer && curl_setopt($ch, CURLOPT_REFERER, $this->_referer);
            $result = curl_exec($ch);
            curl_close($ch);
            
            //If path writable, save cache
            if ($result && $this->_cachePath) {
                file_put_contents($filePath, $result);
            } elseif ($cache) {
                //get old data if error
                $result = file_get_contents($filePath);
            }
        }
        
        $result = json_decode($result);
        
        if ($result && $result->responseStatus === 200) {
            return $result->responseData->results;
        } else {
            trigger_error(isset($result->responseStatus) ?
                "Status {$result->responseStatus}, {$result->responseDetails}":
                "Cannot process query");
            return array();
        }
    }
    
    /**
     * Remove all cache files
     */
    function purgeCaches() {
        if ($this->_cachePath) {
            $files = glob("{$this->_cachePath}*.tmp");
            foreach($files as $file) {
                unlink($file);
            }
            return true;
        }
        return false;
    }
}

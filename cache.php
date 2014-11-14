<?php

// SimpleCache v1.0
// Simple, Reusable PHP Cache Class

// Info and Usage Instructions at: http://devgrow.com/simple-cache-class/
// Author: Monjurul Dolon, http://mdolon.com/
// License: MIT

class Cache {

	// Pages you do not want to Cache:
	var $doNotCache = array("admin","profile");

	// General Config Vars
	var $cacheDir = "./cache";
	var $cacheTime = 1296000;
	var $caching = true;
	var $cacheFile;
	var $cacheFileName;
	var $cacheLogFile;
	var $cacheLog;
	
	

	function __construct(){
		$this->cacheFile = urlencode(base64_encode($_SERVER['REQUEST_URI']));
		$this->cacheFileName = $this->cacheDir.'/'.$this->cacheFile.'.txt';
		$this->cacheLogFile = $this->cacheDir."/log.txt";
		if(!is_dir($this->cacheDir)) mkdir($this->cacheDir, 0755);
		if(file_exists($this->cacheLogFile))
			$this->cacheLog = unserialize(file_get_contents($this->cacheLogFile));
		else
			$this->cacheLog = array();
	}
	
	function start(){
		$location = array_slice(explode('/',$_SERVER['REQUEST_URI']), 2);
		if(!in_array($location[0],$this->doNotCache)){
			if(file_exists($this->cacheFileName) && (time() - filemtime($this->cacheFileName)) < $this->cacheTime && $this->cacheLog[$this->cacheFile] == 1){
				$this->caching = false;
				echo file_get_contents($this->cacheFileName);
				//exit();
			}else{
				$this->caching = true;
				ob_start();
			}
		}
	}
	
	function end(){
		if($this->caching){
			file_put_contents($this->cacheFileName,ob_get_contents());
			ob_end_flush();
			$this->cacheLog[$this->cacheFile] = 1;
			if(file_put_contents($this->cacheLogFile,serialize($this->cacheLog)))
				return true;
		}
	}
	
	function purge($location){
		$location = base64_encode($location);
		$this->cacheLog[$location] = 0;
		if(file_put_contents($this->cacheLogFile,serialize($this->cacheLog)))
			return true;
		else
			return false;
	}
	
	function purge_all(){
		if(file_exists($this->cacheLogFile)){
			foreach($this->cacheLog as $key=>$value) $this->cacheLog[$key] = 0;
			if(file_put_contents($this->cacheLogFile,serialize($this->cacheLog)))
				return true;
			else
				return false;
		}
	}

}

<?php
/**
 * @desc        Configuration file
 * @package     [ABCD] OAI module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  

class Configurator
{
	/**
	 * @desc Path to CEPAL dababase
	 * @var string
	 */
	var $path2Cepal = null;
	/**
	 * @desc Path to DBLIL database
	 * @var string
	 */
	var $path2Dblil = null;
	/**
	 * @desc URL to access the Webservice on database system
	 * @var string URL
	 */
	var $urlws;
	/**
	 * @desc timeout to connect to WebService response
	 * @var int
	 */
	var $timeout = 1800;
	
	function __construct()
	{
		global $BVS_CONF;
		
		if(isset($BVS_CONF['URLWS']) && $BVS_CONF['URLWS'] != "") {
			$this->setUrlWs($BVS_CONF['URLWS']);			
		}
		if(isset($BVS_CONF['PATH2DBLIL']) && $BVS_CONF['PATH2DBLIL'] != "") {
			$this->setPath2Dblil($BVS_CONF['PATH2DBLIL']);
		}
		if(isset($BVS_CONF['PATH2CEPAL']) && $BVS_CONF['PATH2CEPAL'] != "") {
			$this->setPath2Cepal($BVS_CONF['PATH2CEPAL']);
		}
		if(isset($BVS_CONF['PATH2MARC']) && $BVS_CONF['PATH2MARC'] != "") {
			$this->setPath2Marc($BVS_CONF['PATH2MARC']);
		}
	}
	
	function setUrlWs($urlws) {
		$this->urlws = $urlws;		
	}
	function getUrlWs(){		
		return $this->urlws;
	}
	function setPath2Dblil($path2Dblil){
		$this->path2Dblil = $path2Dblil;
	}
	function getPath2Dblil(){
		return $this->path2Dblil;
	}
	function setPath2Cepal($path2Cepal){
		$this->path2Cepal = $path2Cepal;
	}
	function getPath2Cepal(){
		return $this->path2Cepal;
	}
	function setPath2Marc($path2Marc){
		$this->path2Marc = $path2Marc;
	}
	function getPath2Marc(){
		return $this->path2Marc;
	}
	function getTimeOut(){
		return $this->timeout;
	}
	function Configurator()
	{
		return $this->__construct();
	}

}

?>
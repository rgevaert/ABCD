<?php
/**
 * @desc        Isis database access
 * @package     [ABCD] OAI module
 * @version     1.0
 * @author      Bruno Neofiti <bruno.neofiti@bireme.org>
 * @since       10 de janeiro 2009
 * @copyright   (c)BIREME - PFI - 2009
 * @public  
*/  

class IsisBroker
{
	var $client;
	var $error;
	var $path2WS;

	function __construct()
	{
		global $configurator;
		$this->setWebService($configurator->getUrlWs());
		$this->client = new soapclient($this->getWebService(), true);
		$err = $this->client->getError();

		if ($err) {
			$this->error = $err;
		}else {
			$this->error = null;
		}
	}
	
	function IsisBroker(){
		return $this->__construct();
	}	
	function setWebService($path2WS){
		$this->path2WS = $path2WS;
	}
	function getWebService(){
		return $this->path2WS;
	}
	function getError()	{
		return $this->error;
	}

	function search($xmlparameters)
	{

		$result=$this->client->call('IsisSearch', array ("xmlparameters" => $xmlparameters));
		// Master debug
		//echo '<pre>' . $this->client->request . '</pre>';
		//echo '<pre>' . $this->client->response . '</pre>';
		//die;
		
		if ($this->client->fault)
		{
			print_r($this->result);
		}
		else
		{
			$err = $this->client->getError();
			if ($err) {
				// Display the error
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			}
			else
			{
				return ($result);
			}

		}

	}

	function keyrange($xmlparameters)
	{
		$result=$this->client->call('IsisSearch', array ("xmlparameters" => $xmlparameters));
		// Master debug
		//echo '<pre>' . $this->client->request . '</pre>';
		//echo '<pre>' . $this->client->response . '</pre>';
		//die;

		if ($this->client->fault){
			print_r($this->result);
		}else{
			$err = $this->client->getError();
			if ($err) {
				// Display the error
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			}else{
				return ($result);
			}
		}
	}

	function index($xmlparameters){

		$result=$this->client->call('IsisIndex', array ("xmlparameters" => $xmlparameters));
		// Master debug
		//echo '<pre>' . $this->client->request . '</pre>';
		//echo '<pre>' . $this->client->response . '</pre>';

		if ($this->client->fault){
			print_r($this->result);
		}else{
			$err = $this->client->getError();
			if($err){
				// Display the error
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			}else{
				return ($result);
			}
		}
	}

	function IsisSearchSort($xmlparameters)
	{
		$result=$this->client->call('IsisSearchSort', array ("xmlparameters" => $xmlparameters));
		// Master debug
		//echo '<pre>' . $this->client->request . '</pre>';
		//echo '<pre>' . $this->client->response . '</pre>';
		if ($this->client->fault){
			print_r($this->result);
		}else{
			$err = $this->client->getError();
			if($err){
				// Display the error
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			}else{
				return ($result);
			}
		}
	}


}

?>
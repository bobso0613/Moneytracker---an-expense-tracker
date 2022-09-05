<?php
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "../";
//$PAGE_SETTINGS["PageTitle"] = "Dashboard";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$PAGE_SETTINGS["Engine"] = new Engine("refresh");
$PAGE_SETTINGS["Engine"]->checkSession($PAGE_SETTINGS["CurrentDirectory"]);


class TransactionsTemplate{
	protected $controller = "TransactionsController";

	protected $method = "render";

	protected $params = [];

	public function __construct(){
		$url = $this->parseUrl();
		//array(2) { [0]=> string(6) "system" [1]=> string(17) "mst_module_action" }
		require_once "./dependencies/".$this->controller.".php";
		$this->controller = new TransactionsController($url);
		$this->params = $url ? array_values($url) : [];
		if (method_exists($this->controller, $this->method) && is_callable(array($this->controller, $this->method)))
		{
		    call_user_func_array([$this->controller,$this->method], $this->params);
		}
		//var_dump ($url);
	}

	public function parseUrl(){
		if (isset($_GET['url'])){
			return $url = explode('/',filter_var(rtrim($_GET['url'],'/'),FILTER_SANITIZE_URL));
		}
	}
}


?>
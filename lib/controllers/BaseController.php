<?php
global $viewcontent;
if(class_exists('BaseController'))
	return;
class BaseController{
	protected $controller;
	protected $action;
	protected $defaultAction;
	protected $filter;
	protected $redirect;
	protected $viewpath;
	public $render=true;
	public $bag=array();
	public $viewcontent;
	public $values=array();
	private $automatic;
	private function initiate(){
		if(method_exists($this,'on_controller_preinit'))	
			$this->on_controller_preinit();
		$item= get_class($this);
		$this->controller =str_replace('Controller','',$item);
		$this->values=Communication::getQueryString();
		$this->values+=Communication::getFormValues();
		$this->action=array_key_exists_v(ACTIONKEY,$this->values);
		if(!$this->action)
			$this->action=$this->defaultAction;
		unset($this->values[CONTROLLERKEY]);
		unset($this->values[ACTIONKEY]);
		if(method_exists($this,'on_controller_init'))	
			$this->on_controller_init();
	}
	public function init(){
		$this->initiate();
		if($this->filter)
			if(!$this->filter->perform($this,$this->values))
				die("Action could not be performed.");
		if($this->automatic)
			$this->automaticRender();
	}
	public function BaseController($automatic=true,$viewpath=false){
		$this->automatic=$automatic;		
		$this->viewpath=$viewpath;
		Debug::Message('Loaded '.$this->controller.' extends Basecontroller');
	}
	protected function automaticRender(){
			Debug::Message('Executing automatic action');
			$action=array_key_exists_v(ACTIONKEY,Communication::getQueryString());
			if(!isset($action) || empty($action))
				if($this->action)
					$action=$this->action;
				else
					$action='index';
			Debug::Message('PreExecuted action: '.$action);
			try{
				$this->executeAction($action);
			}catch(RuntimeException $ex){
				$this->viewcontent='Could not find action: '.$action;
				$this->render=false;
			}
			Debug::Message('Executed action: '.$action);			
			if($this->render){
				$view=$this->findView($this->controller,$action);
				ob_start();
				if($view){
					extract($this->bag, EXTR_REFS);
					require($view);
					$this->viewcontent=ob_get_contents();
				}else
					$this->viewcontent='Could not find view: '.$action;
				ob_end_clean();
				$this->render=false;
			}
			global $viewcontent;
			$viewcontent=$this->viewcontent;
	}
	public function executeAction($action){
		if(method_exists($this,$action)){
			$reflection = new ReflectionMethod($this, $action);
			if (!$reflection->isPublic())
				throw new RuntimeException("The action you tried to execute is not public.");
			HookHelper::run($this->controller.'->pre'.ucfirst($action),$this);
			$this->$action();
			HookHelper::run($this->controller.'->post'.ucfirst($action),$this);
			if($this->render){
				$this->RenderToAction($action);
			}
		}else
			throw new RuntimeException("The action you tried to execute does not exist.");
	}
	protected function RenderToAction($action){
		$view=$this->findView($this->controller,$action);		
		ob_start();
		if($view){
			extract($this->getBag(), EXTR_REFS);
			include($view);
			$this->viewcontent=ob_get_contents();
		}else
			$this->viewcontent='Could not find view: '.$action.' '.$view;
		$this->render=false;
		ob_end_clean();
		global $viewcontent;
		$viewcontent=$this->viewcontent;
	}
	protected function Render($controller,$action){
		$view=$this->findView($controller,$action);		
		ob_start();
		if($view){
			extract($this->getBag(), EXTR_REFS);
			include($view);
			$this->viewcontent=ob_get_contents();
		}else
			$this->viewcontent='Could not find view: '.$view;
		$this->render=false;
		ob_end_clean();
		global $viewcontent;
		$viewcontent=$this->viewcontent;
	}
	private function getBag(){
		return FilterHelper::run($this->controller.'-bag',array($this->bag,$this->controller,$this->action,$this->values));
	}
	protected function RenderFile($filepath){
		ob_start();
		if(file_exists($filepath)){
			extract($this->getBag(), EXTR_REFS);
			include($filepath);
			$this->viewcontent=ob_get_contents();
		}else
			$this->viewcontent='Could not find view: '.$$filepath;
		$this->render=false;
		ob_end_clean();
		global $viewcontent;
		$viewcontent=$this->viewcontent;		
	}
	protected function RenderText($text){
		$this->render=false;
		global $viewcontent;
		$viewcontent=$text;	
	}
	public function Notfound(){
		ob_start();
		include(VIEWS.$controller.'/'.$action.'.php');
		$this->render=false;
		$this->viewcontent=ob_get_contents();
		ob_end_clean();
		global $viewcontent;
		$viewcontent=$this->viewcontent;
	}
	protected function redirect($query=false){
		if(defined('NOREDIRECT') && NOREDIRECT)
			return;
		$redirect=Communication::useRedirect();
		if($redirect)
			if(strtolower($redirect)=='referer')
				Communication::redirectTo(str_replace('&result=1','',Communication::getReferer()),$query);
			else
				Communication::redirectTo($redirect,$query);
	}
	public static function ViewContents(){
		global $viewcontent;
		return $viewcontent;
	}
	
	private function findView($controller,$action){
		if($this->viewpath){
			return $this->viewpath.$controller.'/'.$action.'.php';
		}		
		$apps=AoiSoraSettings::getApplications();
		$total=sizeof($apps);
		Debug::Message('Nbr of apps: '.$total);
		$lcontroller=strtolower($controller);
		$laction=strtolower($action);		
		foreach($apps as $app){
			$path=$app['path'];
			Debug::Value('Path',$path);
			Debug::Value('Searching',$path.VIEWS.$controller.'/'.$action.'.php');
			if(file_exists($path.VIEWS.$controller.'/'.$action.'.php'))
				return $path.VIEWS.$controller.'/'.$action.'.php';	
			if(file_exists($path.VIEWS.$lcontroller.'/'.$laction.'.php'))
				return $path.VIEWS.$lcontroller.'/'.$laction.'.php';
		}

		return false;
	}
	protected function setDefaultAction($action){
		$this->action=$action; 
	}
}
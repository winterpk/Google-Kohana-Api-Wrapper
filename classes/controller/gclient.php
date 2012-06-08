<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Gclient controller used to handle the Oauth2 dance 
 *
 * @package Gclient
 * @author 	Winter King 
 */
class Controller_Gclient extends Kohana_Controller_Template {
	
	/**
	 * Gclient base api client object
	 * 
	 */
	private $_gclient;
	
	public function before()
	{
		parent::before();
		$this->_gclient = Gclient::instance()->gclient();
	}
	
	/**
	 * Oauth2 dance hanlder action
	 * 
	 */
	public function action_index()
	{
		$this->auto_render = false;
		if (isset($_GET['code']))
		{
			$gtoken = $this->_gclient->authenticate();
			Session::instance()->set('gtoken', $gtoken);
		}
		
		if ($gtoken = Session::instance()->get('gtoken'))
		{
			$this->_gclient->gclient()->setAccessToken($gtoken);
		}
		
		if ($this->_gclient->getAccessToken())
		{
			// success
			echo "SUCCESS!!!!!";
		}
		echo 'in here';
	}
} // END

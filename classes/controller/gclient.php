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
		if ($gtoken = Session::instance()->get('gtoken'))
		{
			try
			{
				$this->_gclient->setAccessToken($gtoken);
			} 
			catch (apiAuthException $e)
			{
				Gclient::instance()->authenticate();
			}
			
		}
		if (isset($_GET['code']))
		{
			try
			{
				$gtoken = $this->_gclient->authenticate();
				Session::instance()->set('gtoken', $gtoken);
			} 
			catch(Exception $e)
			{
				echo 'FAILURE!!!';
			}
		}
		
		if ($valid_token = $this->_gclient->getAccessToken())
		{
			if ($_GET['state'])
			{
				Request::$current->redirect($_GET['state']);
			}
			echo "SUCCESS!!!!!";
		}
	}
} // END
<?php defined('SYSPATH') or die('No direct script access.');

/**
* cclient core class
*
* @package        gclient
* @author         Winter King
* @copyright      (c) 2012 Winter King
* @license        http://www.opensource.org/licenses/isc-license.txt
*/
class gclient_core
{
	/**
	 * Static variable to store the singleton object
	 *
	 * @var object	singleton
	 */
    protected static $_instance;

	/**
	 * Gclient library object
	 * 
	 * @var object
	 */
    protected $_gclient;
    
	/**
	 * Stores the google api service library object being called
	 *
	 * @var object
	 */
	protected $_service;
	
	/**
	 * Gclient configuration file
	 * 
	 * @var object	kohana config object
	 */
	protected $_config;
	
	/**
	 * Includes and loads google api libraries 
	 * 
	 * @param	string	google api library to call
	 */
    protected function __construct($service)
    {
    	if ( ! is_object($this->_gclient))
		{
			if ( ! $this->_config = Kohana::$config->load('gclient'))
			{
				throw new Gclient_Exception("No configuration file found");
			}
	    	if ($api_client = Kohana::find_file('vendor', 'google/apiClient'))
			{
				require_once($api_client);
			}
			else 
			{
				throw new Gclient_Exception("Google client library not found.");
			}
	        // Do class setup
	        $this->_gclient = new google\apiClient();
			$this->_gclient->setApplicationName('Qwizzle');
			$this->_gclient->setClientId($this->_config->client_id);
			$this->_gclient->setClientSecret($this->_config->client_secret);
			$this->_gclient->setDeveloperKey($this->_config->developer_key);
			$this->_gclient->setRedirectUri($this->_config->redirect_uri);      
			$this->_gclient->setState(Request::$current->uri());
			$this->_gclient->setApprovalPrompt('auto');  
			$scopes = '';
			foreach($this->_config['scope'] as $scope)
			{
				$scopes .= $scope . ' ';
			}
			$this->_gclient->setScopes($scopes);	
		}
    	
		if ($service)
		{
			// attempt to load the api library being called
			if ( ! $service_file = Kohana::find_file('vendor', 'google/contrib/'.$service))
			{
				throw new Gclient_Exception("Google api library not found: " . $service);
			}
			require_once($service_file);
			$service = 'google\\'.$service;			
 			$this->_service = new $service($this->_gclient);
		}
    }

	/**
	 * Singleton method loads an object into $_instance, call this function first to use this class
	 * 
	 * @param	string	gclient api service library name
	 * @example	$gclient = Gclient::instance('apiCalendarService);
	 */
    public static function instance($service = null)
    {
        if ( ! isset(self::$_instance))
		{
			Gclient::$_instance = new Gclient($service);
		}
        return Gclient::$_instance;
    }

	/**
	 * Calls the base getUserInfo function to get this users information
	 * 
	 * @return	array user information
	 */
	public function get_user()
	{
		return $this->_gclient->getUserInfo();	
	}
	
	/**
	 * Returns the api service object ie. apiCalendarService
	 * 
	 * @return 
	 */
	public function service()
	{
		return $this->_service;
	}
	
	/**
	 * Returns an object of the base google api library gClient class
	 * 
	 */
    public function gclient()
    {
        return $this->_gclient;
    }
	
	/**
	 * Starts the web server application authentication workflow
	 * 
	 */
	public function authenticate()
	{
		Request::$current->redirect(self::$_instance->auth_url());
	}
	
	/**
	 * Alias for base createAuthUrl() method
	 *
	 * @return string	auth url for user authentication
	 */
	public function auth_url() 
	{
		return $this->_gclient->createAuthUrl();
	}
}
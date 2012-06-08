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
	 * Stores the api library object being called
	 *
	 * @var object
	 */
	protected $_api;
	
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
    protected function __construct($api)
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
			$scopes = '';
			foreach($this->_config['scope'] as $scope)
			{
				$scopes .= $scope . ' ';
			}
			$this->_gclient->setScopes($scopes);	
		}
    	
		if ($api)
		{
			// attempt to load the api library being called
			if ( ! $api_file = Kohana::find_file('vendor', 'google/contrib/'.$api))
			{
				throw new Gclient_Exception("Google api library not found: " . $api);
			}
			require_once($api_file);
			$api = 'google\\'.$api;			
 			$this->_api = new $api($this->_gclient);
		}
    }

	/**
	 * Singleton method loads an object into $_instance, call this function first to use this class
	 * 
	 * @param	string	gclient api service library name
	 * @example	$gclient = Gclient::instance('apiCalendarService);
	 */
    public static function instance($api = null)
    {
        if ( ! isset(self::$_instance))
		{
			Gclient::$_instance = new Gclient($api);
		}
        return Gclient::$_instance;
    }

	/**
	 * Returns the api service object ie. apiCalendarService
	 * 
	 */
	public function api()
	{
		return $this->_api;
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
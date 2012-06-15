<?php defined('SYSPATH') or die('No direct script access.');

/**
* cclient core class
*
* @package        gclient
* @author         Winter King
* @copyright      (c) 2012 Winter King
* @license        http://www.opensource.org/licenses/isc-license.txt
*/
class Gclient_Core
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
		$this->_gclient->setApprovalPrompt('auto'); 
		$this->_gclient->setRedirectUri($this->_config->redirect_uri);      
		$scopes = '';
		foreach($this->_config['scope'] as $scope)
		{
			$scopes .= $scope . ' ';
		}
		$this->_gclient->setScopes($scopes);
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
	 * Singleton method loads Gclient object into $_instance, call this function first to use this class
	 * If a service string is passed, then it returns a new Gclient object with attached service
	 * 
	 * @param	string	gclient api service library name
	 * @return	Object	gclient object
	 * @example $gclient = Gclient::instance();
	 * @example	$gcal = Gclient::instance('apiCalendarService);
	 */
    public static function instance($service = null)
    {
		if ( ! isset(self::$_instance))
		{
			Gclient::$_instance = new Gclient($service);
		}
		if ($service)
		{
			Gclient::$_instance->add_service($service);
			//Gclient::$_instance = new Gclient($service);
		}
        return Gclient::$_instance;
    }

	public function add_service($service)
	{
		return $this->_gclient->addService($service);
	}

	/**
	 * Alias for $this->_gclient->setAccessToken
	 * 
	 */
	public function set_access_token()
	{
		return $this->_gclient->setAcessToken();
	}
	
	/**
	 * Alias for $this->_gclient->getAccessToken()
	 * 
	 */
	public function get_access_token()
	{
		return $this->_gclient->getAccessToken();
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
	 * Validates an access token
	 * 
	 * @return mixed	false on failure token infor array on success
	 */
	public function validate_token($token)
	{
		$return = json_decode($this->_gclient->validateToken($token));
		if (isset($return->error))
		{
			return false;
		}
		else
		{
			return $return;
		}
	}
	
	/**
	 * Returns the api service object ie. apiCalendarService
	 * If you pass a service name it will act as a setter
	 * 
	 * @throws	Gclient_Exception on failure to find service library
	 * @return 	Object Google service object 	
	 !!!NOT WORKING FOR NOW!!!
	public function service($service = null)
	{
		// attempt to load the api library being called
		if ($service)
		{
			if ( ! $service_file = Kohana::find_file('vendor', 'google/contrib/'.$service))
			{
				throw new Gclient_Exception("Google api library not found: " . $service);
			}
			require_once($service_file);
			$service = 'google\\'.$service;			
			$this->_service = new $service($this->_gclient);	
		}
		return $this->_service;
	}
	*/
	
	
	/**
	 * Returns an object of the base google api library gClient class
	 * 
	 */
    public function gclient()
    {
        return $this->_gclient;
	}
	
	/**
	 * Alis for base setState() method
	 * 
	 */
	public function set_state($state)
	{
		return $this->_gclient->setState($state);
	}
	
	/**
	 * setScopes() alias
	 * 
	 * @return void
	 */
	public function set_scopes($scopes)
	{
		if ( ! is_array($scopes))
		{
			$scopes = array($scopes);
		}	
		foreach($scopes as $scope)
		{
			$this->_gclient->setScopes($scope);
		}
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
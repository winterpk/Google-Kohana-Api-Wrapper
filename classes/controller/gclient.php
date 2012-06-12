<?php

class Controller_Gclient extends Controller
{
	/**
	 * Relay action handles the Google account connection business flow
	 *
	 * @return void
	 * @author 	Winter King
	 */
	public function action_index()
	{
		// there are three possible routes here
		// register/login/connect
		// get that from the state 
		if (isset($_GET['state']))
		{
			$state = explode('|', $_GET['state']);
			$action = $state[0];
			$redirect_url = $state[1];
		}
		else 
		{
			Request::$current->redirect();
			$redirect_url = url::site();
		}
		
		// three different actions here register/login/connect
		if ($action == 'register')
		{
			if (isset($_GET['error'])) // this means they did not give authorization
			{
				Session::instance()->set('gclient_error', 'Unauthorized');	
				Request::$current->redirect($redirect_url);	
			}
			if (isset($_GET['code'])) // this means this user has allowed us authorization
			{
				// now authenticate our app
				$token = Gclient::instance('apiOauth2Service')->gclient()->authenticate();
				// set the gtoken session
				Session::instance()->set('gtoken', $token);
				// check to see if they are logged in to qwizzle
				if (A1::instance()->logged_in())
				{
					Session::instance()->set('gclient_error', 'Already logged in.');
					Request::$current->redirect($redirect_url);
					// they are logged in so check to see if this user's Qwizzle google_id matches their social google_id
				}
				else
				{
					$check = $this->model_account->register_google();
					if ($check)
					{
						// success!
						Request::$current->redirect('account');
					}
					else 
					{
						Session::instance()->set('gclient_error', 'Qwizzle registration failed.');
						Request::$current->redirect($redirect_url);	
					}
				}
			}
		}
		if ($action == 'login')
		{
			$gclient = Gclient::instance('apiOauth2Service');
			$oauth2 = $gclient->service();
			if (isset($_GET['error'])) // this means they did not give authorization
			{
				Session::instance()->set('gclient_error', 'Unauthorized');	
				Request::$current->redirect($redirect_url);	
			}
			if (isset($_GET['code'])) // this means this user has allowed us authorization
			{
				// now authenticate our app
				$token = Gclient::instance('apiOauth2Service')->gclient()->authenticate();
				// set the gtoken session
				Session::instance()->set('gtoken', $token);
				// check to see if they are logged in to qwizzle
				if (A1::instance()->logged_in())
				{
					Session::instance()->set('gclient_error', 'Already logged in.');
					Request::$current->redirect($redirect_url);
					// they are logged in so check to see if this user's Qwizzle google_id matches their social google_id
				}
				else
				{
					if ($gtoken = Session::instance()->get('gtoken'))
					{
						$gclient->gclient()->setAccessToken($gtoken);
					}
					if ($gclient->gclient()->getAccessToken())
					{
						$guser = $oauth2->userinfo->get();
						// attepmt a manual user lookup by id
						$user = Mango::factory('mango_user')->load(1, null, null, array(), array('google_id' => $guser['id']));
						if ($user->loaded())
						{
							a1::instance()->complete_login($user);
							Request::$current->redirect($redirect_url);
						} 
						else
						{
							Request::$current->redirect('user/gsync?state=register|account');
						}
					}
				}
			}
		}
		if ($action == 'connect')
		{
			
		}
	}	
} // END

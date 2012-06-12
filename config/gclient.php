<?php defined('SYSPATH') or die('No direct script access.');

$base_url = URL::site().'gclient';
return array(
	'client_id' => '244191462066.apps.googleusercontent.com',
	'client_secret' => 'wIG8z8q9Oq9zMyGd5lqHcoRz',
	'redirect_uri' => $base_url,
	'developer_key' => 'AI39si6F5ixsuqEQKQ2WY2OS3ZbsI40GzuoKTw_qlThgLGMhjNYwgo5OPL05Bzy8ImDed_mbMtYExVLu65AvCag2jPmJT7rWzQ',
	'scope' => array(
		'https://www.googleapis.com/auth/userinfo.profile',
		'https://www.googleapis.com/auth/userinfo.email',
		'https://www.googleapis.com/auth/plus.me'
	)
);


<?php defined('SYSPATH') or die('No direct access allowed.');

Route::set('gclient', 'gclient(/<action>(/<id>))')
	->defaults(array(
		'controller' => 'gclient',
		'action'     => 'index',
	));
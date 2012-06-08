# Kohana Google Api Wrapper

This is a wrapper module for googles api libraries

## Google api link

[code.google.com/p/google-api-php-client](http://code.google.com/p/google-api-php-client/)

## Installation

To use this module copy and paste into the MODPATH of kohana and add to bootstrap.php module loader

	Kohana::modules(array(
		'gclient'		=> MODPATH.'gclient',		// Google api wrapper
	));

## Usage

Call the class anywhere in the application with the static instance method.
	
	Gclient::instance();

Access the base gClient.php library use the gclient() method

	$gclient = Gclientp::instance()->gclient();

If you want to use a specific service pass it as a parameter to instance() method and call api().

	$gcal = Gclient::instance('apiCalendarService')->api();

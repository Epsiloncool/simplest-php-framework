<?php

// Comment out this lines to disable development mode
error_reporting(E_ALL);
ini_set('display_errors', 1);


global $config;

$config = array(
	'db' => array(
		'host' => '127.0.0.1',
		'name' => '<your_db_name>',
		'user' => '<your_db_user>',
		'pass' => '<your_db_password>',
	),
	'system' => array(
		'salt' => 'Any_random_string_here',
		'auth_cookie' => 'the_name_of_auth_cookie',
	),
);
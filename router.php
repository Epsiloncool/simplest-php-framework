<?php

// A simple router functions

global $routes, $url_parameters, $route_data;

$url_parameters = array();
$route_data = false;

class Route
{

	public static function get($rexp, $cb)
	{
		global $routes;

		$routes[$rexp] = array($cb, '*', '', 'get');
	}

	public static function post($rexp, $cb)
	{
		global $routes;

		$routes[$rexp] = array($cb, '*', '', 'post');
	}
}

$routes = array(
	// The routes used in this basic demo
	'/?' => array('theme/main.php', 'U', 'Dashboard'),
	'/login/?' => array('theme/login.php', '*', 'Log In'),
	'/jx/?' => array('jx.php', '*', 'AJAX Request Processor', 'post'),

	// Your additional routes below
	//'/tasks/?' => array('theme/tasks.php', 'U', 'Tasks'),
	//'/tasks/(\d+|add)' => array('theme/tasks.php', 'U', 'Task'),
);

function RouteRequest()
{
	global $routes, $url_parameters, $route_data, $cuser;

	$url_parameters = array();
	$route_data = false;

	$uri = $_SERVER['REQUEST_URI'];

	$path = parse_url($uri, PHP_URL_PATH);

	foreach ($routes as $k => $d) {
		if (preg_match('~^'.$k.'$~', $path, $t)) {
			$url_parameters = $t;
			$route_data = $d;
			break;
		}
	}

	if (!$route_data) {
		// No route found
		$route_data = array('theme/404.php', '*', 'Page Not Found');
	}

	// Check rights (very simple rights checker)
	if (($route_data[1] == 'U') && (!$cuser)) {
		// No access!
		$route_data = array('no_access.php', '*', 'No Access');
	}
	// You can add more rights here


	if (is_callable($route_data[0])) {
		// Route to exact function
		call_user_func($route_data[0]);
	} else {
		// Route to another php file
		$fn = dirname(__FILE__).'/'.$route_data[0];

		// Run controller file
		if (is_file($fn) && file_exists($fn)) {
			require $fn;
		} else {
			echo 'Controller file '.$route_data[0].' not found.';
		}
	}

}
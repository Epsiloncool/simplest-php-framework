<?php

require_once dirname(__FILE__).'/config.php';

require_once dirname(__FILE__).'/classes/sf_user.php';	// Main user class

// Add all your classes here
require_once dirname(__FILE__).'/classes/dummy.class.php';	// Just a dummy class

global $system_salt, $system_authcookie, $cuser;

global $header_no_menus;
$header_no_menus = false;

global $config;

$system_salt = $config['system']['salt'];
$system_authcookie = $config['system']['auth_cookie'];

function home_url()
{
	return 'http://'.$_SERVER['SERVER_NAME'];
}

// Lazy DB connection
function GetDBLink()
{
	global $config;

	if (!isset($GLOBALS['dblink']) || (!$GLOBALS['dblink'])) {
		// Create connection
		$host = $config['db']['host'];
		$name = $config['db']['name'];
		$user = $config['db']['user'];
		$pass = $config['db']['pass'];

		$db = new mysqli($host, $user, $pass, $name);

		if ($db->error) {
			echo 'Can not connect to DB! Error: '.$db->error;
			$db = false;
		}

		$db->query('set names "utf8"');

		$GLOBALS['dblink'] = $db;
	}

	return $GLOBALS['dblink'];
}

/**
 * A set of globally accessible functions here
 */
function MakeDBInsert($table, $data = array())
{
	$kk = array();
	$vv = array();
	foreach ($data as $k => $d) {
		$kk[] = '`'.$k.'`';
		$vv[] = '"'.addslashes($d).'"';
	}

	$q = '';
	if (count($kk) > 0) {
		$q = 'insert into `'.$table.'` ('.implode(',', $kk).') values ('.implode(',', $vv).')';
	}

	return $q;
}

function MakeDBUpdate($table, $data = array(), $where = array())
{
	$w = array();
	foreach ($where as $k => $d) {
		$w[] = '(`'.$k.'`="'.addslashes($d).'")';
	}

	$vv = array();
	foreach ($data as $k => $d) {
		$vv[] = '`'.$k.'`="'.addslashes($d).'"';
	}

	$q = '';
	if ((count($vv) > 0) && (count($w) > 0)) {
		$q = 'update `'.$table.'` set '.implode(',', $vv).' where '.implode(' and ', $w);
	}

	return $q;
}

function MyDBFetchAll($res)
{
	$a = array();
	while ($row = $res->fetch_assoc()) {
		$a[] = $row;
	}

	return $a;
}

/**
 * A set of simple AJAX processor classes and functions
 */

class wpjxmResponse
{
	public $data = array();
	
	protected $xresponse = array();
	
	public function console($msg) {
		$this->xresponse[] = array('cn', $msg);
	}
	
	public function alert($msg) {
		$this->xresponse[] = array('al', $msg);
	}

	public function html($id, $data) {
		$this->xresponse[] = array('as', $id, $data);
	}
	
	public function redirect($url = '', $delay = 0) {
		$this->xresponse[] = array('rd', $url, $delay);
	}
	
	public function reload(){
		$this->xresponse[] = array('rl');
	}
	
	public function script($script = '') {
		$this->xresponse[] = array('js', $script);
	}
	
	public function call($function_name, $params = array()) {
		$this->xresponse[] = array('cl', $function_name, $params);
	}
	
	public function variable($var, $value) {
		$this->xresponse[] = array('vr', $var, $value);
	}
	
	public function variables($vars) {
		$this->xresponse[] = array('vs', $vars);
	}
	
	public function trigger($name, $vars = array()) {
		$this->xresponse[] = array('tr', $name, $vars);
	}
	
	public function setResponse($a) {
		$this->xresponse = $a;
	}
	
	public function getJSON() {
		return json_encode($this->xresponse);
	}
	
	public function getData() 
	{
		if ((isset($_POST['__xr'])) && ($_POST['__xr'] == 1)) {
			$post = isset($_POST['z']) ? json_decode($_POST['z'], true) : array();
			$this->data = $post;
			return $post;
		} else {
			return false;
		}
	}
}

/**
 * A set of functions to set with the current user
 */
function DetectCurrentUser()
{
	global $cuser;

	$cuser = new SF_User();

	if (!$cuser->isExists()) {
		$cuser = false;
	}

	return $cuser;
}

/**
 * A simple action hook library
 * 
 */
global $system_actions;

$system_actions = array();

function add_action($action_name, $callable)
{
	global $system_actions;

	if (!is_callable($callable)) {
		throw new Exception('Called add_action for action='.$action_name.' with non-callable parameter 2');
		return;
	}

	if (!isset($system_actions[$action_name])) {
		$system_actions[$action_name] = array();
	}
	if (!in_array($callable, $system_actions[$action_name])) {
		$system_actions[$action_name][] = $callable;
	}
}

function do_action($action_name)
{
	global $system_actions;

	if (isset($system_actions[$action_name])) {
		foreach ($system_actions[$action_name] as $cb) {
			if (is_callable($cb)) {
				// Call the callable!
				call_user_func($cb);
			}
		}
	}
}

/**
 * A set of functions to work with URLs
 */
function ModifyURL($url, $mod = array())
{
	$p = parse_url($url);
	$q = array();
	if (isset($p['query'])) {
		parse_str($p['query'], $q);
	}
	foreach ($mod as $k => $d) {
		if ($d === false) {
			// Remove
			unset($q[$k]);
		} else {
			$q[$k] = $d;
		}
	}
	$p['query'] = http_build_query($q);
	return join_url($p, false);
}

function join_url($parts, $encode = TRUE)
{
	if ($encode) {
		if (isset($parts['user']))
			$parts['user'] = rawurlencode($parts['user']);
		if (isset($parts['pass']))
			$parts['pass'] = rawurlencode($parts['pass']);
		if (isset($parts['host']) &&
				!preg_match('!^(\[[\da-f.:]+\]])|([\da-f.:]+)$!ui', $parts['host']))
			$parts['host'] = rawurlencode($parts['host']);
		if (!empty($parts['path']))
			$parts['path'] = preg_replace('!%2F!ui', '/', rawurlencode($parts['path']));
		if (isset($parts['query']))
			$parts['query'] = rawurlencode($parts['query']);
		if (isset($parts['fragment']))
			$parts['fragment'] = rawurlencode($parts['fragment']);
	}

	$url = '';
	if (!empty($parts['scheme']))
		$url .= $parts['scheme'] . ':';
	if (isset($parts['host'])) {
		$url .= '//';
		if (isset($parts['user'])) {
			$url .= $parts['user'];
			if (isset($parts['pass']))
				$url .= ':' . $parts['pass'];
			$url .= '@';
		}
		if (preg_match('!^[\da-f]*:[\da-f.:]+$!ui', $parts['host']))
			$url .= '[' . $parts['host'] . ']'; // IPv6
		else
			$url .= $parts['host'];			 // IPv4 or name
		if (isset($parts['port']))
			$url .= ':' . $parts['port'];
		if (!empty($parts['path']) && $parts['path'][0] != '/')
			$url .= '/';
	}
	if (!empty($parts['path']))
		$url .= $parts['path'];
	if (isset($parts['query']))
		$url .= '?' . $parts['query'];
	if (isset($parts['fragment']))
		$url .= '#' . $parts['fragment'];
	return $url;
}

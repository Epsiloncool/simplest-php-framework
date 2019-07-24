<?php

/**
 * Simplest AJAX processor
 */

require_once dirname(__FILE__).'/startup.php';

if (isset($_POST) && isset($_POST['__xr'])) {
	$resp = new wpjxmResponse();

	$data = $resp->getData();

	$jx_action = isset($data['jx_action']) ? $data['jx_action'] : '';

	switch ($jx_action) {
		case 'login':
			ProcessLogin($resp);
			break;
		case 'logout':
			//setcookie('kx_login', '', time() - 3600, '/');
			global $cuser;

			$cuser->Logout();

			$resp->reload();
			break;
		case 'make_search':
			MakeSearch($resp);
			break;
		default:
			// Do nothing
	}

	echo $resp->getJSON();
	exit();
}

function ProcessLogin($jx)
{
	$data = $jx->data;

	$time = time();

	$db = GetDBLink();

	$login = isset($data['login']) ? trim($data['login']) : '';
	$password = isset($data['password']) ? $data['password'] : '';
	$isremember = isset($data['isremember']) ? intval($data['isremember']) : 0;

	$e = array();
	if (strlen($login) < 1) {
		$e[] = 'Please enter login';
	}
	if (strlen($password) < 1) {
		$e[] = 'Please enter password';
	}

	if (count($e) > 0) {
		$jx->alert('There are errors:'."\n\n".implode("\n", $e));
	} else {
		// Check login/password
		$new_user_id = SF_User::LoginUser($login, $password, $isremember);

		if ($new_user_id > 0) {
			$jx->redirect('/');
		} else {
			$jx->alert('Login/password is incorrect.');
		}
	}

}

function MakeSearch($jx)
{
	$data = $jx->data;

	$s = isset($data['s']) ? trim($data['s']) : '';
	$url = isset($data['url']) ? trim($data['url']) : '';

	// Modify URL
	if (strlen($s) > 0) {
		$new_url = ModifyURL($url, array('s' => $s));
	} else {
		$new_url = ModifyURL($url, array('s' => false));
	}

	$jx->redirect($new_url);
}

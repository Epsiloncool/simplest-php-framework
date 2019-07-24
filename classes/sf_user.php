<?php

class SF_User
{
	public $_id = 0;
	public $wp_user = null;
	
	protected $_flds = array(
		'id' => array(2),
		'phone' => array(1),
		'email' => array(1),
		'first_name' => array(1),
		'last_name' => array(1),
	);
	
	function __construct($id = 0) 
	{	
		$this->_id = 0;
		$this->wp_user = null;

		if ($id < 1) {
			$id = self::GetLoggedUserId();
		}

		if ($id > 0) {
			// Read user data
			$wpuser = self::GetUserFromDB($id);

			if ($wpuser) {
				$this->_id = $wpuser['id'];
				$this->wp_user = $wpuser;
			}
		}
	}

	public static function GetUserFromDB($id)
	{
		if ($id > 0) {
			$db = GetDBLink();

			$q = 'select * from `users` where id = "'.addslashes($id).'"';
			$res = $db->query($q);

			if ($res->num_rows > 0) {
				$row = $res->fetch_assoc();

				return $row;
			}
		}

		return false;
	}

	public static function GetLoggedUserId()
	{
		global $system_salt, $system_authcookie;

		$id = 0;
		if (isset($_COOKIE[$system_authcookie])) {
			$id = intval($_COOKIE[$system_authcookie]);
		}

		return $id;
	}

	public static function LoginUser($login, $password, $isremember)
	{
		global $system_salt, $system_authcookie;

		$db = GetDBLink();

		$q = 'select 
				id 
			from `users` 
			where 
				(`login` = "'.addslashes($login).'" or `email` = "'.addslashes($login).'") and 
				(`pass` = "'.addslashes(sha1($system_salt.$password)).'")';
		$res = $db->query($q);

		if ($res->num_rows > 0) {
			// Valid!
			$row = $res->fetch_assoc();
			// Set cookie
			if ($isremember) {
				$exp_time = time() + 3600 * 24 * 30;
			} else {
				$exp_time = 0;
			}
			setcookie($system_authcookie, $row['id'], $exp_time, '/');

			return $row['id'];
		}

		return 0;
	}

	function isExists()
	{
		if (($this->_id > 0) && ($this->wp_user)) {
			return true;
		} else {
			return false;
		}
	}

	function getCustomField($k)
	{
		/*
		switch ($k) {
			case 'periods_arr':
				$s = explode(',', $this->get('periods'));
				$a = array();
				foreach ($s as $z) {
					if (is_numeric(trim($z))) {
						$a[] = floatval(trim($z));
					}
				}
				sort($a, SORT_NUMERIC);
				return $a;
				break;
			default:
				return false;
		}
		*/
		return false;
	}
	
	function get($k)
	{	
		if ($this->isExists()) {
			if (isset($this->_flds[$k])) {
				$f = $this->_flds[$k];
				if ((!isset($f[0])) || ($f[0] == 0)) {
					// json
					return $this->getJson($k);
				} elseif ($f[0] == 1) {
					// meta
					return get_user_meta($this->_id, $k, true);
				} elseif ($f[0] == 3) {
					// meta
					return isset($this->wp_user->{$k}) ? $this->wp_user->{$k} : false;
				} else {
					switch ($k) {
						case 'id':
							return $this->_id;
							//break;
						default:
							return $this->getCustomField($k);
					}
				}
			} else {
				return false;
			}
		}
	}
	
	function getFullName() 
	{
		return trim($this->get('first_name').' '.$this->get('last_name'));
	}
	
	function getEmail() 
	{
		return $this->get('email');
	}
}
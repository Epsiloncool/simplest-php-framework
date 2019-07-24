<?php

require_once dirname(__FILE__).'/startup.php';
require_once dirname(__FILE__).'/classes/pimclient.class.php';
require_once dirname(__FILE__).'/classes/pimblacklist.class.php';

if (isset($_POST) && isset($_POST['__xr'])) {
	$resp = new wpjxmResponse();

	$data = $resp->getData();

	$jx_action = isset($data['jx_action']) ? $data['jx_action'] : '';

	switch ($jx_action) {
		case 'login':
			ProcessLogin($resp);
			break;
		case 'logout':
			setcookie('kx_login', '', time() - 3600, '/');
			$resp->reload();
			break;
		case 'import_list_file';
			ProcessImportListFile($resp);
			break;
		case 'add_phones_blacklist':
			ProcessAddPhonesBlacklist($resp);
			break;
		case 'make_search':
			MakeSearch($resp);
			break;
		case 'remove_blacklist_items':
			RemoveBlacklistItems($resp);
			break;
		case 'submit_sender':
			ProcessSubmitSender($resp);
			break;
		case 'delete_sender':
			ProcessDeleteSender($resp);
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
		$new_user_id = EEC_User::LoginUser($login, $password, $isremember);

		if ($new_user_id > 0) {
			$jx->redirect('/');
		} else {
			$jx->alert('Login/password is incorrect.');
		}
	}

}

function ProcessImportListFile($jx)
{
	$data = $jx->data;

	$time = time();

	$db = GetDBLink();

	$title = isset($data['title']) ? trim($data['title']) : '';
	$file = isset($data['file']) ? $data['file'] : '';

	$e = array();
	if (strlen($title) < 1) {
		$e[] = 'Please specify the name of the list';
	}
	if (strlen($file) < 1) {
		$e[] = 'You need to select a file';
	}

	if (count($e) > 0) {
		$jx->alert('There are errors:'."\n\n".implode("\n", $e));
	} else {
		// Check 
		$file = str_replace(array('<', '>', '/', '\\'), '', $file);

		$fn = $_SERVER['DOCUMENT_ROOT'].'/uploaded_files/'.$file;

		if (is_file($fn) && (file_exists($fn))) {
			$data = ExtractFileData($fn, true);

			if (isset($data['code']) && ($data['code'] == 0)) {

				if (isset($data['rows']) && (count($data['rows']) > 0)) {

					$q = 'insert into `pim_lists` (`title`,`filename`,`insert_dt`,`n_items`) values ("'.addslashes($title).'","'.addslashes($file).'","'.date('Y-m-d H:i:s', time()).'","0")';
					$db->query($q);

					$insert_id = $db->insert_id;

					if ($insert_id > 0) {

						$n = 0;
						$n_clients = 0;
						foreach ($data['rows'] as $row) {

							$client = PIM_Client::GetByPhone($row[0]);

							if ($client->IsValid()) {
								$client_id = $client->_id;
							} else {
								// Create new client
								$client_id = PIM_Client::Create(array(
									'phone' => $row[0],
									'name' => $row[1],
								));
								if ($client_id > 0) {
									$n_clients ++;
								}
							}

							if ($client_id > 0) {
								$q = 'insert into `pim_listitems` (`list_id`,`client_id`) values ("'.$insert_id.'","'.addslashes($client_id).'")';
								$db->query($q);
							}

							$n ++;
						}
						// Update n_items
						$q = 'update `pim_lists` set `n_items` = "'.addslashes($n).'" where id = "'.$insert_id.'"';
						$db->query($q);

						// Recalculate blacklisted items
						PIM_Blacklist::JustifyPhoneListStats();

						$jx->alert('Success! '.$n.' list items were inserted'."\n".'and the list was created.'."\n\n".'We have '.$n_clients.' new clients added.');
						$jx->redirect('/lists/'.$insert_id);
	
					} else {
						$jx->alert('Error: Unable to insert new list descriptor, DB error!');
					}

				} else {
					$jx->alert('Error: No data rows in the file');
				}

			} else {
				$jx->alert('Error: '.$data['error']);
			}
		} else {
			$jx->alert('Temporary uploaded file was removed? ('.$fn.')');
		}

	}

}

function ProcessAddPhonesBlacklist($jx)
{
	$data = $jx->data;

	$time = time();

	$db = GetDBLink();

	$phones = isset($data['phones']) ? trim($data['phones']) : '';
	$is_import = isset($data['is_import']) ? intval($data['is_import']) : 0;

	$phonelist = array();
	$t = explode("\n", $phones);
	foreach ($t as $d) {
		$dd = trim($d);
		if (strlen($dd) > 0) {
			$dd = str_replace(array('(', ')', '-', ' '), '', $dd);
			if (preg_match('~^\d{13}$~', $dd)) {
				$phonelist[] = $dd;
			}
		}
	}

	$e = array();
	if (count($phonelist) < 1) {
		if ($is_import) {
			$e[] = 'Please add some valid phone numbers';
		}
	}

	if (count($e) > 0) {
		$jx->alert('There are errors:'."\n\n".implode("\n", $e));
	} else {

		$n_exists = 0;
		$n_added = 0;
		$n_failed = 0;
		$n_created = 0;
		foreach ($phonelist as $d) {
			// Check if we have the client with this number
			$client = PIM_Client::GetByPhone($d);

			if ($client->IsValid()) {
				$client_id = $client->_id;
			} else {
				// Create new client
				if ($is_import) {
					$client_id = PIM_Client::Create(array(
						'phone' => $d,
						'name' => '',
					));
				} else {
					$client_id = 999999;	// Virtual client id
				}
				$n_created ++;
			}

			if ($client_id > 0) {
				$q = 'select * from `pim_blacklist` where `client_id` = "'.addslashes($client_id).'" and `type_id` = "1"';
				$r4 = $db->query($q);

				if ($r4->num_rows > 0) {
					// Already exists
					$n_exists ++;
				} else {
					if ($is_import) {
						$q = 'insert into `pim_blacklist` (`client_id`,`type_id`,`insert_dt`) values ("'.addslashes($client_id).'","1","'.addslashes(date('Y-m-d H:i:s', time())).'")';
						$db->query($q);
					}
					$n_added ++;
				}

			} else {
				$n_failed ++;
			}
		}

		if ($is_import) {
			PIM_Blacklist::JustifyPhoneListStats();
		}
		
		// Create a small report
		$report = array();
		$report[] = 'Total valid phones found: '.count($phonelist);
		$report[] = 'New clients added and blacklisted: '.$n_created;
		$report[] = 'Existing clients blacklisted: '.($n_added - $n_created);
		$report[] = 'Clients was already blacklisted: '.$n_exists;
		$report[] = 'Failed: '.$n_failed;

		$jx->variable('stats', 'Pre-import statistics:<br><br>'.implode('<br>', $report).'<br><br>');
		$jx->variable('report', implode("\n", $report));
		if ($is_import) {
			$jx->reload();
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

function RemoveBlacklistItems($jx)
{
	$db = GetDBLink();

	$data = $jx->data;

	$ids = isset($data['ids']) ? $data['ids'] : array();

	// Remove specified items from the blacklist

	$ids_f = array();
	foreach ($ids as $d) {
		$ids_f[] = intval($d);
	}

	if (count($ids_f) > 0) {
		$q = 'delete from `pim_blacklist` where id in ('.implode(',', $ids_f).')';
		$db->query($q);
	
		// Recalculate blacklisted items
		PIM_Blacklist::JustifyPhoneListStats();
	}

	$jx->reload();
}

function ProcessSubmitSender($jx)
{
	$data = $jx->data;

	$time = time();

	$db = GetDBLink();

	$sender_id = isset($data['sender_id']) ? intval($data['sender_id']) : 0;
	$act = isset($data['act']) ? trim($data['act']) : '';
	$title = isset($data['title']) ? trim($data['title']) : '';
	$snumber = isset($data['snumber']) ? trim($data['snumber']) : '';
	$tariff = isset($data['tariff']) ? trim($data['tariff']) : '';
	$reference = isset($data['reference']) ? trim($data['reference']) : '';
	$token = isset($data['token']) ? trim($data['token']) : '';
	$ap = isset($data['gateway']) ? trim($data['gateway']) : '';

	$e = array();
	if ($act == 'edit') {
		if ($sender_id < 1) {
			$e[] = 'Something went wrong!';
		}
	}
	if (count($e) < 1) {
		if (strlen($title) < 1) {
			$e[] = 'Please specify the name of the sender';
		}
		if (strlen($snumber) < 1) {
			$e[] = 'Please specify sender number';
		}
		if (strlen($tariff) < 1) {
			$e[] = 'Please specify tariff';
		}
		if (strlen($reference) < 1) {
			$e[] = 'Please specify reference';
		}
		if (strlen($token) < 1) {
			$e[] = 'Please specify token';
		} else {
			// F8BC0769-A8A6-402F-B982-AE37ABE93A8B
			$t2 = str_replace('-', '', $token);
			if (!preg_match('~^[0-9a-fA-F]{32}$~', $t2)) {
				$e[] = 'The token looks wrong. Should be 32 hexadecimal numbers';
			}
		}
		if (strlen($ap) < 1) {
			$e[] = 'Please specify gateway URL';
		} else {
			$pp = parse_url($ap);
			if ((isset($pp['query']) && (strlen($pp['query']) > 0)) ||
				(isset($pp['fragment']) && (strlen($pp['fragment']) > 0))) {
					$e[] = 'Please remove parameters from the gateway URL';
				}
		}
	}

	if (count($e) > 0) {
		$jx->alert('There are errors:'."\n\n".implode("\n", $e));
	} else {

		if ($act == 'create') {
			// Create a record
			$q = MakeDBInsert('pim_senders', array(
				'title' => $title,
				'snumber' => $snumber,
				'tariff' => $tariff,
				'reference' => $reference,
				'token' => $token,
				'ap' => $ap,
				'insert_dt' => date('Y-m-d H:i:s', $time),
			));
			$db->query($q);

			$insert_id = $db->insert_id;

			if ($insert_id > 0) {
				// Fine!
				$jx->alert('New sender was successfully added.');
				$jx->redirect('/senders/');
			} else {
				$jx->alert('MySQL Error: '.$db->error);
			}
		}

		if ($act == 'edit') {
			// Update a record
			$q = MakeDBUpdate('pim_senders', array(
				'title' => $title,
				'snumber' => $snumber,
				'tariff' => $tariff,
				'reference' => $reference,
				'token' => $token,
				'ap' => $ap,
				'insert_dt' => date('Y-m-d H:i:s', $time),
			), array(
				'id' => $sender_id,
			));
			$db->query($q);

			$jx->redirect('/senders/');
		}
	}

}

function ProcessDeleteSender($jx)
{
	$data = $jx->data;

	$db = GetDBLink();

	$sender_id = isset($data['sender_id']) ? intval($data['sender_id']) : 0;

	$e = array();
	if ($sender_id < 1) {
		$e[] = 'Something went wrong!';
	}

	if (count($e) > 0) {
		$jx->alert('There are errors:'."\n\n".implode("\n", $e));
	} else {

		$q = 'delete from `pim_senders` where id = "'.addslashes($sender_id).'"';
		$db->query($q);

		$jx->redirect('/senders/');
	}

}

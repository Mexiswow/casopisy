<?php

namespace Casopisy;

use Nette;
use \dibi;

/**
 */
class UserModel
{

	/** @return ArrayHash|FALSE */
	static function getById($id)
	{
		$user = dibi::fetch("SELECT * FROM user WHERE id=%i", $id);
		if ($user)
			return Nette\ArrayHash::from($user);
		return FALSE;
	}

	/** @return ArrayHash */
	static function login($data)
	{
		$data['lastlogin%sql'] = 'NOW()';

		$id = $data['id'];
		$user = \Casopisy\UserModel::getById($id);
		if ($user) {
			dibi::query("UPDATE user SET", $data, " WHERE id=%i",$id);
		}
		else {
			$data['firstlogin%sql'] = 'NOW()';
			dibi::query("INSERT INTO user", $data);
		}

		return \Casopisy\UserModel::getById($id);
	}


	static function logTime($id, $time)
	{
			dibi::query("UPDATE user SET log_time = %s", $time, " WHERE id=%i",$id);
	}
}

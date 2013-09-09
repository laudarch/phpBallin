<?php
/* $Id: cGenPasswd.php,v 0.1 2009/08/15 17:17:17 aedavies Exp $ */

interface iGenPasswd
{
	public static function genpasswd($len);
	public static function genid($salt);
}

class cGenPasswd implements iGenPasswd
{
	public static function genpasswd($len)
	{
		$pwchars = "abcdefhjmnpqrstuvwxyz1234567890,?;.:!$=+@_-&|#ABCDEFGHJKLMNPQRSTUVWYXZ";
		$pwlen   = strlen( $pwchars )-1;
		$passwd  = '';

		for ($i = 0; $i < $len; $i++) {
				$passwd .= $pwchars[mt_rand(0,$pwlen)];
		}
		return ($passwd);
	}

	public static function genid($salt)
	{
		$uid = self::genpasswd(16);
		$uid = sha1($salt.$uid.time()).md5($salt.$uid.time());
		return ($uid);
	}
}
	
# Usage:
# $passwd = cGenPasswd::gen_passwd(8);
# echo $passwd;
#
# $id = cGenPasswd::genid("ABCD");
# print "id == $id [__] ".strlen($id);
?>

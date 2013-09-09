<?php
/* $Id: cCookies.php,v 0.1 2010/10/17 16:23:15 aedavies Exp $ */

interface iCookies {
	public static function set($name, $value, $expire=0, $path='/', $domain='', $secure=0, $httponly=1);
	public static function set_multi($array_cookies); #, $expire=0, $path='/', $domain='', $secure=0, $httponly=1 );
	public static function get($name);
	public static function destroy($cookie);
}
	
final class cCookies implements iCookies {
	public static function set($name, $value, $expire=0, $path='/', $domain='', $secure=0, $httponly=0)
	{
		$ret = setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
		if ($ret)
			return (true);
		else
			return (false);
	}

	# XXX: set Multiple cookie name values in one cookie :)
	#public static function set_multi( $array_cookies, $expire=0, $path='/', $domain='', $secure=0, $httponly=1 )
	public static function set_multi($array_cookies)
	{
		foreach ($array_cookies as $n => $v) {
			self::set($n, $v);
		}
	}
	
	public static function get($name)
	{
		return ((isset($_COOKIE[$name])) ? $_COOKIE[$name] : null);
	}
	
	public static function destroy($cookie)
	{
		# XXX: backdate cookie to delete it ^^
		$expire = mktime(0, 0, 0, 10, 24, 1986); # Happy Earth Day ^^
		$ret = self::set($cookie, '', $expire);
		if ($ret)
			return (true);
		else
			return (false);
	}
}
/*
 * Usage:
 * $cookie = new cCookies;
 * $cookie->set( 'name', 'value' );
 * $cookie_name = $cookie->get( 'name' );
 * if( $cookie_name != null )
 *      echo $cookie_name;
 * else
 *	    echo "Cookie not found :( you make cookie monster sad.";
 * $cookie->destroy();
 * unset( $cookie );
 */
?>

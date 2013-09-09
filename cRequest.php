<?php
/* $Id: cRequest.php,v 0.1 2010/10/18 18:50:12 aedavies Exp $ */

interface iRequest {
	public static function request();
	public static function file($name);
	public static function get($name, $default=null);
	public static function post($name, $default=null);
	public static function any($name, $default=null);
}

final class cRequest implements iRequest {
	public static function get($name, $default=null)
	{
	    return ((isset($_GET[$name])) ? $_GET[$name] : null);
	}

	public static function post($name, $default=null)
	{
	    return ((isset($_POST[$name])) ? $_POST[$name] : null);
	}

	public static function any($name, $default=null)
	{
	    return ((isset($_REQUEST[$name])) ? $_REQUEST[$name] : null);
	}

	public static function file($name)
	{
	    return ((isset($_FILES[$name])) ? $_FILES[$name] : null);
	}

	public static function request()
	{
		return($_REQUEST);
	}
}
/*
 * Usage1:
 *  $request   = new cRequest;
 *  $username  = $request->get('username');   # used get method in form
 *  $username1 = $request->post('username1'); # used post method in form
 *  $joke = $request->any('joke');            # we don't care what method
 *                                            # was used in the form ;p
 *  unset($request);
 *
 * Usage2:
 *  $username  = cRequest::get('username');   # used get method in form
 *  $username1 = cRequest::post('username1'); # used post method in form
 *  $joke      = cRequest::any('joke');       # We don't care what method
 *                                            # was used in the form ;p
 */
?>
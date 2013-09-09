<?php
/* $Id: cSession.php,v 0.1 2010/10/17 15:29:35 aedavies Exp $ */
/*
 * Copyright (c) 2009,2010,2011 
 * 		Archzilon Eshun-Davies <laudarch@qremiaevolution.org>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */
session_start();

interface iSession
{
	public function set($name, $value);
	public function get($name);
	public function kill($name);
	public function destroy();
}
	
final class cSession implements iSession
{
	public function set($name, $value)
	{
		$_SESSION[$name] = $value;
	}

	public function get($name)
	{
		return ((isset($_SESSION[$name])) ? $_SESSION[$name] : null);
	}

	public function kill($name)
	{
		if ($this->get($name)) {
			unset($_SESSION[$name]);
		}
	}

	public function destroy()
	{
		session_destroy();
	}
}
/*
 * Usage:
 * $session = new cSession;
 * $session->set('name', 'value');
 * $sess_name = $session->get('name');
 * if($sess_name != null)
 *      echo $sess_name;
 * else
 *	    echo "Cookie not found :( you make cookie monster sad.";
 * $session->destroy();
 * unset($session);
 */
?>

<?php
/* $Id: mNotify.php,v 1.0 2013/11/11 07:48:54 laudarch Exp $ */
/**
 * Copyright (c) 2013 
 *               Archzilon Eshun-Davies <laudarch@host.sk>
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

public interface iMNotify {
	public function setURL($url);
	public function setSender($sender);
	public function setAPIKey($key);
	public function setMessage($msg);
	public function setReceivers($number_s);
	public function send();
}

 /**
  * @Coder:          Archzilon Eshun-Davies
  * @Description:    For sending SMS through mNotify.net
  * @Date:           November 11, 2013
  */
public final class mNotify implements iMNotify {

	protected $url = "http://bulk.mnotification.com/smsapi";
	protected $sender;
	protected $apiKey;
	protected $message;
	protected $receivers;
	protected $params ="?key=%s&to=%s&msg=%s&sender_id=%s";
	
	public $errorCode;
	public $hasError = false;
	public $errorMessage = "";
	
	/**
	 * Function Name: setURL
	 * Purpose:       Set the URL for SMS
	 * Parameters:    $url
	 */
	public function setURL($url)
	{
		$this->url = $url;
	}
	
	/**
	 * Function Name: setSender
	 * Purpose:       Set SMS Sender address or name
	 * Parameters:    $sender
	 */
	public function setSender($sender)
	{
		$this->sender = $sender;
	}
	
	/**
	 * Function Name: setAPIKey
	 * Purpose:       Set API Key for this session
	 * Parameters:    $key
	 */
	public function setAPIKey($key)
	{
		$this->apiKey = $key;
	}

	/**
	 * Function Name: setMessage
	 * Purpose:       Set the SMS message to be sent
	 * Parameters:    $msg
	 */
	public function setMessage($msg)
	{
		$this->message = urlencode($msg);
	}

	/**
	 * Function Name: setReceivers
	 * Purpose:       set the receivers of this text can be one or more 
	 *		  phone numbers separated by ,
	 * Parameters:    $number_s
	 */
	public function setReceivers($number_s)
	{
		$this->receivers = $number_s;
	}

	/**
	 * Function Name: send
	 * Purpose:       Sends the text Message via mNotify and sets global
	 * 		  variables $hasError if an error occurred and places
	 *		  the error message in $errorMessage also the error code
	 *		  is placed in $errorCode for reference sake.
	 * Parameters:    NONE
	 */
	public function send()
	{
		$rqUrl = sprintf($this->params, $this->key, $this->receivers,
			$this->message, $this->sender);
		$rqUrl = $url.$rqUrl;

		# is cURL installed yet?
		if (!function_exists('curl_init')) {
			die('Sorry cURL is not installed!');
		}
 
		$ch = curl_init($rqUrl);
		curl_setopt($ch, CURLOPT_URL, $rqUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		#curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
		#curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		#curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_REFERER, "http://laudarch.host.sk");
		curl_setopt($ch, CURLOPT_USERAGENT, "laudarchChrome/1.0");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$result = curl_exec($ch);
		$error = curl_error($ch);
		$curl_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($result == false) {
		    curl_close($ch);
		    $this->hasError = true;
		    $this->errorMessage = "An error occurred: $error";
		    return; /* Return Early */
		} else {
			if (substr($curl_http_code, 0, 2) != '20') {
				curl_close($ch);
				$this->hasError = true;
				$this->errorMessage = "An error occurred: Invalid HTTP response returned: $curl_http_code";
				return; /* Return Early */
			}
		}

		$this->errorCode = $result;

		switch ($result) {
		case "1000":
			/* All is well $this->hasError == false */
			break;
		case "1002":
			$this->hasError = true;
			$this->errorMessage = "SMS sending failed";
			break;
		case "1003":
			$this->hasError = true;
			$this->errorMessage = "Insufficient balance";
			break;
		case "1004":
			$this->hasError = true;
			$this->errorMessage = "Invalid API key";
			break;
		case "1005":
			$this->hasError = true;
			$this->errorMessage = "Invalid destination (the phone number you submitted is not in a valid format)";
			break;
		}
	}
}
?>
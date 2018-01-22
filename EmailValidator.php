<?php
/**
 * EmailValidator: A more logical email validator.
 *
 * @copyright Copyright (c) 2018, Archzilon Eshun-Davies <laudarch@qremiaevolution.org>
 * @license   MIT, http://www.opensource.org/licenses/mit-license.php
 */
class EmailValidator {
	/**
	 * isValidEmail: Check if email is valid
	 *
	 * @param $email
	 * @return bool
	 * Usage:
	 *       $ret = EmailValidator::isValidEmail("name@host.sk");
	 *       if ($ret) 
	 *			echo "VALID EMAIL";
	 *		 else
	 *		    echo "INVALID EMAIL";
	 */
	public static function isValidEmail($email) {
		$email_apart = preg_split('/@/', $email);

		if (count($email_apart) <= 1) return false;

		$username = $email_apart[0];
		$hostname = $email_apart[1];
		$mxhosts = array();
		$dns = checkdnsrr($hostname);
		$mx = getmxrr($hostname, $mxhosts);

		return ($dns && $mx && count($mxhosts) >= 1) ? true : false;
	}
}
?>

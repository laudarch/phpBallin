<?php
/* $Id: cLogger.php,v 0.1 2010/10/14 18:48:54 aedavies Exp $ */

date_default_timezone_set('GMT');
require_once("cIP.php");

interface iLogger {
	public static function log($type, $data);
	public static function log_access($file="logs/access_log");
	public static function push_email($subject, $msg, $from, $email);
	public static function showerror($msg);
}
    
class cLogger implements iLogger {
	public $email = "root@localhost";
	public $from  = "Server";

    	public static function log_access($file="logs/access_log")
      	{
	    	$ref  = (isset($_SERVER['HTTP_REFERER']))    ? $_SERVER['HTTP_REFERER']    : null;
	    	$ua   = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : null;
	    	$ip   = cIP::get_client_ip();
        	$date_time = date("r");

	    	$log_access = $file;
	    	$fd = fopen($log_access, "a");

	    	/* Construct log file */
	    	$logdata = "[$date_time] $ip - $ref - $ua|\n";

	    	fwrite($fd, $logdata);
	    	fclose($fd);
        }

     	public static function log($type, $data)
     	{
     		$logfile = "logs/".$type."_log";
     		$loginfo = self::humanize($data);
     		# Log stuff *now*
     		$fd      = fopen($logfile, "a");
     		fwrite($fd, $loginfo);
	    	fclose($fd);
     	}

	/**
	 * Show Error Page
	 */
	public static function showerror($msg)
	{
		#self::push_email("Application Error", $msg);
		print <<<ERR
		<script>
			document.title = "Application Error";
		</script>
		<style>
			a.button, input[type^="submit"], input[type^="button"] {
				  margin: 			0;
				  padding: 			2px 5px 2px 5px;
				  font-family: 		Arial, Sans-serif;
				  font-size: 		12px;
				  text-decoration: 	none;
				  color: 			#222;
				  cursor: 			pointer;
				  background: 		#ddd url("../images/button-background.gif") repeat-x 0 0;
				  border: 			1px solid #aaa;
			}
			a.button:hover, input[type^="submit"]:hover, input[type^="button"]:hover {
				  border-color: #9cf #69e #69e #7af;
			}
			#errormsg {
				background-color: #efe;
				padding:          8px;
				color:            #000;
			}
			
			.emsg {
				color: #ff0000;
			}
		</style>
		<body bgcolor="#000000">
			<table valign="top" width="500" cellpadding="0" cellspacing="0" align="center" id="errormsg">
				<tr>
				  <td width="500" align="center" class="ehead">
					  <img src="images/error.gif" />&nbsp;<b>Application Error</b><br />
					  <hr size="1" noshade="noshade" />
				  </td>
			</tr>
				<tr>
				  <td width="500" align="left" class="etext">
					  &nbsp;<br />
					  One or more errors were detected while the program attempted to perform
					  your request.  Depending on the nature of the error, you may need the
					  assistance of the system administrator.  A description of the problem is
					  provided below:<br />
					  <ul>
					    <li class="emsg">$msg</li>
					  </ul>
					  <hr size="1" noshade="noshade"/><br />
					  <input type="button" value="&laquo; Return" onclick="history.go(-1);" />
				 </td>
			   </tr>
			</table>
		</body>
ERR;
		exit;
	}
		
	public static function push_email($subject, $msg, $from, $email)
	{
		$headers = "From: $from: $email <$email>\r\n"
			."Reply-To: $email\r\n"
			."Priority: urgent\r\n"
			."Importance: High\r\n"
			."Precedence: special-delivery\r\n"
			."Organization: Qremia Evolution\r\n"
			."MIME-Version: 1.0\r\n"
			."Content-Type: text/plain\r\n"
			."Content-Transfer-Encoding: 8bit\r\n"
			."X-Priority: 1\r\n"
			."X-MSMail-Priority: High\r\n"
			."X-Mailer: PHP/".phpversion()."\r\n"
			."X-QremiaE: 1.0 by Qremia Evolution\r\n"
			."Date: ".date("r")."\n";
		mail($email, $subject, $msg, $headers);
	}

     	private static function humanize($data)
	{
		$line = '';
		foreach ($data as $k => $v) {
			$line .= sprintf(' -%s %s', $k, $v);
		}
		return sprintf("[%s] %s %s %s\n",
				date('r'),
				$_SERVER['SERVER_ADDR'],
				$_SERVER['REQUEST_URI'],
				trim($line));
	}
    }
?>
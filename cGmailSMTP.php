<?php
	    /* $Id: cGmailSMTP.php,v 0.1 2010/10/16 20:11:45 aedavies Exp $ */
	    
	    interface iGmailSMTP {
	    	public static function gmail( $from, $namefrom, $to, $nameto, $subject, $message );
	    }

		class cGmailSMTP implements iGmailSMTP {
			public static function gmail( $from, $namefrom, $to, $nameto, $subject, $message ) {
    			$newLine = "\r\n";
    			$smtpConnect;
    			$smtpResponse;
    			$logArray=array();

    			$this->smtpConnect = fsockopen( "tcp://alt4.gmail-smtp-in.l.google.com", 25, $errno, $errstr, 15 );
    			$this->smtpResponse = fgets( $this->smtpConnect, 515 );

    			if( $this->smtpConnect ) {
        			$this->logArray['connect'] = $this->smtpResponse;
    			}

    			fputs( $this->smtpConnect, "HELO localhost" . $this->newLine );
    			$this->smtpResponse = fgets( $this->smtpConnect, 515 );
    			$this->logArray['helo'] = $this->smtpResponse;

    			fputs( $this->smtpConnect, "MAIL FROM: <$from>" . $this->newLine );
                $this->smtpResponse = fgets( $this->smtpConnect, 515 );
                $this->logArray['from'] = $this->smtpResponse;

                fputs( $this->smtpConnect, "RCPT TO: <$to>" . $this->newLine );
                $this->smtpResponse = fgets( $this->smtpConnect, 515 );
                $this->logArray['to'] = $this->smtpResponse;

                fputs( $this->smtpConnect, "DATA". $this->newLine );
                $this->smtpResponse = fgets( $this->smtpConnect, 515 );
                $this->logArray['data'] = $this->smtpResponse;

                $headers  = "MIME-Version: 1.0" . $newLine;
                $headers .= "Content-type: text/plain; charset=iso-8859-1" . $newLine;
                $headers .= "To: $nameto <$to>" . $newLine;
                $headers .= "From: $namefrom <$from>" . $newLine;
                
                $fmsg  = "To: $to\r\n";
                $fmsg .= "From: $from\r\n";
                $fmsg .= "Subject: $subject\r\n";
                $fmsg .= "$headers\r\n\r\n";
                $fmsg .= "$message\r\n";
                $fmsg .= ".\r\n";

                fputs( $this->smtpConnect, $fmsg );
                $smtpResponse = fgets( $this->smtpConnect, 515 );
                $this->logArray['message'] = $this->smtpResponse;

                fputs( $this->smtpConnect, "QUIT".$this->newLine );
                $this->smtpResponse = fgets( $this->smtpConnect, 515 );
                $this->logArray['quit'] = $this->smtpResponse;
    			#echo nl2br(var_export($logArray));
    		}
	}

/* Usage:
$to = "laudarchzilon@gmail.com";
$nameto = "laudarch";
$from = "laudarch@host.sk";
$namefrom = "laudarch";
$subject = "smtp testing 1.2.3.";
$message = "does this work ... yes it does ... yadda yadda yadda";
#smtp($from, $namefrom, $to, $nameto, $subject, $message);
global $logArray;
print_r($logArray);
*/
?>

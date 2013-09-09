<?php
/* $Id: cMail.php,v 0.1 2011/04/13 16:19:00 aedavies Exp$ */

 interface iMail
 {
	 public function mail($to, $from, $subject, $message, $file="");
 }

final class cMail implements iMail
{
   protected $newline = "";
   public function mail($to, $from, $subject, $message, $file = "")
   {
		if ($file != "") {
			$fn         = explode("/", $file);
			$filename   = $fn[sizeof($fn)-1];
			$attachment = chunk_split(base64_encode(file_get_contents($file))); 
		}
		$uid = md5(uniqid(time()));
		$header  = "From: $from".$this->newline;
		$header .= "Reply-To: $from".$this->newline;
		$header .= "MIME-Version: 1.0".$this->newline;
		$header .= "Content-Type: multipart/mixed; boundary=\"$uid\"".$this->newline.$this->newline;
		$header .= "This is a multi-part message in MIME format.".$this->newline;
		$header .= "--$uid".$this->newline;
		$header .= "Content-type:text/html; charset=\"UTF-8\"".$this->newline;
		$header .= "Content-Transfer-Encoding: 7bit".$this->newline.$this->newline;
		$header .= $message.$this->newline.$this->newline;

		if ($file != "") {
			$header .= "--$uid".$this->newline;
			$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$this->newline;
			$header .= "Content-Transfer-Encoding: base64".$this->newline;
			$header .= "Content-Disposition: attachment; filename=\"$filename\"".$this->newline;
			$header .= $attachment.$this->newline.$this->newline;
		}
		$header .= "--".$uid."--";
		$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
		if (mail($to, $subject, $message, $header))
			return (true);
		else
			return (false);
	}
}
?>

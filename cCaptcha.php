<?php
/* $Id: cCaptcha.php,v 0.1 2009/09/11 07:48:54 aedavies Exp $ */
/*
 * Copyright (c) 2009,2010,2011 
 *               Archzilon Eshun-Davies <laudarch@qremiaevolution.org>
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

 /**
  * @Coder:          Archzilon Eshun-Davies
  * @Description:    Captcha For Web Forms
  * @Date:           September 11, 2009
  * @update:         Added interface declaration and other
  *                  Bug fixes =) 2010/10/14
  *                  Added Session Class 2010/10/17
  *                  Added font file location in class construct
  *                  Added Variable Captcha length 2011/05/22(Major improvement)
  */

require_once('cSession.php');
    
interface iCaptcha {
	public function showcaptcha($rclen);           # show captcha image(png)
	public static function chkcaptchacode($strInput); # validate user input
}
    
final class cCaptcha implements iCaptcha {
   	protected $font; # Font File(ttf)

	# This is to make Everyone Happy :)
	public function __construct($fontfile) {
		 # include Font File(Required)
		 $this->font = $fontfile;
	}

	/*
	 * @Function Name: showcaptcha
	 * @Purpose:       Display Captcha Image
	 * @Parameters:    $rclen - Length of captcha string
	 */
	public function showcaptcha($rclen) {
		$arrchar = array();
        # Some servers don't chroot like OpenBSD :(
        putenv('GDFONTPATH='.realpath('.'));

		$session = new cSession;
		$text = $this->generatecaptcha( $rclen );

		for ($i = $rclen; $i!=0; $i--) {
			$char = substr($text, -$i, 1);
			array_push($arrchar, $char);
		}

		# XXX: potential security problem
		#      especially with session theft
		$session->set("captcha_code", $text); # Store Captcha Code
							                  # For Future Checks

		$height = 43;             # Captcha image height
		$width  = 32*($rclen);    # Captcha image Width
		$font   = $this->font;    # Captcha font(font file and location)
		$image_p = imagecreate($width, $height);  # Create Image

		# Set Color to Black
		$black   = imagecolorallocate($image_p, 255, 255, 255);
		# and white
		$white   = imagecolorallocate( $image_p, 0, 0, 0 );

		# Get Char from font file as Image
		$x = 10;
		$y = 30;
		imagettftext($image_p, 22, rand(-50, 50), $x, $y, 
			     $white, $font, $arrchar[0]); # Dirty hack :)
		for ($j=1; $j<$rclen; $j++) {
			$x = $x+30;
			imagettftext($image_p, 22, rand(-50, 50), $x, $y,
				$white, $font, $arrchar[$j]);
		}
		# Done

		#  Draw Mesh
		$i = 7;
		while ($i <= $height-3) {
			imageline($image_p, 1, $i, $width, $i, $white);
			$i = $i + 7;
		}

		$j = 7;
		while ($j <= $width-3) {
			imageline($image_p, $j, 1, $j, $height, $white);
			$j = $j + 7;
		}
		/* Done */

		#imagejpeg( $image_p, null, 80 ); /* Image Ready; Show it man */
		imagepng( $image_p, null ); # Gotta love png ;=)
	}
		
	/*
	 * Function Name: chkcaptchacode
	 * Purpose:       Check if User Entered The Correct Captcha Code
	 * Paramenters:   $userinput - The Code the User 
	 * 		 	       Entered(eg. $_POST['sec_code')
	 */
	public static function chkcaptchacode($userinput) {
		$session      = new cSession;
		$captcha_code = $session->get('captcha_code');
		if ($captcha_code != null) {
			if ($userinput == $captcha_code)
				return (1);
			else
				return (0);
		} else {
			return (0);
		}
	}

	/*
	 * @Function Name: generatecaptcha
	 * @Purpose:       Generate a random String of $nLen long
	 * @Parameters:    $nLen - Length of String to Generate
	 */
	private function generatecaptcha($nLen) {
		$chars  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ
			   abcdfgijkmnpqrstuvwxyz0123456789";
		$code   = "";                 # Init to null
		$clen   = strlen($chars) - 1; # a variable with the fixed
					      # length of chars correct for
					      # the fence post issue
		while (strlen($code) < $nLen) {
			$code .= $chars[mt_rand( 0, $clen )]; /* mt_rand's range
			       	is inclusive - this is why we need 0 to n-1 */
		}
		return ($code);
	}
}
?>

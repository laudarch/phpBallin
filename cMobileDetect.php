<?php
/* $Id: cMobileDetect.php,v 1.0 2007/08/11 17:48:17 laudarch Exp $ */

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
 
interface iMobileDetect {
	public function isMobile();
}

class cMobileDetect implements iMobileDetect {

    protected $accept;
    protected $userAgent;

    protected $isMobile     = false;
    protected $isAndroid    = null;
    protected $isBlackberry = null;
    protected $isOpera      = null;
    protected $isPalm       = null;
    protected $isWindows    = null;
    protected $isGeneric    = null;

    protected $devices = array(
        "android"       => "android",
        "blackberry"    => "blackberry",
        "iphone"        => "(iphone|ipod)",
        "opera"         => "opera mini",
        "palm"          => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)",
        "windows"       => "windows ce; (iemobile|ppc|smartphone)",
        "generic"       => "(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)"
    );

    public function __construct()
    {
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->accept    = $_SERVER['HTTP_ACCEPT'];

        if (isset($_SERVER['HTTP_X_WAP_PROFILE'] ) || isset( $_SERVER['HTTP_PROFILE'])) {
            $this->isMobile = true;
        } elseif (strpos($this->accept,'text/vnd.wap.wml') > 0 || strpos($accept,'application/vnd.wap.xhtml+xml') > 0) {
            $this->isMobile = true;
        } else {
            foreach ($this->devices as $device => $regexp) {
                if ($this->isDevice($device)) {
                    $this->isMobile = true;
                }
            }
        }
    }

    /**
     * Overloads isAndroid() | isBlackberry() | isOpera() | isPalm() |
     *           isWindows() | isGeneric() through isDevice()
     *
     * @param string $name
     * @param array $arguments
     * @return bool
     */
    public function __call($name, $arguments)
    {
        $device = substr($name, 2);
        if ($name == "is" . ucfirst($device)) {
            return $this->isDevice($device);
        } else {
            trigger_error("Method $name not defined", E_USER_ERROR);
        }
    }

    /**
     * Returns true if any type of mobile device detected, including
     * special ones
     *
     * @return bool
     */
    public function isMobile()
    {
        return $this->isMobile;
    }

    protected function isDevice($device)
    {
        $var    = "is" . ucfirst($device);
        $return = $this->$var === null ? (bool) preg_match(
	"/".$this->devices[$device]."/i", $this->userAgent) : $this->$var;

        if (($device != 'generic') && ($return == true)) {
            $this->isGeneric = false;
        }

        return ($return);
    }
}

/*
 * Usage:
 *  require_once("cMobileDetect.php");
 *  $detect = new cMobileDetect;
 *
 *  if ($detect->isAndroid()) {
 *       // code to run for the Google Android platform
 *  }
 *
 *  if ($detect->isIphone()) {
 *      // code to run for iPhone
 *  }    
 *
 *  if ($detect->isMobile()) {
 *      // any mobile platform
 *  }
 */
?>

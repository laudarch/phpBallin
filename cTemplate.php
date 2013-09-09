<?php
    /* $Id: cTemplate.php,v 0.1 2010/10/16 20:11:45 aedavies Exp $ */

	interface iTemplate {
	   public function setvar($name, $var);
	   public function gettpl($tpl);
	   public function gettpl_file($tplfile);
	}

	# Classic Template manager
	class cTemplate implements iTemplate {
	   private $vars = array();

	   public function setvar($name, $var) {
	       $this->vars[$name] = $var;
	   }

	   public function gettpl_file($tplfile) {
	      if (($tpl = $this->getfile( $tplfile ))) {
	        foreach( $this->vars as $n => $v )
	        {
	           $tpl = str_replace('{' . $n . '}', $v, $tpl);
       	    }
	        return ($tpl);
	      }
	      return (false);
	   }

	   public function gettpl($tpl) {
	      foreach($this->vars as $n => $v) {
	          $tpl = str_replace('{' . $n . '}', $v, $tpl);
       	  }
	      return ($tpl);
	   }

	   private function trustedfile($file)  {
             # only trust local files owned by us
             if (!preg_match( "/^([a-z]+):\/\//", $file) && fileowner($file) == getmyuid()) {
               return (true);
             }
             return (false);
	   }

	   private function getfile($file) {
		if (is_file($file)) {
			if ($this->trustedfile($file)) {
				ob_start();
				include $file;
				$contents = ob_get_contents();
				ob_end_clean();
				return ($contents);
			}
		}
		return (false);
	   }
	}
?>
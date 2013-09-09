<?php
/* $Id: cError.php,v 0.1 2010/10/14 20:23:12 aedavies Exp $ */

require_once("cIP.php");
require_once("cLogger.php");

/**
 * Error interface.
 */
interface iError {
	/* protected methods inherited from Exception class */

	public function getMessage();
	public function getCode();
	public function getFile();
	public function getLine();
	public function getTrace();
	public function getTraceAsString();

	// overrideable methods

	public function __construct($code, $message = '');
	public function __toString();

	// new methods

	public function log();
	public static function showerror($msg);
}

class cError extends Exception implements iError {
	/**
	 * Current environment, taken from the config file.
	 *
	 * @var string
	 */
	public static $environment;

	/**
	 * Error code.
	 *
	 * @var int
	 */
	protected $code = 0;
	/**
	 * Error message.
	 *
	 * @var string
	 */
	protected $message = '';
	/**
	 * Filename where error occurred.
	 *
	 * @var string
	 */
	protected $file;
	/**
	 * Line where error occurred.
	 *
	 * @var int
	 */
	protected $line;

	/**
	 * Error constructor.
	 *
	 * @param    int $code    Error code
	 * @param string $message Error message
	 */
	public function __construct($code, $message='')
	{
		if (empty(self::$environment)) {
			self::$environment = cConf::get('ENVIRONMENT');
		}
		parent::__construct($message, $code);
	}

	/**
	 * String casting.
	 *
	 * @return string Error message
	 */
	public function __toString()
	{
		return get_class($this).'('.$this->message.')';
	}

	/**
	 * Error log.
	 */
	public function log()
	{
		cLogger::log("error", array(
			     "code"    => $this->code,
			     "message" => $this->message,
			     "file"    => $this->file,
			     "line"    => $this->line));
	}

	public static function showerror( $msg )
	{
		cLogger::showerror( $msg );
	}
}

/**
 * Exteption handler.
 * 
 * @param Exception $exception Exception class.
 */
function exception_handler($exception)
{
	$exception->log();
}

set_exception_handler("exception_handler");

/* Amen-ra error handler */
function error_handler($severity, $msg, $filename, $linenum)
{
	cLogger::log("error", array(
		     "code"    => $severity,
		     "message" => $msg,
		     "file"    => $filename,
		     "line"    => $linenum));
	/* Don't execute PHP internal error handler */
	return (true);
}

set_error_handler("error_handler");
?>
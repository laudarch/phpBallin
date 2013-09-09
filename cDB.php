<?php
/* $Id: cDB.php,v 0.1 2010/10/14 11:45:23 aedavies Exp $ */
    
require_once('cConfig.php');
require_once('cLogger.php');
    
interface iDB {
	public function open($dbServer, $user, $password, $dbName=null); # Open database with optional dbname
	public function seldb($db_handle, $dbName);                      # Select dbname to use
	public function query($query);                                   # Query DB(select, insert, update, delete)
	public function asfetch($row);                                   # Fetch result as an associative array
	public function arfetch($row);                                   # Fetch result as an array(assoc && indexes)
	public function count($res);                                     # Count How many results we got
	public function secure($string);                                 # Add slashes and escape :=)
	public function chkint($string);                                 # Check if the input is a *positive* digit
									 # no neg numbers =(
	public function close($db_handle);                               # Close the Database
	public function errorMessage();					 # Show MySQL error
}

class cDB extends cLogger implements iDB {
	/* Variables */
	private $db,
		$dbSel,
		$db_sel,
		$dbResults,
		$dbAffectedRows,
		$dbNumberRows;

	 /**
	  * @Function Name: open
	  * @Purpose:       Connect to specified Database
	  *                 and open the database table.
	  * @Parameters:    $dbServer  - ServerName or IP address
	  *                 $user      - Username for database
	  *                 $password  - Password for database
	  *                 $dbName    - Database name
	  * @Return value:  Database handle($db)
	  */
	public function open($dbServer, $user, $password, $dbName=null)
	{
		$this->db = mysql_connect($dbServer, $user, $password);
		if (!$this->db) {
			$this->logmyerror();
			return (false);
		}
		if ($dbName != null) {
			$this->db_sel = $this->seldb( $dbName, $this->db );
			if (!$this->db_sel) {
				$this->logmyerror();
				return (false);
			}
		}
		return ($this->db);
	}

	/**
	 * @Function name: seldb
	 * @Purpose:       To select a database table using the database handle
	 *                 and datatable name.
	 * @Parameters:    $db_handle  - Database handle received from sql_conn()
	 *                 $dbTable    - Database table name
	 * @return value:  NONE
	 */
	public function seldb($db_handle, $dbName)
	{
		$this->dbSel = mysql_select_db($db_handle, $dbName);
		if (!$this->dbSel) {
	//$this->logmyerror();
	return (false);
		}
		return true;
	}

	/**
	 * @Function name: query
	 * @Purpose:       To query a database
	 * @Parameters:    $query (SQL Query String)
	 * @Return value:  
	 */
	public function query($query)
	{
		/*
		 *check if query is an INSERT, DELETE, UPDATE, REPLACE or DROP query
		 */
		if (preg_match( "/^INSERT|DELETE|UPDATE|REPLACE|DROP/i", $query)) {
			/* let's query */
			$this->dbResults      = mysql_query($query, $this->db); # || die( '[!] An <b>Error</b> Occurred' );
			$this->dbAffectedRows = mysql_affected_rows();
			
			/* check how many rows are affected */
			if ($this->dbResults) {
				/* Return the affected rows */
				return ($this->dbAffectedRows);
			} else {
				/* !Error */
				$this->logmyerror();
				return (false);
			}   
		} else {
			/* check if query is a SELECT, SHOW, DESCRIBE or EXPLAIN  query */
			if (preg_match( "/^SELECT|SHOW|DESCRIBE|EXPLAIN/i", $query)) {
				/* let's query */
				$this->dbResults    = mysql_query($query, $this->db);
				$this->dbNumberRows = $this->count($this->dbResults);

				/* check how many rows are affected */
				if ($this->dbNumberRows) {
					/* Return the results */
					return ($this->dbResults);
				} else {
					/* !Error */
					#$this->logmyerror();
					return (false);
				}
			} else {
				$errmsg = "[!] Fatal Error: you query '$query' is not a valid SQL statement Please Check and Try again\n" ;
				$this->logmyerror(false, $errmsg);
				return (-1);
			}
		}
	}
	
	/**
	 * @Function name: qry
	 * @Purpose:       unconditional query
	 * @Parameters:    $query (SQL Query String)
	 * @Return value:  
	 */
	public function qry($query)
	{
		return (mysql_query( $query, $this->db ));
	}
    
	/**
	 * @Function name: asfetch
	 * @Purpose:       To get an sql result in associative array
	 * @Parameters:    $row (SQL Result Resource)
	 * @Return value:  $row (SQL Result Array(Assoc))
	 */
	public function asfetch($row)
	{
		return mysql_fetch_assoc($row);
	}

	/**
	 * @Function name: arfetch
	 * @Purpose:       To get an sql result in  array
	 * @Parameters:    $row (SQL Result Resource)
	 * @Return value:  $row (SQL Result Array)
	 */
	public function arfetch($row)
	{
		return mysql_fetch_array($row);
	}
	
	/**
	 * @Function name: count
	 * @Purpose:       To get the num_rows of an sql result
	 * @Parameters:    $row (SQL Result Resource)
	 * @Return value:  $count (SQL Result Count)
	 */
	public function count($res)
	{
		return mysql_num_rows($res);
	}

	/**
	 * @Function name: secure
	 * @Purpose:       To protect against SQLi
	 * @Parameters:    $str ( Data to strip :)
	 * @Return value:  $str ( Stripped Data :)
	 */
	public function secure($str)
	{
		return (addslashes(mysql_real_escape_string($str)));
	}

	/**
	 * @Function name: chkint
	 * @Purpose:       To protect against SQLi through ints(eg ?id=)
	 * @Parameters:    $str ( Data to check Must be int and must be positive and no float or double :=)
	 * @Return value:  $boolean ( True | False :)
	 */
	public function chkint($str)
	{
		# XXX: Regex would be much better so that we can escape tricky
		# SQLi like %20%41...
		return ((is_numeric($str)) ? True : False);
	}

	/**
	 * @Function name: close
	 * @Purpose:       To close an open database
	 * @Parameters:    $db_handle
	 * @Return value:  NONE
	 */
	 public function close($db_handle)
	 {
		mysql_close($db_handle);
	 }
	 
	 /**
	  * @Function name: errorMessage
	  * @Purpose:       Gives last Error Message
	  * @Parameters:    NONE
	  * @Return Value:  String
	  */
	  public function errorMessage()
	  {
		return (mysql_error());
	  }

	 /**
	  * @Function name: log
	  * @purpose:       log errors happening here ^^
	  * @Parameters:    null
	  * @Return value:  null
	  */
	private function logmyerror($display=false, $msg="")
	{
		if ($msg == "")
			$msg = mysql_error();
		cLogger::log("mysql_error", array("msg"  => $msg));

		if ($display)
			cLogger::showerror($msg);
	}
}
?>
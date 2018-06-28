<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

//-------------------------
// 1. database management
//-------------------------

	// db object
	$g_db = null;

	class db {
		var $conn;
		var $trans;
		var $sql_result;

		public function __construct(){
			$this->conn = null;
			$this->trans = false;
		}

		static function getDB() {
			global $g_db;
			if ($g_db == null)
			{
				$g_db = new db;
			}
			
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$g_db->connect();
			}

			return $g_db;
		}

		// connect to database
		function connect()
		{
			global $connection;
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				if ($this->conn == null) {
					$this->conn = @mysqli_connect (DB_HOSTNAME . ":" . DB_PORT, DB_USER, DB_PASSWORD, DB_NAME);
							//or die("Database Connection Failed");
					if (mysqli_connect_errno()) {
						die('Database Connection Failed: ' . mysqli_connect_error());
					}
					$connection = $this->conn;
					
					mysqli_select_db ($this->conn, DB_NAME)
					or die ("Could not select database");

					$sql = "SET NAMES utf8";
					@mysqli_query($this->conn, $sql);

					//$sql = "SET GLOBAL time_zone = '" . TIME_ZONE . "'";
					//@mysql_query($sql, $this->conn);

					$sql = "SET time_zone = '" . _time_zone() . "'";
					@mysqli_query($this->conn, $sql);
				}
			}

			return TRUE;
		}

		function set_time_zone($time_zone)
		{
			$sql = "SET time_zone = '" . $time_zone . "'";
			@mysqli_query($this->conn, $sql);
		}

		// close the connection of database
		function close()
		{
		//	    mysql_close($this->conn);
		}	
		
		// execute SQL command (SELECT)
		public function query($sql)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$this->sql_result = mysqli_query ($this->conn, $sql);

				if (IS_CREATEMOCKUP) mockupHelper::createMockup($sql, $this->sql_result);

				return  $this->sql_result ? ERR_OK : ERR_SQL;
			}
			else { // IS_MOCKUP
				$this->sql_result = mockupHelper::getMockup($sql);
				return ERR_OK;
			}
		}

		// execute SQL command (SELECT COUNT, MAX)
		public function scalar($sql)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$this->sql_result = mysqli_query ($this->conn, $sql);
				
				if (IS_CREATEMOCKUP) mockupHelper::createMockup($sql, $this->sql_result);

				if (!$this->sql_result)
				{
					return null;
				}

				if (mysqli_num_rows($this->sql_result) != 1)
					return null;

				//return mysql_result($this->sql_result, 0, 0);
        return $this->mysqli_result($this->sql_result, 0, 0);
			}
			else { // IS_MOCKUP
				$this->sql_result = mockupHelper::getMockup($sql);
				return $this->sql_result->get(0, 0);
			}
		}

		// execute SQL command (INSERT, UPDATE, DELETE)
		public function execute($sql)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$this->sql_result = mysqli_query($this->conn, $sql);

				return $this->sql_result ? ERR_OK : ERR_SQL;
			}
			else { // IS_MOCKUP
				return ERR_OK;
			}
		}

		public function execute_batch($sql)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$sqls = preg_split('/;/', $sql);

				foreach($sqls as $sql) {
					if ($sql != "") {
						$this->sql_result = mysqli_query($this->conn, $sql);
					}
				}

				return ERR_OK;
			}
			else { // IS_MOCKUP
				return ERR_OK;
			}
		}

		public function execute_file($sqlpath)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$sqlfile = fopen($sqlpath, "r");
				$sql = fread($sqlfile, filesize($sqlpath));
				fclose($sqlfile);

				$sql = preg_replace('/\/\*([^*]+)\*\//', '', $sql);
				$sql = preg_replace('/\-\-([^\n]+)\n/', '', $sql);

				$sqls = preg_split('/;\r/', $sql);

				foreach($sqls as $sql) {
					$this->query($sql);
				}

				return ERR_OK;
			}
			else { // IS_MOCKUP
				return ERR_OK;
			}
		}

		public function last_id() 
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				return mysqli_insert_id($this->conn);
			}
			else { // IS_MOCKUP
				return 1;
			}
		}

		function affected_rows()
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				return mysqli_affected_rows($this->conn);
			}
			else { // IS_MOCKUP
				return 1;
			}
		}

		function fetch_array($result)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				return mysqli_fetch_array($result);
			}
			else { // IS_MOCKUP
				return $result->fetch_array();
			}
		}

		// begin transaction
		function begin()
		{
			$err = $this->execute("begin");

			$this->trans = true;
		}

		// commit transaction
		function commit()
		{
			$err = $this->execute("commit");

			$this->trans = false;
		}

		// rollback transaction
		function rollback()
		{
			$err = $this->execute("rollback");

			$this->trans = false;
		}

		function is_exist_table($tablename) {
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$sql = "SHOW TABLES WHERE tables_in_" . DB_NAME . "=" . _sql($tablename);
				$tbl = $this->scalar($sql);
				
				return ($tbl != "");
			}
		}

    /**
     * @description This method is alternative of deprecated method mysql_result()
     * @param $res
     * @param int $row
     * @param int $col
     * @return bool?
     * @author <Utpal Biswas utpal.uoda@gmail.com>
     * @modify 28/06/2018
     */
    private function mysqli_result($res,$row=0,$col=0){
      $numRows = mysqli_num_rows($res);
      if ($numRows && $row <= ($numRows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resRow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resRow[$col])){
          return $resRow[$col];
        }
      }
      return false;
    }
	};

?>
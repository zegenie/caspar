<?php

	namespace b2db;
	
	/**
	 * B2DB Connection class
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * B2DB Connection class
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Connection
	{
		/**
		 * PDO object
		 *
		 * @var \PDO
		 */
		protected $_db_connection = null;
		protected $_db_host;
		protected $_db_uname;
		protected $_db_pwd;
		protected $_db_name;
		protected $_db_type;
		protected $_db_port;
		protected $_dsn;
		protected $_tableprefix = '';
		protected $_aliascnt = 0;
		protected $_transaction_active = false;
		protected $_tables = array();
		
		/**
		 * Add a table alias to alias counter
		 *
		 * @return integer
		 */
		public function addAlias()
		{
			return $this->_aliascnt++;
		}
		
		public function __construct()
		{
		}

		/**
		 * Returns the Table object
		 *
		 * @param Table $tbl_name
		 * 
		 * @return Table
		 */
		public function getTable($tbl_name)
		{
			if (!isset($this->_tables[$tbl_name])) {
				try {
					if (!\class_exists($tbl_name)) throw new Exception("Class $tbl_name does not exist, cannot load it");
					$this->_tables[$tbl_name] = new $tbl_name($this);
					if (!isset($this->_tables[$tbl_name])) throw new Exception('Table ' . $tbl_name . ' is not loaded');
				} catch (\Exception $e) {
					throw $e;
				}
			}
			
			return $this->_tables[$tbl_name];
		}

		/**
		 * Return all tables registered
		 *
		 * @return array
		 */
		public function getTables()
		{
			return $this->_tables;
		}

		/**
		 * Returns PDO object
		 *
		 * @return \PDO
		 */
		public function getDBlink()
		{
			return $this->_db_connection;
		}

		/**
		 * returns a PDO resultset
		 *
		 * @param string $sql
		 */
		public function simpleQuery($sql)
		{
			try {
				$res = $this->getDBLink()->query($sql);
			} catch (\PDOException $e) {
				throw new Exception($e->getMessage());
			}
			return $res;
		}
		
		/**
		 * Set the DSN
		 *
		 * @param string $dsn
		 */
		public function setDSN($dsn)
		{
			$dsn_details = parse_url($dsn);
			if (!array_key_exists('scheme', $dsn_details))
				throw new Exception('This does not look like a valid DSN - cannot read the database type');

			try {
				$this->setDBtype($dsn_details['scheme']);
				$dsn_details = explode(';', $dsn_details['path']);
				foreach ($dsn_details as $dsn_detail) {
					$detail_info = explode('=', $dsn_detail);
					if (count($detail_info) != 2)
						throw new B2DBException('This does not look like a valid DSN - cannot read the connection details');

					switch ($detail_info[0]) {
						case 'host':
							$this->setHost($detail_info[1]);
							break;
						case 'port':
							$this->setPort($detail_info[1]);
							break;
						case 'dbname':
							$this->setDBname($detail_info[1]);
							break;
					}
				}
			} catch (\Exception $e) {
				throw $e;
			}
			$this->_dsn = $dsn;
		}

		/**
		 * Generate the DSN when needed
		 */
		protected function _generateDSN()
		{
			$dsn = $this->getDBtype() . ":host=" . $this->getHost();
			if ($this->getPort()) $dsn .= ';port=' . $this->getPort();
			$dsn .= ';dbname='.$this->getDBname();
			$this->_dsn = $dsn;
		}

		/**
		 * Return current DSN
		 *
		 * @return string
		 */
		public function getDSN()
		{
			if ($this->_dsn === null) $this->_generateDSN();
			return $this->_dsn;
		}

		/**
		 * Set the database host
		 *
		 * @param string $host
		 */
		public function setHost($host)
		{
			$this->_db_host = $host;
		}

		/**
		 * Return the database host
		 *
		 * @return string
		 */
		public function getHost()
		{
			return $this->_db_host;
		}

		/**
		 * Return the database port
		 *
		 * @return integer
		 */
		public function getPort()
		{
			return $this->_db_port;
		}

		/**
		 * Set the database port
		 * 
		 * @param integer $port 
		 */
		public function setPort($port)
		{
			$this->_db_port = $port;
		}

		/**
		 * Set database username
		 *
		 * @param string $uname
		 */
		public function setUname($uname)
		{
			$this->_db_uname = $uname;
		}

		/**
		 * Get database username
		 *
		 * @return string
		 */
		public function getUname()
		{
			return $this->_db_uname;
		}

		/**
		 * Set the database table prefix
		 *
		 * @param string $prefix
		 */
		public function setTablePrefix($prefix)
		{
			$this->_tableprefix = $prefix;
		}

		/**
		 * Get the database table prefix
		 *
		 * @return string
		 */
		public function getTablePrefix()
		{
			return $this->_tableprefix;
		}

		/**
		 * Set the database password
		 *
		 * @param string $upwd
		 */
		public function setPasswd($upwd)
		{
			$this->_db_pwd = $upwd;
		}

		/**
		 * Return the database password
		 *
		 * @return string
		 */
		public function getPasswd()
		{
			return $this->_db_pwd;
		}

		/**
		 * Set the database name
		 *
		 * @param string $dbname
		 */
		public function setDBname($dbname)
		{
			$this->_db_name = $dbname;
			$this->_dsn = null;
		}

		/**
		 * Get the database name
		 *
		 * @return string
		 */
		public function getDBname()
		{
			return $this->_db_name;
		}

		/**
		 * Set the database type
		 *
		 * @param string $dbtype
		 */
		public function setDBtype($dbtype)
		{
			if (Core::hasDBEngine($dbtype) == false)
				throw new Exception('The selected database is not supported: "' . $dbtype . '".');

			$this->_db_type = $dbtype;
		}

		/**
		 * Get the database type
		 *
		 * @return string
		 */
		public function getDBtype()
		{
			return $this->_db_type;
		}

		/**
		 * Try connecting to the database
		 */
		public function connect()
		{
			if (!\class_exists('\\PDO'))
				throw new B2DBException('B2DB needs the PDO PHP libraries installed. See http://php.net/PDO for more information.');

			try {
				$this->_db_connection = new \PDO(self::getDSN(), $this->getUname(), $this->getPasswd());
				if (!$this->_db_connection instanceof \PDO)
					throw new Exception('Could not connect to the database, but not caught by PDO');

				self::getDBLink()->query('SET NAMES UTF8');
			} catch (\PDOException $e) {
				throw new Exception($e->getMessage());
			} catch (Exception $e) {
				throw $e;
			}
		}

		/**
		 * Create the specified database
		 *
		 * @param string $db_name
		 */
		public function createDatabase($db_name)
		{
			$res = $this->getDBLink()->query('create database ' . $db_name);
		}

		/**
		 * Close the database connection
		 */
		public function disconnect()
		{
			$this->_db_connection = null;
		}

		/**
		 * Toggle the transaction state
		 *
		 * @param boolean $state
		 */
		public function setTransaction($state)
		{
			$this->_transaction_active = $state;
		}
		
		/**
		 * Starts a new transaction
		 */
		public function startTransaction()
		{
			return new Transaction();
		}
		
		public function isTransactionActive()
		{
			return (bool) $this->_transaction_active == Transaction::STATE_STARTED;
		}
		
	}
	
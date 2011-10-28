<?php

	namespace b2db;
	
	/**
	 * B2DB Core class
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * B2DB Core class
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Core
	{
		static protected $_sqlhits = array();
		static protected $_sqltiming;
		static protected $_throwhtmlexception = true;
		static protected $_debug_mode = true;
		static protected $_cache_path;
		static protected $_cached_column_class_properties = array();
		static protected $_cached_foreign_classes = array();

		/**
		 * Enable or disable debug mode
		 *
		 * @param boolean $debug_mode
		 */
		public static function setDebugMode($debug_mode)
		{
			self::$_debug_mode = $debug_mode;
		}

		/**
		 * Return whether or not debug mode is enabled
		 *
		 * @return boolean
		 */
		public static function isDebugMode()
		{
			return self::$_debug_mode;
		}

		/**
		 * Initialize a B2DB connection instance
		 *
		 * @param array $configuration
		 * 
		 * @return Connection
		 */
		public static function getInstance($configuration)
		{
			$b2db = new Connection();
			if (array_key_exists('hostname', $configuration)) $b2db->setHost($configuration['hostname']);
			if (array_key_exists('username', $configuration)) $b2db->setUname($configuration['username']);
			if (array_key_exists('password', $configuration)) $b2db->setPasswd($configuration['password']);
			if (array_key_exists('tableprefix', $configuration)) $b2db->setTablePrefix($configuration['tableprefix']);
			if (array_key_exists('dsn', $configuration)) $b2db->setDSN($configuration['dsn']);
			
			return $b2db;
		}

		public static function setCachePath($cache_path)
		{
			self::$_cache_path = $cache_path;
		}

		/*
		 * Store connection parameters
		 *
		 * @param string $bootstrap_location Where to save the connection parameters
		
		public static function saveConnectionParameters($bootstrap_location)
		{
			$string = "<?php\n";
			$string .= "\t/**\n";
			$string .= "\t * B2DB sql parameters\n";
			$string .= "\t *\n";
			$string .= "\t * @author Daniel Andre Eikeland <zegenie@gmail.com>\n";
			$string .= "\t * @version 2.0\n";
			$string .= "\t * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)\n";
			$string .= "\t * @package b2db\n";
			$string .= "\t * @subpackage core\n";
			$string .= "\t *\n";
			$string .= "\n";
			$string .= "\tself::setUname('".addslashes(self::getUname())."');\n";
			$string .= "\tself::setPasswd('".addslashes(self::getPasswd())."');\n";
			$string .= "\tself::setTablePrefix('".self::getTablePrefix()."');\n";
			$string .= "\n";
			$string .= "\tself::setDSN('".self::getDSN()."');\n";
			$string .= "\n";
			try
			{
				if (\file_put_contents($bootstrap_location, $string) === false)
				{
					throw new Exception('Could not save the database connection details');
				}
			}
			catch (\Exception $e)
			{
				throw $e;
			}
		} */
		
		/**
		 * Register a new SQL call
		 */
		public static function sqlHit($sql, $values, $time)
		{
			$backtrace = \debug_backtrace();
			$reserved_names = array('B2DB.class.php', 'B2DBSaveable.class.php', '\b2db\Criteria.class.php', 'B2DBCriterion.class.php', 'B2DBResultset.class.php', '\b2db\Row.class.php', '\b2db\Statement.class.php', 'B2DBTransaction.class.php', 'B2DBTable.class.php', 'B2DB.class.php', '\b2db\Criteria.class.php', 'B2DBCriterion.class.php', 'B2DBResultset.class.php', '\b2db\Row.class.php', '\b2db\Statement.class.php', 'B2DBTransaction.class.php', 'B2DBTable.class.php', 'TBGB2DBTable.class.php');

			$trace = null;
			foreach ($backtrace as $t)
			{
				if (!\in_array(\basename($t['file']), $reserved_names))
				{
					$trace = $t;
					break;
				}
			}
			
			if (!$trace)
				$trace = array('file' => 'unknown', 'line' => 'unknown');

			self::$_sqlhits[] = array('sql' => $sql, 'values' => $values, 'time' => $time, 'filename' => $trace['file'], 'line' => $trace['line']);
			self::$_sqltiming += $time;
		}

		/**
		 * Get number of SQL calls
		 *
		 * @return integer
		 */
		public static function getSQLHits()
		{
			return self::$_sqlhits;
		}
		
		public static function getSQLCount()
		{
			return \count(self::$_sqlhits) +1;
		}
		
		public static function getSQLTiming()
		{
			return self::$_sqltiming;
		}

		public static function getCachePath()
		{
			return self::$_cache_path;
		}
		
		public static function saveCache()
		{
			$cache_path = self::getCachePath();
			
			if ($cache_path !== false)
			{
				foreach (self::$_cached_column_class_properties as $class => $properties)
				{
					if ((!\file_exists($cache_path . "/{$class}.column_class_properties.cache.php") && \is_writable($cache_path)) || \is_writable($cache_path . "/{$class}.column_class_properties.cache.php"))
					{
						$content = '<?php '."\n\n";
						$content .= "\tself::\$_cached_column_class_properties['{$class}'] = array();\n";
						foreach ($properties as $property => $value)
						{
							$content .= "\tself::\$_cached_column_class_properties['{$class}']['{$property}'] = \"{$value}\";\n";
						}
						$content .= "\n\n";
						\file_put_contents($cache_path . "/{$class}.column_class_properties.cache.php", $content);
					}
				}
				
				foreach (self::$_cached_foreign_classes as $class => $properties)
				{
					if ((!\file_exists($cache_path . "/{$class}.foreign_classes.cache.php") && \is_writable($cache_path)) || \is_writable($cache_path . "/{$class}.foreign_classes.cache.php"))
					{
						$content = '<?php '."\n\n";
						$content .= "\tself::\$_cached_foreign_classes['{$class}'] = array();\n";
						foreach ($properties as $property => $value)
						{
							$content .= "\tself::\$_cached_foreign_classes['{$class}']['{$property}'] = \"{$value}\";\n";
						}
						$content .= "\n\n";
						\file_put_contents($cache_path . "/{$class}.foreign_classes.cache.php", $content);
					}
				}				
			}
		}

		/**
		 * Displays a nicely formatted exception message
		 *  
		 * @param Exception $exception
		 */
		public static function fatalError(Exception $exception)
		{
			$ob_status = \ob_get_status();
			if (!empty($ob_status) && $ob_status['status'] != PHP_OUTPUT_HANDLER_END)
			{
				\ob_end_clean();
			}
			if (self::$_throwhtmlexception)
			{
				echo "
				<style>
				body { background-color: #DFDFDF; font-family: \"Droid Sans\", \"Trebuchet MS\", \"Liberation Sans\", \"Nimbus Sans L\", \"Luxi Sans\", Verdana, sans-serif; font-size: 13px; }
				h1 { margin: 5px 0 15px 0; font-size: 18px; }
				h2 { margin: 15px 0 0 0; font-size: 15px; }
				.rounded_box {background: transparent; margin:0px;}
				.rounded_box h4 { margin-bottom: 0px; margin-top: 7px; font-size: 14px; }
				.xtop, .xbottom {display:block; background:transparent; font-size:1px;}
				.xb1, .xb2, .xb3, .xb4 {display:block; overflow:hidden;}
				.xb1, .xb2, .xb3 {height:1px;}
				.xb2, .xb3, .xb4 {background:#F9F9F9; border-left:1px solid #CCC; border-right:1px solid #CCC;}
				.xb1 {margin:0 5px; background:#CCC;}
				.xb2 {margin:0 3px; border-width:0 2px;}
				.xb3 {margin:0 2px;}
				.xb4 {height:2px; margin:0 1px;}
				.xboxcontent {display:block; background:#F9F9F9; border:0 solid #CCC; border-width:0 1px; padding: 0 5px 0 5px;}
				.xboxcontent table td.description { padding: 3px 3px 3px 0;}
				.white .xb2, .white .xb3, .white .xb4 { background: #FFF; border-color: #CCC; }
				.white .xb1 { background: #CCC; }
				.white .xboxcontent { background: #FFF; border-color: #CCC; }
				</style>
				<div class=\"rounded_box white\" style=\"margin: 30px auto 0 auto; width: 600px;\">
					<b class=\"xtop\"><b class=\"xb1\"></b><b class=\"xb2\"></b><b class=\"xb3\"></b><b class=\"xb4\"></b></b>
					<div class=\"xboxcontent\" style=\"vertical-align: middle; padding: 10px 10px 10px 15px;\">
					<h1>An error occured in the B2DB database framework</h1>
					<h2>The following error occured:</h2>
					<i>".$exception->getMessage()."</i><br>
					";
					if ($exception->getSQL())
					{
						echo "<h2>SQL was:</h2>";
						echo $exception->getSQL();
						echo '<br>';
					}
					echo "<h2>Stack trace:</h2>
					<ul>";
					foreach ($exception->getTrace() as $trace_element)
					{
						echo '<li>';
						if (\array_key_exists('class', $trace_element))
						{
							echo '<strong>'.$trace_element['class'].$trace_element['type'].$trace_element['function'].'()</strong><br>';
						}
						elseif (\array_key_exists('function', $trace_element))
						{
							echo '<strong>'.$trace_element['function'].'()</strong><br>';
						}
						else
						{
							echo '<strong>unknown function</strong><br>';
						}
						if (\array_key_exists('file', $trace_element))
						{
							echo '<span style="color: #55F;">'.$trace_element['file'].'</span>, line '.$trace_element['line'];
						}
						else
						{
							echo '<span style="color: #C95;">unknown file</span>';
						}	
						echo '</li>';
					}
					echo "
					</ul></div>
					<b class=\"xbottom\"><b class=\"xb4\"></b><b class=\"xb3\"></b><b class=\"xb2\"></b><b class=\"xb1\"></b></b>
				</div>
				";
			}
			else
			{
				echo "B2DB error\n";
				echo 'The following error occurred in ' . $e->getFile() . ' at line ' . $e->getLine() . ":\n";
				echo $e->getMessage() . "\n\n";
				echo "Trace:\n";
				echo $e->getTraceAsString() . "\n\n";
				echo self::$_db_connection->error . "\n\n";
				echo "For more information, refer to the B2DB manual.\n";
			}
		}

		/**
		 * Toggle HTML exception messages
		 *
		 * @param boolean $active
		 */
		public static function setHTMLException($active)
		{
			self::$_throwhtmlexception = $active;
		}

		/**
		 * Return whether exceptions are thrown and displayed as HTML
		 *
		 * @return boolean
		 */
		public static function throwExceptionAsHTML()
		{
			return self::$_throwhtmlexception;
		}

		/**
		 * Get available DB drivers
		 *
		 * @return array
		 */
		public static function getDBtypes()
		{
			$retarr = array();
			
			if (\class_exists('\PDO'))
			{
				$retarr['mysql'] = 'MySQL';
				$retarr['pgsql'] = 'PostgreSQL';
				$retarr['mssql'] = 'Microsoft SQL Server';
				/*
				$retarr['sqlite'] = 'SQLite';
				$retarr['sybase'] = 'Sybase';
				$retarr['dblib'] = 'DBLib';
				$retarr['firebird'] = 'Firebird';
				$retarr['ibm'] = 'IBM';
				$retarr['oci'] = 'Oracle';
				 */
			}
			else
			{
				throw new Exception('You need to have PHP PDO installed to be able to use B2DB');
			}
			
			return $retarr;
		}

		/**
		 * Whether a specific DB driver is supported
		 *
		 * @param string $driver
		 *
		 * @return boolean
		 */
		public static function hasDBEngine($driver)
		{
			return \array_key_exists($driver, self::getDBtypes());
		}

		public static function loadCachedClassFiles($class)
		{
			$filename = self::getCachePath() . "/{$class}.column_class_properties.cache.php";
			if (\file_exists($filename)) require $filename;

			$filename = self::getCachePath() . "/{$class}.foreign_classes.cache.php";
			if (\file_exists($filename)) require $filename;
		}
		
		public static function addCachedColumnClassProperty($column, $class, $property)
		{
			if (!\array_key_exists($class, self::$_cached_column_class_properties)) {
				self::$_cached_column_class_properties[$class] = array();
			}
			self::$_cached_column_class_properties[$class][$column] = $property;
		}
		
		public static function getCachedColumnClassProperty($column, $class)
		{
			self::loadCachedClassFiles($class);
			if (\array_key_exists($class, self::$_cached_column_class_properties)) {
				if (\array_key_exists($column, self::$_cached_column_class_properties[$class])) {
					return self::$_cached_column_class_properties[$class][$column];
				}
			}
		}
		
		public static function addCachedClassPropertyForeignClass($class, $property, $foreign_class)
		{
			if (!\array_key_exists($class, self::$_cached_foreign_classes)) {
				self::$_cached_foreign_classes[$class] = array();
			}
			self::$_cached_foreign_classes[$class][$property] = $foreign_class;
		}
		
		public static function getCachedClassPropertyForeignClass($class, $property)
		{
			self::loadCachedClassFiles($class);
			if (\array_key_exists($class, self::$_cached_foreign_classes)) {
				if (\array_key_exists($property, self::$_cached_foreign_classes[$class])) {
					return self::$_cached_foreign_classes[$class][$property];
				}
			}
		}
	
	}
	
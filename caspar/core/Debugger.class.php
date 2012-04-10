<?php

	namespace caspar\core;

	/**
	 * Caspar debugger
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>, Philip Kent <philip.kent@me.com>
	 * @version 1.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 * @subpackage core
	 */

	/**
	 * Caspar debugger
	 *
	 * @package caspar
	 * @subpackage core
	 */
	class Debugger
	{
		protected $_storedvars = array();
		protected $_jsonoutput = array();

		public static function display() {
			$csp_debugger = \caspar\core\Caspar::getDebugger();
			require CASPAR_PATH . 'templates' . DS . 'debugger.php';
		}

		public function __construct() {
		}

		public function getRouting() {
			return \caspar\core\Caspar::getRouting();
		}

		public function getExecutionTime() {
			return \caspar\core\Caspar::getLoadTime();
		}

		public function getLog() {
			return \caspar\core\Logging::getEntries();
		}

		public function getPartials() {
			return \caspar\core\Caspar::getVisitedPartials();
		}

		public function isAjaxRequest() {
			return false;
		}

		public function setJsonOutput($output) {
			$this->_jsonoutput = json_decode($output);
		}

		public function getJsonOutput() {
			return $this->_jsonoutput;
		}

		public function isDatabaseLoaded() {
			return false;
		}

		public function getDatabaseQueries() {
			return array();
		}

		public function storeVariable($name, $data, $file, $line) {
			if (array_key_exists($name, $this->_storedvars)) {
				throw new Exception('A variable by this name has already been stored');
			}

			$this->_storedvars[$name] = array('value' => $data, 'file' => $file, 'line' => $line);
		}

		public function unstoreVariable($name) {
			unset($this->_storedvars[$name]);
		}

		public function getStoredVariables() {
			return $this->_storedvars;
		}

		public function getCurrentPageRow() {
			$cspdbgrow = time();
			$csp_debugger = $this;
			require CASPAR_PATH . 'templates' . DS . 'debugger-row.php';
		}
	}

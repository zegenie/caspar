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
		/**
		 * Any variables stored for output
		 *
		 * @var array
		 */
		protected $_storedvars = array();

		/**
		 * Fields in the JSON output, AJAX only
		 *
		 * @var array
		 */
		protected $_jsonoutput = array();

		/**
		 * Display the debugger button and main div on the page, includes JS functions
		 */
		public static function display() {
			$csp_debugger = \caspar\core\Caspar::getDebugger();
			require CASPAR_PATH . 'templates' . DS . 'debugger.php';
		}

		/**
		 * Shortcut to get Routing object
		 *
		 * @return Routing
		 */
		public function getRouting() {
			return \caspar\core\Caspar::getRouting();
		}

		/**
		 * Shortcut to get execution time up to this point
		 *
		 * @return integer
		 */
		public function getExecutionTime() {
			return \caspar\core\Caspar::getLoadTime();
		}

		/**
		 * Shortcut to get array of log entries
		 *
		 * @return array
		 */
		public function getLog() {
			return \caspar\core\Logging::getEntries();
		}

		/**
		 * Shortcut to get array of visited partials (templates and actions)
		 *
		 * @return array
		 */
		public function getPartials() {
			return \caspar\core\Caspar::getVisitedPartials();
		}

		/**
		 * Shortcut to see if request is AJAX
		 *
		 * @return boolean
		 */
		public function isAjaxRequest() {
			return \caspar\core\Caspar::getRequest()->isAjaxCall();
		}

		/**
		 * Add output that will be sent to JSON for AJAX query to the debugger
		 *
		 * @param array $output The output that will be passed to json_encode
		 */
		public function setJsonOutput($output) {
			$this->_jsonoutput = $output;
		}

		/**
		 * Get an array of key/value pairs in the JSON output for an AJAX query
		 *
		 * @return array
		 */
		public function getJsonOutput() {
			return $this->_jsonoutput;
		}

		/**
		 * Add a variable to be var_dump'ed in the debugger for inspection
		 *
		 * @param string $name Name of the variable to show in the debugger
		 * @param mixed $data The data to show in the debugger
		 */
		public function storeVariable($name, $data, $file = __FILE__, $line = __LINE__) {
			if (array_key_exists($name, $this->_storedvars)) {
				throw new Exception('A variable by this name has already been stored');
			}

			$this->_storedvars[$name] = array('value' => $data, 'file' => $file, 'line' => $line);
		}

		/**
		 * Remove a variable from the list of stored variables
		 *
		 * @param string $name The variable to remove
		 */
		public function unstoreVariable($name) {
			unset($this->_storedvars[$name]);
		}

		/**
		 * Get an array of stored variables (will be var_dumped in debugger, to aid in inspecting variables as they change)
		 *
		 * @return array
		 */
		public function getStoredVariables() {
			return $this->_storedvars;
		}

		/**
		 * Print the debugger row for this page on-screen
		 */
		public function getCurrentPageRow() {
			$cspdbgrow = time();
			$csp_debugger = $this;
			require CASPAR_PATH . 'templates' . DS . 'debugger-row.php';
		}

		/**
		 * Get the debugger row for this page as a string
		 *
		 * @return string
		 */
		public function returnCurrentPageRow() {
			ob_start();
			$this->getCurrentPageRow();
			$row = ob_get_contents();
			ob_end_clean();
			return $row;
		}
	}

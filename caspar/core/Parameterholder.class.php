<?php

	namespace caspar\core;
	
	/**
	 * Parameter holder class used in the MVC part of the framework for TBGAction and TBGActionComponent
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 1.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 * @subpackage mvc
	 */

	/**
	 * Parameter holder class used in the MVC part of the framework for TBGAction and TBGActionComponent
	 *
	 * @package caspar
	 * @subpackage mvc
	 */
	class Parameterholder
	{
		
		protected $_property_list = array();
		
		public function __set($key, $value)
		{
			$this->_property_list[$key] = $value;
		}
		
		public function __get($property)
		{
			return ($this->hasParameter($property)) ? $this->_property_list[$property] : null; 
		}
		
		public function hasParameter($key)
		{
			return $this->__isset($key);
		}
		
		public function getParameterHolder()
		{
			return $this->_property_list;
		}
		
		public function __isset($key)
		{
			return (array_key_exists($key, $this->_property_list)) ? true : false; 
		}
		
	}

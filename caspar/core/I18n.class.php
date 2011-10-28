<?php

	namespace caspar\core;

	/**
	 * I18n class
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 1.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 * @subpackage core
	 */

	/**
	 * I18n class
	 *
	 * @package caspar
	 * @subpackage core
	 */
	class I18n
	{

		protected $_strings = array();
		
		protected $_missing_strings = array();
		
		protected $_language = null;
		
		protected $_charset = 'utf-8';
		
		protected $_datetime_formats = array();
		
		public static function setup($language)
		{
			return $_i18n;
		}

		public function __construct($language = 'en_US')
		{
			if (!file_exists($this->getStringsFilename($language))) {
				Logging::log('Selected language not available, trying "en_US" as a last attempt', 'i18n', Logging::LEVEL_NOTICE);
				$this->_language = 'en_US';
				if (!file_exists($this->getStringsFilename($this->_language))) {
					throw new \Exception('The selected language is not available');
				}
			}
			$this->_language = $language;
		}

		public function getStringsFilename($language = null)
		{
			$language = ($language === null) ? $this->_language : $language;
			return \CASPAR_PATH . 'i18n' . DS . $language . DS . 'strings.inc.php';
		}
		
		public function initialize()
		{
			$filename = \CASPAR_PATH . 'caspar' . DS . 'i18n' . DS . $this->_language . DS . 'initialize.inc.php';
			if (file_exists($filename)) {
				Logging::log("Initiating with file '{$filename}", 'i18n');
				include $filename;
			}
			$this->loadStrings();
			foreach (Caspar::getModules() as $module_name => $module) {
				$this->loadStrings($module_name);
			}
			
		}
		
		public function setLanguage($language)
		{
			if ($language != $this->_language) {
				$this->_language = $language;
				$this->initialize();
			}
		}
		
		public function getMissingStrings()
		{
			return $this->_missing_strings;
		}

		public function addMissingStringsToStringsFile()
		{
			return;
			$strings = array();
			foreach ($this->getMissingStrings() as $string => $truth)
			{
				if (mb_strpos($string, '"') !== false && mb_strpos($string, "'") !== false)
				{
					$string = str_replace('"', '\"', $string);
					$strings[] = '$strings["'.$string.'"] = "'.$string."\";";
				}
				elseif (mb_strpos($string, "'") !== false)
				{
					$strings[] = '$strings["'.$string.'"] = "'.$string."\";";
				}
				else
				{
					$strings[] = '$strings[\''.$string.'\'] = \''.$string."';";
				}
			}
			file_put_contents("\n\t".$this->getStringsFilename(), join("\n\t", $strings), FILE_APPEND);
		}
		
		public function setCharset($charset)
		{
			$this->_charset = $charset;
		}

		public function getCurrentLanguage()
		{
			return $this->_language;
		}
		
		public function getHTMLLanguage()
		{
			$lang = explode('_', $this->getCurrentLanguage());
			return $lang[0];
		}
		
		public function getCharset()
		{
			return $this->_charset;
		}
		
		public function getLangCharset()
		{
			return $this->_charset;
		}
		
		public function loadModuleStrings($module)
		{
			$this->loadStrings($module);
		}
		
		protected function loadStrings($module = null)
		{
			$filename = '';
			if ($module !== null) {
				if (file_exists(CASPAR_CORE_PATH . 'i18n' . DS . $this->_language . DS . "{$module}.inc.php")) {
					$filename = CASPAR_CORE_PATH . 'i18n' . DS . $this->_language . DS . "{$module}.inc.php";
				} else {
					$filename = CASPAR_MODULES_PATH . $module . DS . 'i18n' . DS . $this->_language . DS . "{$module}.inc.php";
				}
			} else {
				$filename = $this->getStringsFilename();
			}

			if (file_exists($filename)) {
				Logging::log("Loading strings from file '{$filename}", 'i18n');
				$strings = array();
				require $filename;
			} else {
				$message = 'Could not find language file ' . $filename;
				Logging::log($message, 'i18n', Logging::LEVEL_NOTICE);
			}
			$this->addStrings($strings);
		}
		
		public function addString($key, $translation)
		{
			$this->_strings[$key] = $translation;
		}
		
		public function addStrings($strings)
		{
			if (is_array($strings)) {
				foreach ($strings as $key => $translation) {
					$this->_strings[$key] = $translation;
				}
			}
		}
		
		public static function getLanguages()
		{
			$retarr = array();
			$cp_handle = opendir(CASPAR_CORE_PATH . 'i18n');
			while ($classfile = readdir($cp_handle)) {
				if (mb_strstr($classfile, '.') == '' && file_exists(CASPAR_CORE_PATH . 'i18n/' . $classfile . '/language')) {
					$retarr[$classfile] = file_get_contents(CASPAR_CORE_PATH . 'i18n/' . $classfile . '/language');
				}
			}
			
			return $retarr;
		}

		public function hasTranslatedTemplate($template, $is_component = false)
		{
			if (mb_strpos($template, '/')) {
				$templateinfo = explode('/', $template);
				$module = $templateinfo[0];
				$templatefile = ($is_component) ? '_' . $templateinfo[1] . '.inc.php' : $templateinfo[1] . '.' . Caspar::getRequest()->getRequestedFormat() . '.php';
			} else {
				$module = Caspar::getRouting()->getCurrentRouteModule();
				$templatefile = ($is_component) ? '_' . $template . '.inc.php' : $template . '.' . Caspar::getRequest()->getRequestedFormat() . '.php';
			}
			if (file_exists(CASPAR_MODULES_PATH . $module . DS . 'i18n' . DS . $this->_language . DS . 'templates' . DS . $templatefile)) {
				return CASPAR_MODULES_PATH . $module . DS . 'i18n' . DS . $this->_language . DS . 'templates' . DS . $templatefile;
			} elseif (file_exists(CASPAR_CORE_PATH . 'i18n' . DS . $this->getCurrentLanguage() . DS . 'templates' . DS . $module . DS . $templatefile)) {
				return CASPAR_CORE_PATH . 'i18n' . DS . $this->getCurrentLanguage() . DS . 'templates' . DS . $module . DS . $templatefile;
			}
			return false;
		}

		public function __($text, $replacements = array(), $html_decode = false)
		{
			if (isset($this->_strings[$text])) {
				$retstring = $this->_strings[$text];
			} else {
				$retstring = $text;
				Logging::log('The text "' . $text . '" does not exist in list of translated strings.', 'i18n');
				$this->_missing_strings[$text] = true;
			}
			if (!empty($replacements)) {
				$tmp = array();
				foreach ($replacements as $key => $value) {
        			$tmp[$key] = $value;
    				$retstring = str_replace(array_keys($tmp), array_values($tmp), $retstring);
				}
			}
			if ($html_decode) {
				$retstring = html_entity_decode($retstring);
			}
			return $retstring;
		}
		
		/** 
		 * Set local date and time formats
		 * 
		 * @param $formats array list of applicable formats for this local
		 * 
		 */
		public function setDateTimeFormats($formats)
		{
			if(is_array($formats)) {
				$this->_datetime_formats = $formats;
			}
		}
		
		/** 
		 * Return localized date and time format
		 * @see http://php.net/manual/en/function.date.php
		 * 
		 * @param $id integer ID of format
		 * 
		 * @return string
		 * 
		 */
		public function getDateTimeFormat($id)
		{
			if(array_key_exists($id, $this->_datetime_formats)) {
				 return $this->_datetime_formats[$id];
			}
			switch ($id) {
				case 1 : // 14:45 - Thu Dec 30, 2010
					$format = '%H:%M - %a %b %d, %Y';
					break;
				case 2 : // 14:45 - Thu 30.m, 2010
					$format = '%H:%M - %a %d.m, %Y';
					break;
				case 3 : // Thu Dec 30 14:45
					$format = '%a %b %d %H:%M';
					break;
				case 4 : // Dec 30 14:45
					$format = '%b %d %H:%M';
					break;
				case 5 : // December 30, 2010
					$format = '%B %d, %Y';
					break;
				case 6 : // December 30, 2010 (14:45)
					$format = '%B %d, %Y (%H:%M)';
					break;
				case 7 : // Thursday 30 December, 2010 (14:45)
					$format = '%A %d %B, %Y (%H:%M)';
					break;
				case 8 : // Dec 30, 2010 14:45
					$format = '%b %d, %Y %H:%M';
					break;
				case 9 : // Dec 30, 2010 - 14:45
					$format = '%b %d, %Y - %H:%M';
					break;
				case 10 : // Dec 30, 2010 (14:45)
					$format = '%b %d, %Y (%H:%M)';
					break;
				case 11 : // December
					$format = '%B';
					break;
				case 12 : // Dec 30
					$format = '%b %d';
					break;
				case 13 : // Thu
					$format = '%a';
					break;
				case 14 : // 14:45
					$format = '%H:%M';
					break;
				case 15 : // Dec 30, 2010
					$format = '%b %d, %Y';
					break;
				case 16 : // 14h 45m
					$format = '%Gh %im';
					break;
				case 17 : // Thu, 30 December 2010 14:45:45 GMT
					$format = '%a, %d %b %Y %H:%M:%S GMT';
					break;
				default : // local server setting
					$format = '%c';
			}
			return $format;
		}

		/**
		 * Returns a formatted string of the given timestamp
		 *
		 * @param integer $tstamp the timestamp to format
		 * @param integer $format[optional] the format
		 */
		public function formatTime($tstamp, $format = 0)
		{

			switch ($format)
			{
				case 1:
					$tstring = strftime($this->getDateTimeFormat(1), $tstamp);
					break;
				case 2:
					$tstring = strftime($this->getDateTimeFormat(2), $tstamp);
					break;
				case 3:
					$tstring = strftime($this->getDateTimeFormat(3), $tstamp);
					break;
				case 4:
					$tstring = strftime($this->getDateTimeFormat(4), $tstamp);
					break;
				case 5:
					$tstring = strftime($this->getDateTimeFormat(5), $tstamp);
					break;
				case 6:
					$tstring = strftime($this->getDateTimeFormat(6), $tstamp);
					break;
				case 7:
					$tstring = strftime($this->getDateTimeFormat(7), $tstamp);
					break;
				case 8:
					$tstring = strftime($this->getDateTimeFormat(8), $tstamp);
					break;
				case 9:
					$tstring = strftime($this->getDateTimeFormat(9), $tstamp);
					break;
				case 10:
					$tstring = strftime($this->getDateTimeFormat(10), $tstamp);
					break;
				case 11:
					$tstring = strftime($this->getDateTimeFormat(9), $tstamp);
					break;
				case 12:
					$tstring = '';
					if (date('dmY', $tstamp) == date('dmY'))
					{
						$tstring .= __('Today') . ', ';
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
					{
						$tstring .= __('Yesterday') . ', ';
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
					{
						$tstring .= __('Tomorrow') . ', ';
					}
					else
					{
						$tstring .= strftime($this->getDateTimeFormat(12) . ', ', $tstamp);
					}
					$tstring .= strftime($this->getDateTimeFormat(14), $tstamp);
					break;
				case 13:
					$tstring = '';
					if (date('dmY', $tstamp) == date('dmY'))
					{
						//$tstring .= __('Today') . ', ';
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
					{
						$tstring .= __('Yesterday') . ', ';
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
					{
						$tstring .= __('Tomorrow') . ', ';
					}
					else
					{
						$tstring .= strftime($this->getDateTimeFormat(12) . ', ', $tstamp);
					}
					$tstring .= strftime($this->getDateTimeFormat(14), $tstamp);
					break;
				case 14:
					$tstring = '';
					if (date('dmY', $tstamp) == date('dmY'))
					{
						$tstring .= __('Today');
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
					{
						$tstring .= __('Yesterday');
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
					{
						$tstring .= __('Tomorrow');
					}
					else
					{
						$tstring .= strftime($this->getDateTimeFormat(12), $tstamp);
					}
					break;
				case 15:
					$tstring = strftime($this->getDateTimeFormat(11), $tstamp);
					break;
				case 16:
					$tstring = strftime($this->getDateTimeFormat(12), $tstamp);
					break;
				case 17:
					$tstring = strftime($this->getDateTimeFormat(13), $tstamp);
					break;
				case 18:
					$tstring = strftime($this->getDateTimeFormat(16), $tstamp);
					break;
				case 19:
					$tstring = strftime($this->getDateTimeFormat(14), $tstamp);
					break;
				case 20:
					$tstring = '';
					if (date('dmY', $tstamp) == date('dmY'))
					{
						$tstring .= __('Today') . ' (' . strftime('%H:%M', $tstamp) . ')';
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
					{
						$tstring .= __('Yesterday') . ' (' . strftime('%H:%M', $tstamp) . ')';
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
					{
						$tstring .= __('Tomorrow') . ' (' . strftime('%H:%M', $tstamp) . ')';
					}
					else
					{
						$tstring .= strftime($this->getDateTimeFormat(15), $tstamp);
					}
					break;
				case 21:
					$tstring = (TBGContext::isCLI()) ? strftime('%a, %d %b %Y %H:%M:%S GMT', $tstamp) : strftime($this->getDateTimeFormat(17), $tstamp);
					if (\caspar\core\Caspar::getUser()->getTimezone() > 0) $tstring .= '+';
					if (\caspar\core\Caspar::getUser()->getTimezone() < 0) $tstring .= '-';
					if (\caspar\core\Caspar::getUser()->getTimezone() != 0) $tstring .= \caspar\core\Caspar::getUser()->getTimezone();
					break;
				case 22:
					$tstring = strftime($this->getDateTimeFormat(15), $tstamp);
					break;
				case 23:
					$tstring = '';
					if (date('dmY', $tstamp) == date('dmY'))
					{
						$tstring .= __('Today');
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
					{
						$tstring .= __('Yesterday');
					}
					elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
					{
						$tstring .= __('Tomorrow');
					}
					else
					{
						$tstring .= strftime($this->getDateTimeFormat(15), $tstamp);
					}
					break;
				default:
					return $tstamp;
			}
			return htmlentities($tstring, ENT_COMPAT, $this->getCharset());
		}
		
	}

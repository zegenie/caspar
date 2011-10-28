<?php

	namespace caspar\core;

	/**
	 * Cache class
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 1.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 * @subpackage core
	 */

	/**
	 * Cache class
	 *
	 * @package caspar
	 * @subpackage core
	 */
	class Cache
	{

		const KEY_SCOPES = '_scopes';
		const KEY_ROUTES_ALL = '_routes';
		const KEY_ROUTES_APPLICATION = '_routes_premodules';
		const KEY_B2DB_CONFIG = '_b2db_config';
		const KEY_PERMISSIONS_CACHE = '_permissions';
		const KEY_USERSTATES_CACHE = 'TBGUserstate::getAll';
		const KEY_SETTINGS = '_settings';
		const KEY_TEXTPARSER_ISSUE_REGEX = '\thebuggenie\core\TextParser::getIssueRegex';
		
		protected static $_enabled = false;
		protected static $_filecache_enabled = false;
		protected static $_cache_path;
		
		public static function get($key)
		{
			if (!self::isEnabled()) return null;
			$success = false;
			$var = apc_fetch($key, $success);
			return ($success) ? $var : null;
		}

		public static function has($key)
		{
			if (!self::isEnabled()) return false;
			$success = false;
			apc_fetch($key, $success);
			return $success;
		}
		
		public static function add($key, $value)
		{
			if (!self::isEnabled())
			{
				Logging::log('Key "' . $key . '" not cached', 'cache');
				return false;
			}
			apc_store($key, $value);
			Logging::log('Caching value for key "' . $key . '"', 'cache');
			return true;
		}
		
		public static function delete($key)
		{
			if (!self::isEnabled()) return null;
			apc_delete($key);
		}
		
		public static function fileGet($key)
		{
			if (!self::$_filecache_enabled) return null;
			$filename = self::$_cache_path . $key . '.cache';
			if (!file_exists($filename)) return null;
			
			$value = unserialize(file_get_contents($filename));
			return $value;
		}
		
		public static function fileAdd($key, $value)
		{
			if (!self::$_filecache_enabled) return null;
			$filename = THEBUGGENIE_CORE_PATH . 'cache' . DS . $key . '.cache';
			file_put_contents($filename, serialize($value));
		}
		
		public static function fileDelete($key)
		{
			if (!self::$_filecache_enabled) return null;
			$filename = THEBUGGENIE_CORE_PATH . 'cache' . DS . $key . '.cache';
			unlink($filename);
		}
		
		public static function isEnabled()
		{
			if (self::$_enabled)
			{
				self::$_enabled = function_exists('apc_add');
			}
			return self::$_enabled;
		}
	}

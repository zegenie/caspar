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

		const TYPE_APC = 1;

		protected static $_in_memory_enabled;
		protected static $_in_memory_type;
		protected static $_filecache_enabled;
		protected static $_filecache_path;
		
		public static function setInMemorycacheStrategy($enabled, $type = null)
		{
			self::$_in_memory_enabled = $enabled;
			if (self::$_in_memory_enabled) {
				self::$_in_memory_type = $type;
			}
		}

		public static function setFilecacheStrategy($enabled, $path = null)
		{
			self::$_filecache_enabled = $enabled;
			if (self::$_filecache_enabled) {
				if (!file_exists($path)) {
					throw new \RuntimeException("Configured cache path ({$path}) is not writable. Please check your configuration.");
				}
				self::$_filecache_path = $path;
			}
		}

		public static function get($key)
		{
			if (!self::isInMemorycacheEnabled()) return null;
			$success = false;
			$var = apc_fetch($key, $success);
			return ($success) ? $var : null;
		}

		public static function has($key)
		{
			if (!self::isInMemorycacheEnabled()) return false;
			$success = false;
			apc_fetch($key, $success);
			return $success;
		}
		
		public static function add($key, $value)
		{
			if (!self::isInMemorycacheEnabled()) {
				Logging::log('Key "' . $key . '" not cached (cache disabled)', 'cache');
				return false;
			}
			apc_store($key, $value);
			Logging::log('Caching value for key "' . $key . '"', 'cache');
			return true;
		}
		
		public static function delete($key)
		{
			if (!self::isInMemorycacheEnabled()) return null;
			apc_delete($key);
		}
		
		public static function fileHas($key)
		{
			if (!self::$_filecache_enabled) return false;

			$filename = self::$_filecache_path . $key . '.cache';
			return file_exists($filename);
		}
		
		public static function fileGet($key)
		{
			if (!self::$_filecache_enabled) return false;

			$filename = self::$_filecache_path . $key . '.cache';
			if (!file_exists($filename)) return null;

			$value = unserialize(file_get_contents($filename));
			return $value;
		}

		public static function fileAdd($key, $value)
		{
			if (!self::$_filecache_enabled) return false;
			$filename = self::$_filecache_path . $key . '.cache';
			file_put_contents($filename, serialize($value));
		}
		
		public static function fileDelete($key)
		{
			if (!self::$_filecache_enabled) return false;

			$filename = self::$_filecache_path . $key . '.cache';
			unlink($filename);
		}

		public static function isInMemorycacheEnabled()
		{
			return self::$_in_memory_enabled;
		}

		public static function isFilecacheEnabled()
		{
			return self::$_filecache_enabled;
		}

	}

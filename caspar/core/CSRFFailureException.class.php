<?php

	namespace caspar\core;

	/**
	 * Exception used for csrf failure
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 1.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 * @subpackage core
	 */

	/**
	 * This exception is thrown whenever the request cannot be validated
	 * against the csrf_token stored in the session
	 *
	 * @package caspar
	 * @subpackage core
	 */
	class CSRFFailureException extends \Exception
	{
		
	}


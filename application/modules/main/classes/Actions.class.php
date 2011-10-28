<?php 

	namespace application\modules\main;

	use \caspar\core\Request;

	/**
	 * Actions for the main module
	 */
	class Actions extends \caspar\core\Actions
	{

		/**
		 * Index page
		 *  
		 * @param Request $request
		 */
		public function runIndex(Request $request)
		{
		}

		public function runNotFound(Request $request)
		{
			$this->getResponse()->setHttpStatus(404);
		}

	}
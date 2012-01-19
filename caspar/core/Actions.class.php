<?php

	namespace caspar\core;

	/**
	 * Action class used in the MVC part of the framework
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 1.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 * @subpackage mvc
	 */

	/**
	 * Action class used in the MVC part of the framework
	 *
	 * @package caspar
	 * @subpackage mvc
	 */
	class Actions extends Parameterholder
	{
		
		/**
		 * Forward the user to a specified url
		 * 
		 * @param string $url The URL to forward to
		 * @param integer $code[optional] HTTP status code
		 * @param integer $method[optional] 2 for meta redirect instead of header
		 */
		public function forward($url, $code = 200)
		{
			if (Caspar::getRequest()->isAjaxCall() || Caspar::getRequest()->getRequestedFormat() == 'json')
			{
				$this->getResponse()->ajaxResponseText($code, Caspar::getMessageAndClear('forward'));
			}
			\caspar\core\Logging::log("Forwarding to url {$url}");
			
			\caspar\core\Logging::log('Triggering header redirect function');
			$this->getResponse()->headerRedirect($url, $code);
		}

		/**
		 * Function that is executed before any actions in an action class
		 * 
		 * @param TBGRequest $request The request object
		 * @param string $action The action that is being triggered
		 */
		public function preExecute(Request $request, $action) { }

		/**
		 * Redirect from one action method to another in the same action
		 * 
		 * @param string $redirect_to The method to redirect to
		 */
		public function redirect($redirect_to)
		{
			$actionName = 'run' . ucfirst($redirect_to);
			$this->getResponse()->setTemplate(mb_strtolower($redirect_to) . '.' . Caspar::getRequest()->getRequestedFormat() . '.php');
			if (method_exists($this, $actionName))
			{
				return $this->$actionName(Caspar::getRequest());
			}
			throw new Exception("The action \"{$actionName}\" does not exist in ".get_class($this));
		}
		
		/**
		 * Render a string
		 * 
		 * @param string $text The text to render
		 * 
		 * @return boolean
		 */
		public function renderText($text)
		{
			echo $text;
			return true;
		}
		
		/**
		 * Renders JSON output, also takes care of setting the correct headers
		 * 
		 * @param array $content The array to render
		 *  
		 * @return boolean
		 */
		public function renderJSON($text = array())
		{
			$this->getResponse()->setContentType('application/json');
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			echo json_encode($text);
			return true;
		}
		
		/**
		 * Return the response object
		 * 
		 * @return TBGResponse
		 */
		protected function getResponse()
		{
			return Caspar::getResponse();
		}
		
		protected function getRouting()
		{
			return Caspar::getRouting();
		}

		protected function getUser()
		{
			return Caspar::getUser();
		}
		
		protected function b2db($config = 'default')
		{
			return Caspar::getB2DBInstance($config);
		}
		
		/**
		 * Sets the response to 404 and shows an error, with an optional message
		 * 
		 * @param string $message[optional] The message
		 */
		public function return404($message = null)
		{
			if (Caspar::getRequest()->isAjaxCall() || Caspar::getRequest()->getRequestedFormat() == 'json')
			{
				$this->getResponse()->ajaxResponseText(404, $message);
			}

			$this->message = $message;
			$this->getResponse()->setHttpStatus(404);
			$this->getResponse()->setTemplate('main/notfound');
			return false;
		}
		
		/**
		 * Forward the user with HTTP status code 403 and an (optional) message
		 * 
		 * @param string $message[optional] The message
		 */
		public function forward403($message = null)
		{
			$this->forward403unless(false, $message);
		}
		
		/**
		 * Forward the user with HTTP status code 403 and an (optional) message
		 * based on a boolean check
		 * 
		 * @param boolean $condition
		 * @param string $message[optional] The message
		 */
		public function forward403unless($condition, $message = null)
		{
			if (!$condition)
			{
				$message = ($message === null) ? Caspar::getI18n()->__("You are not allowed to access to this page") : $message;
				$this->getResponse()->setHttpStatus(403);
				$this->message = $message;
				$this->getResponse()->setTemplate('main/forbidden');
			}
		}
		
		public function forward403if($condition, $message = null)
		{
			$this->forward403unless(!$condition, $message);
		}
		
		/**
		 * Render a template
		 * 
		 * @param string $template the template name
		 * @param array $params template parameters
		 * 
		 * @return boolean 
		 */
		public function renderTemplate($template, $params = array())
		{
			echo \caspar\core\ActionComponents::includeTemplate($template, $params);
			return true;
		}

		/**
		 * Render a component
		 * 
		 * @param string $template the component name
		 * @param array $params component parameters
		 * 
		 * @return boolean
		 */
		public function renderComponent($template, $params = array())
		{
			echo \caspar\core\ActionComponents::includeComponent($template, $params);
			return true;
		}

		/**
		 * Returns the HTML output from a component, but doesn't render it
		 *
		 * @param string $template the component name
		 * @param array $params component parameters
		 *
		 * @return boolean
		 */
		public static function returnComponentHTML($template, $params = array())
		{
			$current_content = ob_get_clean();
			ob_start('mb_output_handler');
			echo \caspar\core\ActionComponents::includeComponent($template, $params);
			$component_content = ob_get_clean();
			ob_start('mb_output_handler');
			echo $current_content;
			return $component_content;
		}
		
		/**
		 * Returns the HTML output from a component, but doesn't render it
		 * 
		 * @param string $template the component name
		 * @param array $params component parameters
		 * 
		 * @return boolean
		 */
		public function getComponentHTML($template, $params = array())
		{
			return self::returnComponentHTML($template, $params);
		}

		/**
		 * Returns the HTML output from a template, but doesn't render it
		 *
		 * @param string $template the template name
		 * @param array $params template parameters
		 *
		 * @return boolean
		 */
		public static function returnTemplateHTML($template, $params = array())
		{
			$current_content = ob_get_clean();
			ob_start('mb_output_handler');
			echo \caspar\core\ActionComponents::includeTemplate($template, $params);
			$template_content = ob_get_clean();
			ob_start('mb_output_handler');
			echo $current_content;
			return $template_content;
		}

		/**
		 * Returns the HTML output from a template, but doesn't render it
		 * 
		 * @param string $template the template name
		 * @param array $params template parameters
		 * 
		 * @return boolean
		 */
		public function getTemplateHTML($template, $params = array())
		{
			return self::returnTemplateHTML($template, $params);
		}
		
	}

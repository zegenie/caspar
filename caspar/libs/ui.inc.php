<?php

	/**
	 * UI functions
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 */
	
	/**
	 * Returns an <img> tag with a specified image
	 * 
	 * @param string $image image source
	 * @param array $params[optional] html parameters
	 * @param boolean $notheme[optional] whether this is a themed image or a top level path
	 * @param string $module whether this is a module image or in the core image set
	 * @param boolean $relative whether the path is relative or absolute
	 * 
	 * @return string
	 */
	function image_tag($image, $params = array(), $relative = true)
	{
		$params['src'] = $image;

		if (!$relative) {
			$params['src'] = \caspar\core\Caspar::getBasePath() . $params['src'];
		}
		if (!array_key_exists('alt', $params) || !$params['alt']) {
			$params['alt'] = $image;
		}

		return "<img " . parseHTMLoptions($params) . '>';
	}
	
	/**
	 * Returns an <a> tag linking to a specified url
	 * 
	 * @param string $url link target
	 * @param string $link_text the text displayed in the tag
	 * @param array $params[optional] html parameters
	 * 
	 * @return string
	 */
	function link_tag($url, $link_text = null, $params = array())
	{
		$params['href'] = $url;
		if ($link_text === null) $link_text = $url;
		return "<a " . parseHTMLoptions($params) . ">{$link_text}</a>";
	}

	/**
	 * Returns a csrf_token hidden input tag to use in forms
	 *
	 * @return string
	 */
	function csrf_tag()
	{
		return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
	}

	/**
	 * Returns the csrf token for this session
	 *
	 * @return string
	 */
	function csrf_token()
	{
		return \caspar\core\Caspar::generateCSRFtoken();
	}

	/**
	 * Return a javascript link tag
	 *
	 * @see link_tag()
	 * 
	 * @param string $link_text the text displayed in the tag
	 * @param array $params[optional] html parameters
	 *
	 * @return string
	 */
	function javascript_link_tag($link_text, $params = array())
	{
		return link_tag('javascript:void(0);', $link_text, $params);
	}
	
	/**
	 * Includes a template with specified parameters
	 *
	 * @param string	$template	name of template to load, or module/template to load
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function include_template($template, $params = array())
	{
		return \caspar\core\Components::includeTemplate($template, $params);
	}

	/**
	 * Return a rendered template with specified parameters
	 *
	 * @param string	$template	name of template to load, or module/template to load
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function get_template_html($template, $params = array())
	{
		return \caspar\core\Actions::returnTemplateHTML($template, $params);
	}

	/**
	 * Includes a component with specified parameters
	 *
	 * @param string	$component	name of component to load, or module/component to load
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function include_component($component, $params = array())
	{
		return \caspar\core\Components::includeComponent($component, $params);
	}

	/**
	 * Return a rendered component with specified parameters
	 *
	 * @param string	$component	name of component to load, or module/component to load
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function get_component_html($component, $params = array())
	{
		return \caspar\core\Actions::returnComponentHTML($component, $params);
	}

	/**
	 * Generate a url based on a route
	 * 
	 * @param string	$name 	The route key
	 * @param array 	$params	key => value pairs of route parameters
	 * 
	 * @return string
	 */
	function make_url($name, $params = array(), $relative = true)
	{
		return \caspar\core\Caspar::getRouting()->generate($name, $params, $relative);
	}
	
	/**
	 * Returns a string with html options based on an array
	 * 
	 * @param array	$options an array of options
	 * 
	 * @return string
	 */
	function parseHTMLoptions($options)
	{
		$option_strings = array();
		if (!is_array($options)) {
			throw new Exception('Invalid HTML options. Must be an array with key => value pairs corresponding to html attributes');
		}
		foreach ($options as $key => $val) {
			$option_strings[$key] = "{$key}=\"{$val}\"";
		}
		return implode(' ', array_values($option_strings));
	}

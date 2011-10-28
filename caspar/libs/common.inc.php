<?php

	/**
	 * Common helper functions
	 */

	/**
	 * Run the I18n translation function
	 *
	 * @param string $text the text to translate
	 * @param array $replacements[optional] replacements
	 *
	 * @return string
	 */
	function __($text, $replacements = array(), $html_decode = false)
	{
		$i18n = \caspar\core\Caspar::getI18n();
		return ($i18n instanceof \caspar\core\I18n) ? $i18n->__($text, $replacements, $html_decode) : $text;
	}

	/**
	 * Truncate a string, and optionally add padding dots
	 * 
	 * @param string $text
	 * @param integer $length
	 * @param boolean $add_dots[optional] defaults to true
	 * 
	 * @return string The truncated string
	 */
	function csp_truncateText($text, $length, $add_dots = true)
	{
		if (mb_strlen($text) > $length) {
			$string = wordwrap($text, $length - 3);
			$text = mb_substr($string, 0, mb_strpos($string, "\n"));
			if ($add_dots) $text .= '...';
		}
		return $text;
	}

	/**
	 * Returns a random number
	 * 
	 * @return integer
	 */
	function csp_randomNumber()
	{
		$randomNumber = "";

		for($cc = 1; $cc <= 6; $cc++) {
			$randomNumber .= mt_rand(0,9);
		}

		return $randomNumber;
	}


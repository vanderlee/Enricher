<?php
	/**
	 * Enricher
	 *
	 * @author Martijn W. van der Lee <martijn-at-vanderlee-dot-com>
	 * @copyright Copyright (c) 2015 Martijn W. van der Lee
	 * @license http://www.opensource.org/licenses/mit-license.php
	 */

	function Enricher_autoloader($class) {
		if (!class_exists($class) && is_file(dirname(__FILE__). '/' . $class . '.php')) {
			require dirname(__FILE__). '/' . $class . '.php';
		}
	}
	
	spl_autoload_register('Enricher_autoloader');

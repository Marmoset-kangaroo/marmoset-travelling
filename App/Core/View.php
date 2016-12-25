<?php
/**
 * @copyright Dukascopy
 * @author Stanislav Kudrytskyi <stanislav.kudrytskyi@dukascopy.com>
 * Date: 8/25/2016
 * Time: 17:30
 */

namespace App\Core;

use Twig_Loader_Filesystem;
use Twig_Environment;

class View
{
	protected static $instance;
	protected $twig;

	protected function __construct()
	{
		$loader = new Twig_Loader_Filesystem(__DIR__ . '/../../templates');
		$this->twig = new Twig_Environment($loader, []);
	}

	protected static function getInstance()
	{
		if (is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * @param string $templateName The template name
	 * @param array $context An array of parameters to pass to the template
	 * @return string
	 */
	public static function make($templateName, array $context = [])
	{
		return static::getInstance()->twig->render($templateName, $context);
	}
}

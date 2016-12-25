<?php
/**
 * @copyright Dukascopy
 * @author Stanislav Kudrytskyi <stanislav.kudrytskyi@dukascopy.com>
 * Date: 8/26/2016
 * Time: 17:18
 */

namespace App\Core;

use Symfony\Component\Routing\Route;
use Exception;

class AppRoute
{
	/**
	 * See Symfony\Component\Routing\Route::__construct() description for details
	 *
	 * @var array
	 */
	protected static $defaults = [
		'controller' => [],
		'defaults' => [],
		'requirements' => [],
		'options' => [],
		'host' => '',
		'schemes' => [],
		'method' => [],
		'condition' => '',
	];

	/**
	 * Validate received route
	 *
	 * @param $routeParameters
	 * @throws Exception
	 */
	protected static function validate($routeParameters)
	{
		if (empty($routeParameters['path'])) {
			throw new Exception('Path parameter in route is missed');
		}

		if (empty($routeParameters['controller'])) {
			throw new Exception('Controller for route is not specified');
		}

		if (!is_callable($routeParameters['controller'])) {
			throw new Exception('Specified controller is invalid ' . print_r($routeParameters, true));
		}
	}

	/**
	 * Generate Route object, set required options with received route parameters
	 *
	 * @param $routeParameters
	 * @return Route
	 * @throws Exception
	 */
	public static function get($routeParameters)
	{
		self::validate($routeParameters);

		$routeParameters = array_merge(self::$defaults, $routeParameters);

		$route = new Route($routeParameters['path']);

		$route->setDefaults(array_merge(['controller' => $routeParameters['controller']], $routeParameters['defaults']));
		$route->setRequirements($routeParameters['requirements']);
		$route->setOptions($routeParameters['options']);
		$route->setHost($routeParameters['host']);
		$route->setSchemes($routeParameters['schemes']);
		$route->setMethods($routeParameters['method']);
		$route->setCondition($routeParameters['condition']);

		return $route;
	}
}

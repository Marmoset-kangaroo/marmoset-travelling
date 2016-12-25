<?php
/**
 * @copyright Dukascopy
 * @author Stanislav Kudrytskyi <stanislav.kudrytskyi@dukascopy.com>
 * Date: 7/28/2016
 * Time: 12:10
 */

namespace App\Core;

use Exception;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Class Http
 * Provides functionality of routing of application: add routes, call corresponding handlers, generating url
 * Based on Symfony Routing component
 * @package App\Core
 */
class Http implements HttpKernelInterface
{
	/**
	 * @var RouteCollection
	 */
	public $routes;

	/**
	 * @var RequestContext
	 */
	protected $context;

	/**
	 * @var  \App\Core\Http instance
	 */
	protected static $instance;
	protected static $request;

	/**
	 * @return Http
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function __construct()
	{
		$this->routes = new RouteCollection();
		$this->context = new RequestContext();
	}

	/**
	 * Fill Request context object
	 */
	protected function setRequestContext()
	{
		if (empty(Config::getConnectionParameters()['context'])) {
			return;
		}

		$context = Config::getConnectionParameters()['context'];

		if (!empty($context['host'])) {
			$this->context->setHost($context['host']);
		}

		if (!empty($context['baseUrl'])) {
			$this->context->setBaseUrl($context['baseUrl']);
		}
	}

	protected function executeAction ($controller, $attributes)
	{


		if (!is_callable($controller)) {
			throw new Exception('Can not handle route');
		}

		if (!is_array($controller)) {
			return call_user_func_array($controller, $attributes);
		}

		$method = $controller[1];
		$controller = $controller[0];
		$controller = new $controller();

		return call_user_func_array([$controller, $method], $attributes);
	}

	/**
	 * @param Request $request
	 * @param int $type
	 * @param bool $catch
	 * @return mixed|Response
	 */
	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
	{
		// create a context using the current request
		$this->context->fromRequest($request);
		$matcher = new UrlMatcher($this->routes, $this->context);

		try {
			$attributes = $matcher->match($request->getPathInfo());
			$controller = $attributes['controller'];
			unset($attributes['controller']);

			$response = $this->executeAction($controller, $attributes);
		} catch (ResourceNotFoundException $e) {
			$response = new Response('Resource not found!', Response::HTTP_NOT_FOUND);
		} catch (Exception $e) {
			$response = new Response('Not found!' . $e->getMessage(), Response::HTTP_NOT_FOUND);
		}

		return $response;
	}

	public function map($routeSettings, $name = '')
	{
		$name = $name ?: $routeSettings['path'];
		$this->routes->add($name, AppRoute::get($routeSettings));
	}

	public function mapGroup($groupPrefix, array $routes)
	{
		$collection = new RouteCollection();

		foreach ($routes as $name => $routeSettings) {
			$collection->add($name, AppRoute::get($routeSettings));
		}

		$collection->addPrefix($groupPrefix);
		$this->routes->addCollection($collection);
	}

	public function addRoutePrefix($prefix)
	{
		$this->routes->addPrefix($prefix);
	}

	/**
	 * Get url by route name.
	 *
	 * @param string $routeName
	 * @param array $params
	 * @return string
	 */
	public function getUrl($routeName, array $params = [])
	{
		$this->setRequestContext();
		$generator = new UrlGenerator($this->routes, $this->context);

		return $generator->generate($routeName, $params, UrlGenerator::ABSOLUTE_URL);
	}

	/**
	 * @return Request
	 */
	public function getRequest()
	{
		if (is_null(self::$request)) {
			self::$request = Request::createFromGlobals();
		}

		return self::$request;
	}

	public function getBaseUrl($absolute = true)
	{
		$result = '';
		$this->setRequestContext();

		if ($absolute) {
			$result .= $this->context->getScheme() . '://' . $this->context->getHost();
		}

		$result .= $this->context->getBaseUrl();

		return $result;
	}

	public function getContext()
	{
		return $this->context;
	}
}

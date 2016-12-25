<?php
/**
 * @copyright Dukascopy
 * @author Stanislav Kudrytskyi <stanislav.kudrytskyi@dukascopy.com>
 * Date: 9/22/2016
 * Time: 14:48
 */

namespace App\Core;

use \Monolog\Logger;
use \Monolog\Handler\ErrorLogHandler;

class AppLogger
{
	protected static $instance;

	protected function __construct()
	{
	}

	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new Logger('CARD_PAYMENT');
			self::$instance->pushHandler(new ErrorLogHandler());
		}

		return self::$instance;
	}
}

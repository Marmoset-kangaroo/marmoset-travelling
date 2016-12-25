<?php
/**
 * @copyright Dukascopy
 * @author Stanislav Kudrytskyi <stanislav.kudrytskyi@dukascopy.com>
 * Date: 10/7/2016
 * Time: 10:05
 */

namespace App\Core;

use Swift_Message;
use Swift_MailTransport;
use Swift_Mailer;

class Mail
{
	protected static $instance;
	protected $mailer;

	protected function __construct()
	{
		$transport = Swift_MailTransport::newInstance();
		$this->mailer = Swift_Mailer::newInstance($transport);
	}

	protected static function createMessage()
	{
		return Swift_Message::newInstance();
	}

	protected static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected static function parseView($view)
	{
		$result = [
			'html' => null,
			'plain' => null,
			'raw' => null,
		];
		if (is_string($view)) {
			$result['html'] = $view;
		}

		if (is_array($view)) {
			$result['html'] = !empty($view['html']) ? $view['html'] : null;
			$result['plain'] = !empty($view['plain']) ? $view['plain'] : null;
			$result['raw'] = !empty($view['raw']) ? $view['raw'] : null;
		}

		if (array_filter($result)) {
			return $result;
		}

		throw new \Exception('Mail Invalid argument $view');
	}

	protected static function setContent(Swift_Message $message, array $view, array $placeholders = [])
	{
		if (!empty($view['html'])) {
			$message->setBody(View::make($view['html'], $placeholders), 'text/html');
		}

		if (!empty($view['plain'])) {
			$method = !empty($view['html']) ? 'addPart' : 'setBody';
			$message->$method(View::make($view['plain'], $placeholders), 'text/plain');
		}

		if (!empty($view['raw'])) {
			$method = !empty($view['html']) || !empty($view['plain']) ? 'addPart' : 'setBody';
			$message->$method($view['raw'], 'text/plain');
		}
	}

	public function mail(Swift_Message $message)
	{
		return $this->mailer->send($message);
	}

	public static function send($view, $placeholders, callable $builder)
	{
		$view = self::parseView($view);

		$message = self::createMessage();
		call_user_func_array($builder, array($message));

		self::setContent($message, $view, $placeholders);

		return self::getInstance()->mail($message);
	}
}

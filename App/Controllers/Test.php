<?php
/**
 * @author Stanislav
 * Date: 25.12.2016
 * Time: 16:06
 */

namespace App\Controllers;

use App\Core\View;
use Symfony\Component\HttpFoundation\Response;

class Test
{
	public function map()
	{
		return new Response(View::make('test/map.twig', [
			'api_key' => 'AIzaSyC1Cp40wh_7k0cTmUNPwL2ox_4QpMgJcKI',
		]));
	}

	public function home()
	{
		return new Response('Marmoset home');
	}
}

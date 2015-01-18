<?php

namespace Controllers;

use DateTime;
use DateInterval;
use SlimFacades\App;
use SlimFacades\Input;
use SlimFacades\View;
use SlimFacades\Response;
use Model;
use Respect\Validation\Validator as V;

class Main
{
	use Controller;

	static public function index($year, $month)
	{
		$date = new DateTime("$year-$month");

		$currentLinkData = compact('year', 'month');

		$date->sub(new DateInterval('P1M'));
		$previousLinkData = array('year' => $date->format('Y'), 'month' => $date->format('m'));

		$date->add(new DateInterval('P2M'));
		$nextLinkData = array('year' => $date->format('Y'), 'month' => $date->format('m'));

		$entrances = Model::factory('Models\\Entry')
			->where('year', (int) $year)
			->where('month', (int) $month)
			->whereGte('estimated', 0)
			->findMany();

		$variables = compact(
			'currentLinkData', 'previousLinkData', 'nextLinkData',
			'entrances', 'outs'
		);
		View::display(self::template('index'), $variables);
	}

	static public function create($year, $month)
	{
		$entranceValidator = V::attribute('day', V::notEmpty()->int()->between(1, 31, true))
							  ->attribute('description', V::notEmpty()->string())
							  ->attribute('estimated-amount', V::notEmpty()->numeric()->positive())
							  ->attribute('status', V::notEmpty()->int()->in(array(1, 2)));

		$date = new DateTime("$year-$month");
		$entrance = Input::post('entrance', false);
		if (array_key_exists('add', $entrance) && is_array($entrance)) {
			$add = (object) $entrance['add'];
			$add->type = 'add';

			if (2 == $add->status) {
				$entranceValidator->attribute('real-amount', V::notEmpty()->numeric()->positive());
			} else {
				$entranceValidator->attribute('real-amount', V::numeric()->positive());
			}

			try {
				$entranceValidator->assert((object) $add);
				// @TODO store in database
				App::flash('success', 'Entrada adicionada!');
				Response::setStatus(200);
			} catch (\Exception $e) {
				App::flash('error', 'Ocorreram erros ao cadastrar adição');
				Response::setStatus(402);
			}
			return;
		}
	}
}
<?php

namespace Controllers;

use DateTime;
use DateInterval;
use SlimFacades\App;
use SlimFacades\Input;
use SlimFacades\View;
use SlimFacades\Response;
use Model;

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
		$entrance = Input::post('entrance', false);

		if (array_key_exists('add', $entrance) && is_array($entrance)) {
			$add = (object) $entrance['add'];
			$add->type = 'add';

			try {
				$entry = Model::factory('Models\\Entry')->create();

				$entry->year = $year;
				$entry->month = $month;
				$entry->day = $add->day;
				$entry->description = $add->description;
				$entry->estimated = $add->{'estimated-amount'};
				$entry->real = $add->{'real-amount'};
				$entry->status = $add->status;

				$entry->validate();

				$entry->save();

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
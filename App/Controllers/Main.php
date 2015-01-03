<?php

namespace Controllers;

use DateTime;
use DateInterval;
use SlimFacades\Input;
use SlimFacades\View;
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
}
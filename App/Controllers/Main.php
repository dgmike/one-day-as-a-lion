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
			->orderByAsc('day')
			->findMany();

		$outs = Model::factory('Models\\Entry')
			->where('year', (int) $year)
			->where('month', (int) $month)
			->whereLte('estimated', 0)
			->orderByAsc('day')
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

		if (array_key_exists('add', $entrance) && is_array($entrance['add'])) {
			$add = (object) $entrance['add'];
			$add->type = 'add';
			$add->year = $year;
			$add->month = $month;
			self::save($add);
			return;
		} elseif (array_key_exists('remove', $entrance) && is_array($entrance['remove'])) {
			$remove = (object) $entrance['remove'];
			$remove->type = 'remove';
			$remove->year = $year;
			$remove->month = $month;
			self::save($remove);
			return;
		}
		Response::setStatus(402);
	}

	static protected function save($data)
	{
		try {
			Model::factory('Models\\Entry')
				->create()
				->assign($data)
				->validate()
				->save();

			App::flash('success', 'Entrada adicionada!');
			Response::setStatus(200);
		} catch (\Exception $e) {
			App::flash('error', 'Ocorreram erros ao cadastrar entrada');
			Response::setStatus(402);
		}
	}
}
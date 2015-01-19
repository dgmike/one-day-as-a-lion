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

		$entry = Model::factory('Models\\Entry')->create();

		if (array_key_exists('add', $entrance) && is_array($entrance['add'])) {
			$add = (object) $entrance['add'];
			$add->type = 'add';
			$add->year = $year;
			$add->month = $month;
			self::save($entry, $add);
			return;
		} elseif (array_key_exists('remove', $entrance) && is_array($entrance['remove'])) {
			$remove = (object) $entrance['remove'];
			$remove->type = 'remove';
			$remove->year = $year;
			$remove->month = $month;
			self::save($entry, $remove);
			return;
		}
		Response::setStatus(402);
	}

	static public function update($year, $month)
	{
		$commit = Input::post('commit', false);
		if (is_array($commit)) {
			$entry = Model::factory('Models\\Entry')
				->whereEqual('id', (int) $commit['id'])
				->findOne();

			$commit['status'] = 2;

			self::save($entry, (object) $commit);
			return;
		}
		Response::setStatus(402);
	}

	static protected function save($entry, \stdClass $data)
	{
		try {
			$entry
				->assign($data)
				->validate()
				->save();

			App::flash('success', 'Dados salvos com sucesso!');
			Response::setStatus(200);
		} catch (\Exception $e) {
			App::flash('error', 'Ocorreram erros ao salvar entrada');
			Response::setStatus(402);
		}
	}
}
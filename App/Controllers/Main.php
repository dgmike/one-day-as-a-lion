<?php

namespace Controllers;

use DateTime;
use DateInterval;
use SlimFacades\App;
use SlimFacades\Input;
use SlimFacades\View;
use SlimFacades\Response;
use Model;
use Respect\Validation\Validator as v;

class Main
{
	use Controller;

	static public function index($year, $month)
	{
		$date = new DateTime("$year-$month");

		$currentLinkData = compact('year', 'month');

		$date->sub(new DateInterval('P1M'));
		$previousLinkData = array(
			'year' => $date->format('Y'),
			'month' => $date->format('m')
		);

		$date->add(new DateInterval('P2M'));
		$nextLinkData = array(
			'year' => $date->format('Y'),
			'month' => $date->format('m')
		);

		$modelEntrance = Model::factory('Models\\Entry')
			->filter('on', $year, $month)
			->filter('entrances');
		$entrances = $modelEntrance->findMany();

		$modelOut = Model::factory('Models\\Entry')
			->filter('on', $year, $month)
			->filter('outs');
		$outs = $modelOut->findMany();

		$sums = array(
			'entrance' => array(
				'estimated' => array(
					'first_half' => $modelEntrance->filter('firstHalf')->sum('estimated'),
					'second_half' => $modelEntrance->filter('secondHalf')->sum('estimated'),
					'sum' => $modelEntrance->sum('estimated'),
				),
				'real' => array(
					'first_half' => $modelEntrance->filter('chain', 'commited', 'firstHalf')->sum('real'),
					'second_half' => $modelEntrance->filter('chain', 'commited', 'secondHalf')->sum('real'),
					'sum' => $modelEntrance->filter('commited')->sum('real'),
				),
			),
			'out' => array(
				'estimated' => array(
					'first_half' => $modelOut->filter('firstHalf')->sum('estimated'),
					'second_half' => $modelOut->filter('secondHalf')->sum('estimated'),
					'sum' => $modelOut->sum('estimated'),
				),
				'real' => array(
					'first_half' => $modelOut->filter('chain', 'commited', 'firstHalf')->sum('real'),
					'second_half' => $modelOut->filter('chain', 'commited', 'secondHalf')->sum('real'),
					'sum' => $modelOut->filter('commited')->sum('real'),
				),
			),
		);

		// print '<pre>';
		// print_r(\ORM::get_query_log());
		// print_r($sums);
		// print '</pre>';
		// die;

		$variables = compact(
			'currentLinkData', 'previousLinkData', 'nextLinkData',
			'entrances', 'outs', 'sums'
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
			$add->estimated = abs($add->estimated);
			$add->real = $add->real ? abs($add->real) : null;
			$add->year = $year;
			$add->month = $month;
			self::save($entry, $add);
			return;
		} elseif (array_key_exists('remove', $entrance) && is_array($entrance['remove'])) {
			$remove = (object) $entrance['remove'];
			$remove->type = 'remove';
			$add->estimated = (abs($add->estimated) * -1);
			$add->real = $add->real ? (abs($add->real) * -1) : null;
			$remove->year = $year;
			$remove->month = $month;
			self::save($entry, $remove);
			return;
		}
		Response::setStatus(403);
	}

	static public function commit($year, $month)
	{
		$commit = Input::post('commit', false);
		if (is_array($commit)) {
			return self::updateData($commit);
		}
		Response::setStatus(403);
	}

	static public function update($year, $month)
	{
		$entrance = Input::post('entrance', false);
		if (is_array($entrance) && is_array($entrance['edit'])) {
			return self::updateData($entrance['edit']);
		}
		Response::setStatus(403);
	}

	static protected function updateData($data)
	{
		$entry = Model::factory('Models\\Entry')
			->whereEqual('id', (int) $data['id'])
			->findOne();

		$entry->type = $data['type'] == 'add' ? 'entrance' : 'remove';

		self::save($entry, (object) $data);
	}

	static public function remove($year, $month)
	{
		$id = Input::post('id', false);
		if ($id) {
			$entry = Model::factory('Models\\Entry')
				->whereEqual('id', (int) $id)
				->findOne();
			$entry->delete();
			App::flash('success', 'Entrada removida com sucesso!');
			return;
		}
		Response::setStatus(403);
	}

	static public function import()
	{
		$to = Input::post('to') . '-01';
		$validate = v::date('Y-m-d');

		$date = new \DateTime($to);
		$date->sub(new \DateInterval('P1D'));

		try {
			$validate->assert($to);

			list($year, $month) = explode('-', $to);
			$entries = Model::factory('Models\\Entry')
				->whereEqual('year', (int) $date->format('Y'))
				->whereEqual('month', (int) $date->format('m'))
				->findMany();

			foreach ($entries as $entry) {
				$newEntry = Model::factory('Models\\Entry')->create();
				foreach ($entry->as_array() as $key => $value) {
					if ('id' !== $key) {
						$newEntry->{$key} = $value;
					}
				}
				$newEntry->year = $year;
				$newEntry->month = $month;
				$newEntry->status = 1;
				$newEntry->type = ($entry->estimated < 0 ? 'remove' : 'add');
				$newEntry->save();
			}
			App::flash('success', 'Dados importados com sucesso!');
			return;
		} catch (\Exception $e) {
			print_r($e);
			App::flash('error', 'Ocorreram erros ao salvar entrada');
			Response::setStatus(403);
		}
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
			Response::setStatus(403);
		}
	}
}

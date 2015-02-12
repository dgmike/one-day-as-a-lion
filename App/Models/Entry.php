<?php

namespace Models;
use Model;
use Respect\Validation\Validator as V;

class Entry
	extends Model
{
	public static $_table = 'entries';

	public $type = 'add';

	public static function chain($orm)
	{
		$orm = clone $orm;
		$chains = array_slice(func_get_args(), 1);
		foreach ($chains as $chain) {
			$orm = $orm->filter($chain);
		}
		return $orm;
	}

	public static function on($orm, $year, $month)
	{
		return $orm
			->where('year', (int) $year)
			->where('month', (int) $month)
			->orderByAsc('day');
	}

	public static function commited($orm)
	{
		$orm = clone $orm;
		return $orm->whereEqual('status', 2);
	}

	public static function entrances($orm)
	{
		return $orm->whereGte('estimated', 0);
	}

	public static function outs($orm)
	{
		return $orm->whereLt('estimated', 0);
	}

	public static function firstHalf($orm)
	{
		$orm = clone $orm;
		return $orm->whereLte('day', 15);
	}

	public static function secondHalf($orm)
	{
		$orm = clone $orm;
		return $orm->whereGt('day', 15);
	}

	public function assign($data)
	{
		$attributes = array(
			'type', 'year', 'month', 'day', 'description',
			'estimated', 'real', 'status'
		);

		foreach ($attributes as $attribute) {
			if (isset($data->{$attribute})) {
				$this->{$attribute} = $data->{$attribute};
			}
		}

		return $this;
	}

	public function validate()
	{
		$entranceValidator = V::attribute('day', V::notEmpty()->int()->between(1, 31, true))
							  ->attribute('description', V::notEmpty()->string())
							  ->attribute('estimated', V::notEmpty()->numeric())
							  ->attribute('status', V::notEmpty()->int()->in(array(1, 2)));

		if (2 == $this->status) {
			$entranceValidator->attribute('real', V::notEmpty()->numeric());
		} else {
			$entranceValidator->attribute('real', V::numeric());
		}

		$entranceValidator->assert((object) $this->asArray());

		return $this;
	}

	public function save()
	{
		if ('remove' === $this->type) {
			$this->estimated = abs($this->estimated) * -1;
			if ($this->real) {
				$this->real = abs($this->real) * -1;
			}
		}
		return parent::save();
	}
}

<?php

namespace Models;
use Model;
use Respect\Validation\Validator as V;

class Entry
	extends Model
{
	public static $_table = 'entries';

	public $type = 'add';

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
							  ->attribute('estimated', V::notEmpty()->numeric()->positive())
							  ->attribute('status', V::notEmpty()->int()->in(array(1, 2)));

		if (2 == $this->status) {
			$entranceValidator->attribute('real', V::notEmpty()->numeric()->positive());
		} else {
			$entranceValidator->attribute('real', V::numeric()->positive());
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
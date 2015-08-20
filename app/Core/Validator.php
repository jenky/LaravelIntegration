<?php

namespace App\Core;

use Hazzard\Validation\Validator as ValidatorFactory;

class Validator
{
	protected $validator;

	public function __construct()
	{
		$this->validator = new ValidatorFactory;

		$this->validator->setDefaultLines();

		$this->validator->setAsGlobal();

		$this->validator->classAlias();
	}

	public function getValidator()
	{
		return $this->validator;
	}

	public function setConnection($db)
	{
		$this->validator->setConnection($db);

		return $this;
	}
}
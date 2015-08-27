<?php

namespace App\Core;

use Illuminate\Container\Container;
use Hazzard\Validation\Validator as ValidatorFactory;

class Validator
{
	protected $validator;

	public function __construct(Container $container = null)
	{
		$this->validator = new ValidatorFactory($container);

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
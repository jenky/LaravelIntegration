<?php

namespace App\Core;

class Allias
{
	public function __construct(array $aliases = [])
	{
		$this->loadDefaultAliases($aliases);
	}

	public static function makeAliases(array $aliases)
	{
		foreach ($aliases as $alias => $class) 
		{
			static::makeAlias($alias, $class);
		}
	}

	public static function makeAlias($alias, $class)
	{
		$class = str_replace('App\\Core\\', '', $class);
		class_alias($class, $alias);
	}

	public function loadDefaultAliases(array $aliases)
	{
		$aliases += [
			'Carbon' => Carbon\Carbon::class
		];

		static::makeAliases($aliases);
	}
}
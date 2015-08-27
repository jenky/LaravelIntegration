<?php

namespace App\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
	protected $capsule;

	public function __construct(Container $container = null)
	{
		$this->connect($container);
	}

	public function getCapsule()
	{
		return $this->capsule;
	}

	protected function connect($container = null)
	{
		$connection = config('database.default');
		$this->capsule = new Capsule($container); 
		/*$capsule->addConnection(array(
			'driver'    => config('database.driver', 'mysql'),
			'host' 		=> config('database.host', DB_HOSTNAME),
			'database' 	=> config('database.database', DB_DATABASE),
			'username' 	=> config('database.username', DB_USERNAME),
			'password' 	=> config('database.password', DB_PASSWORD),
			'charset' 	=> config('database.charset', 'utf8'),
			'collation' => config('database.collation', 'utf8_unicode_ci'),
			'prefix' 	=> config('database.prefix', DB_PREFIX),
		));*/

		// $capsule->addConnection(config('database.connections.' . $connection));

		$this->capsule->addConnection(array(
			'driver'    => 'mysql',
			'host' 		=> DB_HOSTNAME,
			'database' 	=> DB_DATABASE,
			'username' 	=> DB_USERNAME,
			'password' 	=> DB_PASSWORD,
			'charset' 	=> 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix' 	=> DB_PREFIX,
		));
		
		$container = $container ? $container :  new Container;
		$this->capsule->setEventDispatcher(new Dispatcher($container));
		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();

		$this->capsule->getConnection()->enableQueryLog();

		/*config([
			'database' => [
				'default' => 'mysql',
				'connections' => [
					'mysql' => [
						'driver'    => config('database.driver', 'mysql'),
						'host' 		=> config('database.host', DB_HOSTNAME),
						'database' 	=> config('database.database', DB_DATABASE),
						'username' 	=> config('database.username', DB_USERNAME),
						'password' 	=> config('database.password', DB_PASSWORD),
						'charset' 	=> config('database.charset', 'utf8'),
						'collation' => config('database.collation', 'utf8_unicode_ci'),
						'prefix' 	=> config('database.prefix', DB_PREFIX),
			            'strict'    => false,
			        ],
			    ],
			]
		]);*/
		
		return $this->capsule;
	}
}
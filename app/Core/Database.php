<?php

namespace App\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
	public function __construct()
	{
		$this->connect();
	}

	protected function connect()
	{
		$capsule = new Capsule(); 
		$capsule->addConnection(array(
			'driver'    => config('database.driver', 'mysql'),
			'host' 		=> config('database.host', DB_HOSTNAME),
			'database' 	=> config('database.database', DB_DATABASE),
			'username' 	=> config('database.username', DB_USERNAME),
			'password' 	=> config('database.password', DB_PASSWORD),
			'charset' 	=> config('database.charset', 'utf8'),
			'collation' => config('database.collation', 'utf8_unicode_ci'),
			'prefix' 	=> config('database.prefix', DB_PREFIX),
		));
		 
		$capsule->setEventDispatcher(new Dispatcher(new Container));
		$capsule->setAsGlobal();
		$capsule->bootEloquent();

		$capsule->getConnection()->enableQueryLog();
		
		return $capsule;
	}
}
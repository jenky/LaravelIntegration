<?php

namespace App;

class Auth
{
	/**
	 * Logged in user Id
	 * 
	 * @var int
	 */ 
	public $id;

	/**
	 * Eloquent model
	 * 
	 * @var App\Model
	 */ 
	protected $model;

	/**
	 * Opencart session class
	 * 
	 * @var Session
	 */ 
	protected $session;

	/**
	 * Errors 
	 * 
	 * @var array
	 */ 
	protected $errors = [];

	/**
	 * Id field name
	 * 
	 * @var string
	 */ 
	const ID = 'id';

	/**
	 * Password field name
	 * 
	 * @var string
	 */ 
	const PASSWORD = 'password';

	public function __construct($registry, $model)
	{
		$this->session = $registry->get('session');
		$this->model = $model;

		$this->boot();
	}

	/**
	 * Boot
	 * 
	 * @return void
	 */ 
	protected function boot()
	{
		$session = $this->getSession();

		if (isset($session->{static::ID}))
		{
			$this->id = $session->{static::ID};
		}
	}

	/**
	 * Get the logged in user in session
	 * 
	 * @return array|null
	 */ 
	protected function getSession()
	{
		return isset($this->session->data['_auth_' . class_basename($this->model)])
			? $this->session->data['_auth_' . class_basename($this->model)]
			: null;
	}

	/**
	 * Login user with provided data
	 * 
	 * @param array $data
	 * @return object
	 */ 
	public function login(array $data)
	{
		$password = isset($data[static::PASSWORD]) ? $data[static::PASSWORD] : '';

		$fields = array_except($data, [static::PASSWORD]);

		$query = $this->model->query();

		foreach ($fields as $key => $value) 
		{
			$query->where($key, '=', $value);
		}

		$user = $query->first();

		if ($user)
		{
			if (app('hasher')->check($password, $user->{static::PASSWORD}))
			{
				$this->session->data['_auth_' . class_basename($this->model)] = $user;
				$this->session->data['token'] = md5(mt_rand());
				$this->id = $user->{static::ID};
			}
			else
			{
				$this->errors[] = 'Password does not match';
			}
		}
		else
		{
			$this->errors[] = 'We cannot log you in';		
		}

		return $this;
	}

	/**
	 * Logout user
	 * 
	 * @return void
	 */ 
	public function logout()
	{
		if (isset($this->session->data['_auth_' . class_basename($this->model)]))
		{
			unset($this->session->data['_auth_' . class_basename($this->model)]);
		}

		if (isset($this->session->data['token']))
		{
			unset($this->session->data['token']);
		}
	}

	/**
	 * Check if user is logged in
	 * 
	 * @return bool
	 */ 
	public function check()
	{
		return (bool) $this->id;
	}

	/**
	 * Get the logged in user data
	 * 
	 * @param bool $fetch 
	 * @return object
	 */ 
	public function user($fetch = false)
	{
		$session = $this->getSession();

		if ($fetch) return isset($session->{static::ID}) ? $this->model->find($session->{static::ID}) : null;

		return $session;
	}

	/**
	 * Get the error messages
	 * 
	 * @return array
	 */ 
	public function errors()
	{
		return $this->errors;
	}	
}
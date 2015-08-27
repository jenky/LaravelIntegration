<?php

namespace App;

class Input
{
	protected $request;

	public function __construct($registry)
	{
		$this->request = $registry->get('request');
	}

	/**
	 * Get all of the input and files for the request.
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->input();
	}

	/**
	 * Retrieve an input item from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string|array
	 */
	public function input($key = null, $default = null)
	{
		$post = !empty($this->request->post) ? $this->request->post : [];
		$get = !empty($this->request->get) ? $this->request->get : [];

		$input = $post + $get;

		return array_get($input, $key, $default);
	}

	/**
	 * Get a subset of the items from the input data.
	 *
	 * @param  array  $keys
	 * @return array
	 */
	public function only($keys)
	{
		$keys = is_array($keys) ? $keys : func_get_args();

		$results = [];

		$input = $this->all();

		foreach ($keys as $key)
		{
			array_set($results, $key, array_get($input, $key));
		}

		return $results;
	}

	/**
	 * Get all of the input except for a specified array of items.
	 *
	 * @param  array  $keys
	 * @return array
	 */
	public function except($keys)
	{
		$keys = is_array($keys) ? $keys : func_get_args();

		$results = $this->all();

		array_forget($results, $keys);

		return $results;
	}

	/**
	 * Determine if the request contains a non-empty value for an input item.
	 *
	 * @param  string|array  $key
	 * @return bool
	 */
	public function has($key)
	{
		$keys = is_array($key) ? $key : func_get_args();

		foreach ($keys as $value)
		{
			if ($this->isEmptyString($value)) return false;
		}

		return true;
	}

	/**
	 * Determine if the given input key is an empty string for "has".
	 *
	 * @param  string  $key
	 * @return bool
	 */
	protected function isEmptyString($key)
	{
		$boolOrArray = is_bool($this->input($key)) || is_array($this->input($key));

		return ! $boolOrArray && trim((string) $this->input($key)) === '';
	}
}
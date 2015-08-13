<?php

use Illuminate\Container\Container;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string  $make
     * @param  array   $parameters
     * @return mixed|\Laravel\Lumen\Application
     */
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($make, $parameters);
    }
}

if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string  $path
     * @return string
     */
    function base_path($path = '')
    {
        return app()->basePath().($path ? '/'.$path : $path);
    }
}

if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        if (is_array($key)) {
            return app('config')->set($key);
        }

        return app('config')->get($key, $default);
    }
}

if (! function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        $factory = Container::getInstance()->make('view')->view();

        if (func_num_args() === 0) {
            return $factory;
        }
        return $factory->make($view, $data, $mergeData);
    }
}

if (!function_exists('datetime')) 
{
    /**
     * Parse datetime with Carbon
     * 
     * @param mixed|$time Carbon supported time
     * @param string|$timezone Output timezone, try to catch from users table if not set
     * 
     * @return Carbon
     */ 

    function datetime($time = null, $timezone = null, $reverse = false)
    {
        $defaultTz = config('app.timezone', 'UTC');

        if (!in_array($timezone, timezone_identifiers_list()))
        {
            $timezone = false;
        }       

        if ($timezone)
        {
            if ($time instanceof Carbon\Carbon)
            {
                return $time->tz($timezone);
            }

            return $reverse 
                ? Carbon::parse($time, $timezone)->tz($defaultTz)
                : Carbon::parse($time, $defaultTz)->tz($timezone);
        }

        return Carbon::parse($time, $defaultTz); 
    }
}
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

if (! function_exists('auth')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     */
    function auth()
    {
        return Container::getInstance()->make('auth');
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
        $defaultTz = config('app.timezone', 'Asia/Singapore');
        // $defaultTz = config('app.timezone', 'Asia/Ho_Chi_Minh');

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

if (!function_exists('prd'))
{
    /**
     * Print the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function prd()
    {
        array_map(function($x) { 
            echo '<pre>';
            print_r($x); 
            echo '</pre>';
        }, func_get_args());
        die;
    }
}

if (!function_exists('vd')) 
{
    /**
     * Dump the passed variables using var_dump and end the script.
     *
     * @param  mixed
     * @return void
     */
    function vd()
    {
        array_map(function($x) { var_dump($x);die; }, func_get_args());
    }
}

if (!function_exists('get_update_rules')) 
{
    /**
     * Get the validation update rules
     *
     * @param  array
     * @return void
     */
    function get_update_rules(array $rules)
    {
        foreach ($rules as &$rule) 
        {
            if (is_array($rule) && !in_array('sometimes', $rule))
            {                   
                array_unshift($rule, 'sometimes');
            }
            else if (is_string($rule) && !str_contains('sometimes', $rule))
            {
                $rule = 'sometimes|' . $rule;
            }
        }

        return $rules;
    }
}

if (!function_exists('remove_query_string')) 
{
    /**
     * Remove a query string parameter from an URL.
     *
     * @param string $url
     * @param string $varname
     *
     * @return string
     */
    function remove_query_string($url, $varname)
    {
        $parsedUrl = parse_url(html_entity_decode($url));        
        $query = array();

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $query);
            unset($query[$varname]);
        }

        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = !empty($query) ? '?'. http_build_query($query) : '';

        return $parsedUrl['scheme']. '://'. $parsedUrl['host']. $path. $query;
    }
}

if (!function_exists('bcrypt')) 
{
    /**
     * Get the validation update rules
     *
     * @param  array
     * @return void
     */
    function bcrypt($value, array $options = [])
    {
        return Container::getInstance()->make('hasher')->make($value, $options);
    }
}
<?php

namespace Libraries;

/**
 * Router handle class.
 */
class Router
{
    /**
     * Registered routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Base path of the project
     *
     * For a project whose main file lives in subdirectory of host.
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * Register a route.
     *
     * @param  string     $method  HTTP request method
     * @param  string     $url     Route URL
     * @param  \callable  $action  Route action (method/function)
     * @return void
     */
    public function map($method, $url, $action)
    {
        $this->routes[] = [$method, $url, $action];
    }

    /**
     * Get registered routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Set the base path of the project.
     *
     * @param  string  $basePath  Base path of the project
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Check the routes list according to request method and URL, and execute the action while match
     *
     * @param  string  $method  HTTP request method
     * @param  string  $url     Route URL
     * @return boolean
     */
    public function match($method, $url)
    {
        $basePath = str_replace('/', '\/', $this->basePath);
        $pureUrl = trim($this->basePath, '/') == trim($url, '/') ? '/' : preg_replace("/^\/{$basePath}/", '/', $url);

        foreach ($this->routes as $route)
        {
            if ($route[0] == strtoupper($method))
            {
                if (trim($route[1], '/') == trim($pureUrl, '/'))
                {
                    call_user_func_array($route[2], []);
                    return true;
                }
                else if ($regex = $this->_regex(trim($route[1], '/')))
                {
                    $match = preg_match_all($regex, trim($pureUrl, '/'), $matches);
                    if ($match <= 0) continue;

                    array_shift($matches);

                    $args = array_map(function($item)
                    {
                        return rawurldecode($item[0]);
                    },
                    $matches);

                    call_user_func_array($route[2], $args);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Parse a route containing parameters and return its Regex.
     *
     * @param  string  $rule  Route containing parameters
     * @return string|false
     */
    private function _regex($rule)
    {
        $match = preg_match_all('/\{([^\{\}\/]+)\}/', $rule, $matches);

        if ($match > 0)
        {
            $regex = preg_replace('/\{[^\{\}\/]+\}/', '([^\/]+)', $rule);
            $regex = '/' . str_replace('/', '\/', $regex) . '$/';
            $regex = str_replace('([^\\\/]+)', '([^\\/]+)', $regex);    // Remove the redundant backslash added by the code in the above line in '([^\/]+)'
            return $regex;
        }

        return false;
    }
}

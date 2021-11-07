<?php

namespace Libraries;

use Closure;

/**
 * Router handling class.
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
     * @param  string             $method  HTTP request method
     * @param  string             $url     Route URL
     * @param  \callable|Closure  $action  Route action (method/function)
     * @return void
     */
    public function map(string $method, string $url, mixed $action): void
    {
        $this->routes[] = [$method, $url, $action];
    }

    /**
     * Get registered routes.
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Set the base path of the project.
     *
     * @param  string  $basePath  Base path of the project
     * @return void
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Check the routes list according to request method and URL, and execute the action while match
     *
     * @param  string|null  $method  HTTP request method; get request method from server information automatically if it is set to `null`
     * @param  string|null  $url     Route URL
     * @return boolean
     */
    public function match(mixed $method = null, mixed $url = null): bool
    {
        if (is_null($method))
        {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        if (is_null($url))
        {
            $url = rtrim(preg_replace(['/\?.*/', '/#.*/'], '', $_SERVER['REQUEST_URI']), '/');
        }

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
    private function _regex(string $rule): mixed
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

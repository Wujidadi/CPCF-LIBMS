<?php

/*
|--------------------------------------------------------------------------
| Facades Framework Tools
|--------------------------------------------------------------------------
|
| Tools which has closer dependency with the framework itself.
|
| + Injections
|   - function  inject
|
| + Views
|   - function  view
|   - function  viewPath
|
*/

if (!function_exists('inject'))
{
    /**
     * Generate the full path of the injection file.
     *
     * @param  string  $_path  Short path of the injection file
     * @return string
     */
    function inject(string $_path = ''): string
    {
        $path = '';
        if ($_path !== '')
        {
            $path = preg_replace('/\./', DIRECTORY_SEPARATOR, $_path) . '.php';
            $path = APP_DIR . DIRECTORY_SEPARATOR . 'Injects' . DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }
}

if (!function_exists('view'))
{
    /**
     * Render view page by short path of the view file and the given data.
     *
     * @param  string  $_path     Short path of the view file
     * @param  array   $_rawdata  Data to be rendered to the view page
     * @return void
     */
    function view(string $_path = '', array $_rawdata = []): void
    {
        if ($_path !== '')
        {
            $path = preg_replace('/\./', DIRECTORY_SEPARATOR, $_path) . '.view.php';
            extract($_rawdata);
            include VIEW_DIR . DIRECTORY_SEPARATOR . $path;
        }
        exit;
    }
}

if (!function_exists('viewPath'))
{
    /**
     * Return the path of a view file.
     *
     * @param  string  $_path  Short path of the view file
     * @return string
     */
    function viewPath(string $_path = ''): string
    {
        if ($_path !== '')
        {
            $path = preg_replace('/\./', DIRECTORY_SEPARATOR, $_path) . '.view.php';
            return VIEW_DIR . DIRECTORY_SEPARATOR . $path;
        }
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Autoload
|--------------------------------------------------------------------------
|
| Simplify and improve the usage of include/require and "use" by PHP's
| spl_autoload_register function.
|
*/

/**
 * Map array of autoload directories.
 *
 * @var string[]
 */
define('AUTOLOAD_MAP', require 'autoload_map.php');

# Rigister autoload function
spl_autoload_register(function($_path)
{
    $namespace = '';
    $className = '';

    # Remove the left-most backslash (if exists) in the path
    $path = ltrim($_path, '\\');

    # Search in autoload class map first, and require the file if it is found
    if (in_array($path, array_keys(AUTOLOAD_MAP)))
    {
        require_once BASE_DIR . DIRECTORY_SEPARATOR . AUTOLOAD_MAP[$path];
    }
    # Parse the real path of the namespace if found nothing in autoload class map
    else
    {
        # Get the namespace (part before the last backslash in the path) and the class name (the file's name)
        if ($lastBackslash = strrpos($path, '\\'))
        {
            $namespace = substr($path, 0, $lastBackslash);
            $className = substr($path, $lastBackslash + 1);
        }

        # Throw the real class name in the code while the class path paring is failed
        if ($namespace == '' && $className == '')
        {
            throw new Exception($_path, 1);
        }

        # Convert the path according to autoload.json
        $pathSegment = explode('\\', $namespace);
        $topDir = topDir();
        $pathSegment[0] = rtrim($topDir[$pathSegment[0] . '\\'], '/');
        $namespace = implode('\\', $pathSegment);

        # Remake the path
        $path = empty($namespace)
              ? $className . '.php'
              : $namespace . DIRECTORY_SEPARATOR . $className . '.php';

        # Convert the directory separator
        switch (DIRECTORY_SEPARATOR)
        {
            case '/':
                $path = BASE_DIR . DIRECTORY_SEPARATOR . preg_replace('/\\\/', DIRECTORY_SEPARATOR, $path);
                break;

            case '\\':
                $path = BASE_DIR . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $path);
                break;
        }

        # Require the file
        require_once $path;
    }
});


/**
 * Get top directory name translating array from the autoload config JSON file.
 *
 * @return array
 */
function topDir()
{
    $classMapFile = BASE_DIR . DIRECTORY_SEPARATOR . 'autoload.json';
    $classMapJson = file_get_contents($classMapFile);
    $classMap = json_decode($classMapJson, true);
    return $classMap['top'];
}

<?php

chdir(__DIR__);
require_once '../bootstrap/tools.php';

/*
|--------------------------------------------------------------------------
| Database migrate
|--------------------------------------------------------------------------
|
| Execute database migration commands.
|
*/

use Libraries\Logger;

$prefix = 'Database\Migrations';

$migrationMap = require_once DATABASE_DIR . DIRECTORY_SEPARATOR . 'migration_map.php';

foreach ($migrationMap as $class => $functionArray)
{
    $classFileName = DATABASE_DIR . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . preg_replace('/[\/\\\]/', DIRECTORY_SEPARATOR, $class) . '.php';

    if (is_file($classFileName))
    {
        $fullClass = "{$prefix}\\{$class}";

        foreach ($functionArray as $function)
        {
            if (method_exists($fullClass, $function))
            {
                $_isRunning = false;

                try
                {
                    echo "Executing {$fullClass}::{$function} ... ";
                    $_isRunning = true;

                    $fullClass::getInstance()->$function();

                    echo "\033[32;1mDone\033[0m\n";
                }
                catch (Throwable $ex)
                {
                    if ($_isRunning) echo "\033[31;1mFailed\033[0m\n";

                    $exCode = $ex->getCode();
                    $exMsg  = $ex->getMessage();
                    Logger::getInstance()->logError("Exception while migrate {$class}::{$function}: ({$exCode}) {$exMsg}");
                }
            }
            else
            {
                echo "Migration function \033[33;1m{$class}::{$function}\033[0m does not exist\n";
            }
        }
    }
    else
    {
        echo "Migration class \033[33;1m{$class}\033[0m does not exist\n";
    }
}

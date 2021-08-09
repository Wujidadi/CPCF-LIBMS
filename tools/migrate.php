<?php

/*
|--------------------------------------------------------------------------
| Database migrate
|--------------------------------------------------------------------------
|
| Execute database migration commands.
|
*/

require_once '_startup.php';

use Libraries\Logger;

$prefix = 'Database\Migrations';

$migrationMap = require_once VENDOR_DIR . '/migration/migration_map.php';

foreach ($migrationMap as $class => $functionArray)
{
    foreach ($functionArray as $function)
    {
        $_isRunning = false;

        try
        {
            $fullClass = "{$prefix}\\{$class}";

            echo "Executing {$fullClass}::{$function} ... ";
            $_isRunning = true;

            $fullClass::getInstance()->$function();

            echo "\033[32;1mDone\033[0m\n";
        }
        catch (Exception $ex)
        {
            if ($_isRunning) echo "\033[31;1mFailed\033[0m\n";

            $exCode = $ex->getCode();
            $exMsg  = $ex->getMessage();
            Logger::getInstance()->logError("Exception while migrate {$class}::{$function}: ({$exCode}) {$exMsg}");
        }
    }
}

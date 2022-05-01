<?php

chdir(__DIR__);
require_once '../bootstrap/tools.php';

/*
|--------------------------------------------------------------------------
| SQLite Database migrate
|--------------------------------------------------------------------------
|
| Execute SQLite database migration commands.
|
*/

use Libraries\Database\SQLiteAPI;
use Libraries\Logger;

$migrationMap = require_once DATABASE_DIR . '/sqlite/migration_map.php';

foreach ($migrationMap as $migrationFile)
{
    $fileName = DATABASE_DIR . "/sqlite/migrations/{$migrationFile}.sql";

    if (is_file($fileName))
    {
        $sql = file_get_contents($fileName);

        $schema = explode('.', $migrationFile);
        $dbName = $schema[0];
        $tableName = $schema[1];

        $dbObjName = "sqlite{$dbName}";
        $dbFile = STORAGE_DIR . "/sqlite/{$dbName}.db";

        try
        {
            if (!isset(${$dbObjName}) || !is_object(${$dbObjName}) || get_class(${$dbObjName}) !== 'Libraries\Database\SQLiteAPI')
            {
                ${$dbObjName} = new SQLiteAPI($dbFile);
            }

            echo "Executing {$migrationFile} ... ";

            ${$dbObjName}->beginTransaction();
            ${$dbObjName}->query($sql);
            ${$dbObjName}->commit();

            echo "\033[32;1mDone\033[0m\n";
        }
        catch (\PDOException $ex)
        {
            if (${$dbObjName}->inTransaction())
            {
                ${$dbObjName}->rollBack();
            }

            $exCode = $ex->getCode();
            $exMsg = $ex->getMessage();
            $exTrace = $ex->getTraceAsString();
            Logger::getInstance()->logError("Exception while migrate {$migrationFile}: ({$exCode}) {$exMsg}\n{$exTrace}");

            echo "\033[31;1mFailed\033[0m\n";
        }
    }
}

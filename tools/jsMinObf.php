<?php

chdir(__DIR__);
require_once '../bootstrap/tools.php';

/*
|--------------------------------------------------------------------------
| JavaScript Minimizing and Obfuscation
|--------------------------------------------------------------------------
|
| Minimize and obfuscate JavaScript codes according to the jsmap file
| (jsmap.json).
| It is required to install webpack and terser-webpack-plugin (both are
| npm packages) in your OS first (can be installed globally).
|
*/

$file = 'jsmap.json';
$path = BASE_DIR . DIRECTORY_SEPARATOR;
$json = file_get_contents("{$path}{$file}");

$jsScriptMap = json_decode($json, true);

if ($jsScriptMap)
{
    chdir(dirname(__DIR__));

    foreach ($jsScriptMap as $src => $dst)
    {
        if ($dst == '')
        {
            $dst = $src;
        }

        $srcWithRelativePath = 'resources' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $src);
        $dstWithRelativePath = 'public'    . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $dst);

        $srcWithFullPath = RESOURCE_DIR . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $src);
        $dstWithFullPath = PUBLIC_DIR   . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $dst);

        $dstDirectory = dirname($dstWithFullPath);
        $dstFileName  = basename($dstWithFullPath);

        echo "\033[33;1m{$srcWithRelativePath}\033[0m => \033[32;1m{$dstWithRelativePath}\033[0m ... ";

        $command = "webpack {$srcWithFullPath} -o {$dstDirectory} --output-filename {$dstFileName}";
        if (!in_array('--verbose', $argv))
        {
            $command .= ' > /dev/null 2>&1';
        }
        $result = system($command, $return_var);
        if ($return_var == 0)
        {
            echo "\033[36;1mDone\033[0m\n";
        }
        else
        {
            echo "\033[31;1mFailed\033[0m\n";
        }
    }
}
else
{
    echo "\033[31;1mThere may be problem(s) in {$file}, please check the file.\033[0m\n";
}

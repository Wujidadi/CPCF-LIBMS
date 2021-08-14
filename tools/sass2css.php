<?php

chdir(__DIR__);
require_once '../bootstrap/tools.php';

/*
|--------------------------------------------------------------------------
| SASS/SCSS to CSS
|--------------------------------------------------------------------------
|
| Convert SASS/SCSS to CSS according to the sassmap file (sassmap.json).
| It is required to install SASS in your OS first. 
|
*/

$file = 'sassmap.json';
$path = BASE_DIR . DIRECTORY_SEPARATOR;
$json = file_get_contents($path . $file);

$stylesheetMap = json_decode($json, true);

if ($stylesheetMap)
{
    foreach ($stylesheetMap as $sass => $css)
    {
        if ($css == '')
        {
            $css = preg_replace('/\.s(?:a|c)ss$/', '.css', $sass);
        }

        $sassWithRelativePath = 'resources' . DIRECTORY_SEPARATOR . 'sass' . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $sass);
        $cssWithRelativePath  = 'public'    . DIRECTORY_SEPARATOR . 'css'  . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $css);

        $sassWithFullPath = RESOURCE_DIR . DIRECTORY_SEPARATOR . 'sass' . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $sass);
        $cssWithFullPath  = PUBLIC_DIR   . DIRECTORY_SEPARATOR . 'css'  . DIRECTORY_SEPARATOR . preg_replace('/\//', DIRECTORY_SEPARATOR, $css);

        echo sprintf("\033[33;1m%s\033[0m => \033[32;1m%s\033[0m ... \033[31;1m", $sassWithRelativePath, $cssWithRelativePath);

        $command = sprintf('sass --charset --no-source-map "%s" "%s"', $sassWithFullPath, $cssWithFullPath);
        $result = system($command, $return_var);
        if ($return_var == 0)
        {
            echo sprintf("\033[0m\033[36;1m%s\033[0m\n", 'Done');
        }
    }
}
else
{
    echo sprintf('There may be problem(s) in %s, please check the file.%s', $file, PHP_EOL);
}

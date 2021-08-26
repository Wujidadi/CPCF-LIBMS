<?php

chdir(__DIR__);
require_once '../bootstrap/tools.php';

/*
|--------------------------------------------------------------------------
| SASS/SCSS to CSS
|--------------------------------------------------------------------------
|
| Convert SASS/SCSS to CSS according to the sassmap file (sassmap.json).
| It is required to install SASS and clean-css (npm) in your OS first.
|
*/

$file = 'sassmap.json';
$path = BASE_DIR . DIRECTORY_SEPARATOR;
$json = file_get_contents("{$path}{$file}");

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

        echo "\033[33;1m{$sassWithRelativePath}\033[0m => \033[32;1m{$cssWithRelativePath}\033[0m ... ";

        $command = "sass --charset --no-source-map \"{$sassWithFullPath}\" \"{$cssWithFullPath}\"";
        if (!in_array('--verbose', $argv))
        {
            $command .= ' > /dev/null 2>&1';
        }
        $result = system($command, $return_var);
        if ($return_var == 0)
        {
            if (in_array('-c', $argv))
            {
                $command = "cleancss {$cssWithFullPath} -o {$cssWithFullPath}.temp ; mv {$cssWithFullPath}.temp {$cssWithFullPath}";
                $result = system($command, $return_var);
                if ($return_var == 0)
                {
                    echo "\033[36;1mDone with CSS minimized\033[0m\n";
                }
            }
            else
            {
                echo "\033[36;1mDone\033[0m\n";
            }
        }
    }
}
else
{
    echo "\033[31;1mThere may be problem(s) in {$file}, please check the file.\033[0m\n";
}

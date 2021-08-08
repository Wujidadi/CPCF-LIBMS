<?php require_once inject('Demo.Maintainer'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Tangerine">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cinzel Decorative">
    <style><?php
        loadCSS(PUBLIC_DIR . '/css/demo.css');
    ?></style>
</head>
<body>
    <main>
        <!-- Toggle -->
        <div id="toggle-font" class="toggle">Modern</div>

        <!-- Elvish -->
        <div id="main-message-elvish" class="main-message elvish" title="<?php echo $mainMessage; ?>"><?php echo $mainMessage; ?></div>
        <div id="sub-message-elvish" class="sub-message elvish" title="Contact to the Maintainer"><?php

if (isset($maintainer))
{
    $mailto = sprintf('<a href="mailto:%1$s<%2$s>">Contact to the Maintainer</a>', $maintainer['name'], $maintainer['address']);
    echo PHP_EOL . Blank(12) . $mailto . PHP_EOL . Blank(8);
}

        ?></div>

        <!-- Modern -->
        <div id="main-message-modern" class="main-message modern hidden" title="<?php echo $mainMessage; ?>"><?php echo $mainMessage; ?></div>
        <div id="sub-message-modern" class="sub-message modern hidden" title="Contact to the Maintainer"><?php

if (isset($maintainer))
{
    $mailto = sprintf('<a href="mailto:%1$s<%2$s>">Contact to the Maintainer</a>', $maintainer['name'], $maintainer['address']);
    echo PHP_EOL . Blank(12) . $mailto . PHP_EOL . Blank(8);
}

        ?></div>
    </main>

    <!-- <script src="<?php echo AssetCachebuster('/js/demo.js', CachebusterLength); ?>"></script> -->
    <script><?php
        loadJS(PUBLIC_DIR . '/js/demo.js');
        loadJS(PUBLIC_DIR . '/js/demo-toggle.js');
    ?></script>
</body>
</html>
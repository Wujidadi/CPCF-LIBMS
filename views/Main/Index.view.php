<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'title' ?></title>
    <link rel="stylesheet" href="/libraries/Bootstrap/bootstrap.min.css">
    <script src="/libraries/Bootstrap/bootstrap.min.js"></script>
    <script src="/libraries/Vue/vue.global.prod.js"></script>
    <script src="/libraries/axios/axios.min.js"></script>
    <script src="/libraries/Cck/cck.encrypt.js"></script>
    <link rel="stylesheet" href="/css/caterpillar-main.css">
</head>
<body class="caterpillar caterpillar-default">
    <div id="app">
        <?php include viewPath('components.common._header'); ?>
        <?php include viewPath('components.common._sidebar'); ?>
        <main class="p-3">
            <div id="mainContent" class="content">
                <?php include viewPath($template); ?>
            </div>
        </main>
    </div>
    <script>const PageContext = '<?= $pageContext ?>';</script>
    <script src="/js/common.js"></script>
    <?= $scripts ?>
</body>
</html>
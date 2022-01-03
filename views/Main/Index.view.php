<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'title' ?></title>
    <?php include viewPath('shared._commonLibraries'); ?>
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
    <script>
        const PageContext = '<?= $pageContext ?>';
        <?php if (in_array($pageContext, ['AddBook', 'EditBook'])) {
            if ($pageContext === 'EditBook') { ?>
                const BookData = <?= JsonUnescaped($bookData) ?>;
            <?php } ?>
            const StorageTypes = <?= JsonUnescaped($storageTypes) ?>;
        <?php } ?>
    </script>
    <script src="/js/common.js"></script>
    <?= $scripts ?>
</body>
</html>
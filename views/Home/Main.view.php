<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'title'; ?></title>
    <link rel="stylesheet" href="/libraries/Bootstrap/bootstrap.min.css">
    <script src="/libraries/Bootstrap/bootstrap.min.js"></script>
    <script src="/libraries/Vue/vue.global.prod.js"></script>
    <link rel="stylesheet" href="/css/caterpillar.css">
</head>
<body class="caterpillar caterpillar-default">
    <?php include viewPath('components.common.header'); ?>
    <?php include viewPath('components.common.left-side'); ?>
    <main>
        <div>Hello! Caterpillar.</div>
    </main>
</body>
</html>
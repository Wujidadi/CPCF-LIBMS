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
    <link rel="stylesheet" href="/css/caterpillar-main.css">
</head>
<body class="caterpillar caterpillar-default">
    <?php include viewPath('components.common.header'); ?>
    <?php include viewPath('components.common.left-side'); ?>
    <main class="p-3" style="height: 2000px">
        <div id="mainContent" class="tab-content">
            <div id="home"        class="tab-pane fade show active">首頁</div>
            <div id="circulation" class="tab-pane fade"            >借還書</div>
            <div id="book"        class="tab-pane fade"            >圖書</div>
            <div id="member"      class="tab-pane fade"            >會員</div>
        </div>
    </main>
    <script>
        let circulationTab = document.querySelector('#circulationTab');
        circulationTab.addEventListener('shown.bs.tab', function(event)
        {
            console.log('借還書分頁顯示');
        });
        circulationTab.addEventListener('hidden.bs.tab', function(event)
        {
            console.log('借還書分頁隱藏');
        });
    </script>
</body>
</html>
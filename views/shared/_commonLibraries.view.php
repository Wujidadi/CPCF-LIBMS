<link rel="stylesheet" href="/libraries/Bootstrap/bootstrap.min.css">
<script src="/libraries/Bootstrap/bootstrap.min.js"></script>
<?php if (IS_DEV) { ?>
<script src="/libraries/Vue/vue.global.js"></script>
<?php } else { ?>
<script src="/libraries/Vue/vue.global.prod.js"></script> 
<?php } ?>
<script src="/libraries/axios/axios.min.js"></script>
<script src="/libraries/Cck/prototype.js"></script>
<script src="/libraries/Cck/timeout.js"></script>
<?php if (IS_DEV) { ?>
<script src="/libraries/Cck/cck.encrypt.js"></script>
<?php } else { ?>
<script src="/libraries/Cck/cck.js"></script>
<?php } ?>

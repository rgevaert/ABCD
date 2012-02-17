<?php
session_start();
session_unset();
session_destroy();
?>
<script>
	top.window.location.href="../../indexmysite.php";
</script>
<?
if ( $def['GOOGLE_ANALYTICS_ID'] != '')
{
?>
    <script src="http://www.google-analytics.com/ga.js" type="text/javascript"></script>

    <script type="text/javascript">
        var pageTracker = _gat._getTracker("<?=$def['GOOGLE_ANALYTICS_ID']?>");
        pageTracker._trackPageview();
    </script>

    <!-- old style urchin code
	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
	<script type="text/javascript">
		_uacct = "";
		urchinTracker();
	</script>
    -->
<?}?>
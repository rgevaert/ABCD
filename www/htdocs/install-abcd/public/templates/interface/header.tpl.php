<head>
	<title>{$BVS_LANG.titleApp}{if $listRequest} :: {if $smarty.get.m} {$BVS_LANG.adminDataOf} {/if}{$BVS_LANG.$listRequest}{/if}</title>

	<meta http-equiv="Expires" content="-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="Content-Type" content="text/html; charset={$BVS_LANG.metaCharset}" />
	<meta http-equiv="Content-Language" content="{$BVS_LANG.metaLanguage}" />

	<meta name="robots" content="all" />
	<meta http-equiv="keywords" content="{$BVS_LANG.metaKeywords}" />
	<meta http-equiv="description" content="{$BVS_LANG.metaDescription}" />
	<!-- Stylesheets -->
	<!--Styles for yui -->
	<style type="text/css">
	{literal}
		/* custom styles for this example */
		#dt-pag-nav { margin-bottom:1em; } /* custom pagination UI */
		#yui-history-iframe {
  			position:absolute;
  			top:0; left:0;
  			width:1px; height:1px; /* avoid scrollbars */
  			visibility:hidden;
		}
	{/literal}
	</style>
	<link rel="shortcut icon" href="favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="public/css/template.css" media="screen"/>
	<script language="JavaScript" type="text/javascript" src="public/js/functions.js"></script>
 
</head>

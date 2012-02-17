<div class="heading">
	<div class="institutionalInfo">
		<h1 class="logo"><a href="{$BVS_LANG.institutionURI}" title="{$BVS_LANG.BVSlogo}" target="_blank"><span>{$BVS_LANG.BVSlogo}</span></a></h1>
		<h1>{$BVS_LANG.nameBVS}</h1>
		<h2>{$BVS_LANG.titleApp}</h2>
	</div>	
	<div class="userInfo">
	{if $smarty.session.identified}
		<span>{if $smarty.session.fullName} {$smarty.session.fullName} {else} {$smarty.session.logged} {/if}</span> | 
		<a href="?m=users&amp;edit={$smarty.session.mfn}&amp;lang={$smarty.get.lang}">{$BVS_LANG.myPreferences}</a> | 
  		<a href="?action=signoff&amp;lang={$smarty.get.lang}" class="button_logout"><span>{$BVS_LANG.logOff}</span></a>
	{else}
		<div>
			{if $BVS_LANG.metaLanguage neq "pt"}<a href="?lang=pt" target="_self">{$BVS_LANG.portuguese}</a> | {/if}
			{if $BVS_LANG.metaLanguage neq "en"}<a href="?lang=en" target="_self">{$BVS_LANG.english}</a> | {/if}
			{*if $BVS_LANG.metaLanguage neq "es"}<a href="?lang=es" target="_self">{$BVS_LANG.espanish}</a> | {/if}
			{if $BVS_LANG.metaLanguage neq "fr"}<a href="?lang=fr" target="_self">{$BVS_LANG.french}</a>{/if*}
		</div>

	{/if}
	</div>
	<div class="spacer">&#160;</div>
</div>
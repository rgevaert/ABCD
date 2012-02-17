<div class="login">
<div class="loginForm">
	<div class="boxTop">
		<div class="btLeft">&#160;</div>
		<div class="btRight">&#160;</div>
	</div>
	<div class="boxContent">
	<form action="?action=install&amp;lang={if $smarty.get.lang eq ''}{$smarty.get.lang}{else}{$BVS_LANG.metaLanguage}{/if}" enctype="multipart/form-data" name="formData" id="formData" class="form"  method="post">

		<input type="hidden" name="field[action]" id="action" value="do" />
		
		<div class="formRow">

			<label for="user">{$BVS_LANG.installDir}</label>
			<input type="text" name="field[installDir]" id="installDir" value="{$smarty.server.DOCUMENT_ROOT}{$smarty.server.PHP_SELF|replace:'install-abcd/index.php':''}" class="textEntry superTextEntry" onfocus="this.className = 'textEntry superTextEntry textEntryFocus';" onblur="this.className = 'textEntry superTextEntry';" />
		</div>

		<div class="submitRow">
			<div class="frRightColumn">
				<a href="javascript:confirmInstall('{$BVS_LANG.installQuestion}', '{$BVS_LANG.installAbort}')" class="defaultButton goButton" id="btLogin">
					<img src="public/images/common/spacer.gif" alt="" title="" />
					<span><strong>{$BVS_LANG.install}</strong></span>
				</a>
			</div>
			<div class="spacer">&#160;</div>
		</div>
		<div class="spacer">&#160;</div>

	</form>
	</div>

	<div class="boxBottom">
		<div class="bbLeft">&#160;</div>
		<div class="bbRight">&#160;</div>
	</div>
</div>
</div>
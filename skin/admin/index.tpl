<?php
/**
 * CubeCart v6
 * ========================================
 * CubeCart is a registered trade mark of CubeCart Limited
 * Copyright CubeCart Limited 2014. All rights reserved.
 * UK Private Limited Company No. 5323904
 * ========================================
 * Web:   http://www.cubecart.com
 * Email:  sales@devellion.com
 * License:  GPL-2.0 http://opensource.org/licenses/GPL-2.0
 */
?>
<form action="{$VAL_SELF}" method="post" enctype="multipart/form-data">
	<div id="Coinbase" class="tab_content">
  		<h3>{$TITLE}</h3>
		<p class="copyText">{$LANG.coinbase.module_description}</p>
  		<fieldset><legend>{$LANG.module.cubecart_settings}</legend>
			<div><label for="status">{$LANG.common.status}</label><span><input type="hidden" name="module[status]" id="status" class="toggle" value="{$MODULE.status}" /></span></div>
			<div><label for="position">{$LANG.module.position}</label><span><input type="text" name="module[position]" id="position" class="textbox number" value="{$MODULE.position}" /></span></div>
			<div>
				<label for="scope">{$LANG.module.scope}</label>
				<span>
					<select name="module[scope]">
						<option value="both" {$SELECT_scope_both}>{$LANG.module.both}</option>
						<option value="main" {$SELECT_scope_main}>{$LANG.module.main}</option>
						<option value="mobile" {$SELECT_scope_mobile}>{$LANG.module.mobile}</option>
					</select>
				</span>
			</div>
			<div><label for="default">{$LANG.common.default}</label><span><input type="hidden" name="module[default]" id="default" class="toggle" value="{$MODULE.default}" /></span></div>
			<div><label for="description">{$LANG.common.description} *</label><span><input name="module[desc]" id="description" class="textbox" type="text" value="{$MODULE.desc}" /></span></div>
      <div><label for="api_key">{$LANG.coinbase.api_key}</label><span><input name="module[api_key]" id="api_key" class="textbox" type="password" value="{$MODULE.api_key}" /></span></div>
      <div><label for="api_secret">{$LANG.coinbase.api_secret}</label><span><input name="module[api_secret]" id="api_secret" class="textbox" type="password" value="{$MODULE.api_secret}" /></span></div>
  		</fieldset>


  		</div>
  		{$MODULE_ZONES}
  		<div class="form_control">
			<input type="submit" name="save" value="{$LANG.common.save}" />
  		</div>
  	<input type="hidden" name="token" value="{$SESSION_TOKEN}" />
</form>


<h1>{L_CONFIGURATION_TITLE}</h1>

<p>{L_CONFIGURATION_EXPLAIN}</p>

<form action="{S_CONFIG_ACTION}" method="post"><table width="99%" cellpadding="4" cellspacing="1" border="0" align="center" class="forumline">
	<tr>
	  <th class="thHead" colspan="2">{L_GENERAL_SETTINGS} (phpBBlog {L_VERSION})</th>
	</tr>
	<tr>
		<td class="row1">{L_PERMIT_MOD}</td>
		<td class="row2"><input type="radio" name="permit_mod" value="1" {PERMIT_MOD_YES} /> {L_YES}&nbsp;&nbsp;<input type="radio" name="permit_mod" value="0" {PERMIT_MOD_NO} /> {L_NO}</td>
	</tr>
	<tr>
		<td class="row1">{L_ONENTRY_SENDMAIL}</td>
		<td class="row2"><input type="radio" name="onentry_sendmail" value="1" {ONENTRY_SENDMAIL_YES} /> {L_YES}&nbsp;&nbsp;<input type="radio" name="onentry_sendmail" value="0" {ONENTRY_SENDMAIL_NO} /> {L_NO}</td>
	</tr>
	<tr>
		<td class="row1">{L_ALLOW_CAT}</td>
		<td class="row2"><input type="radio" name="allow_cat" value="1" {ALLOW_CAT_YES} /> {L_YES}&nbsp;&nbsp;<input type="radio" name="allow_cat" value="0" {ALLOW_CAT_NO} /> {L_NO}</td>
	</tr>
	<tr>
		<td class="row1">{L_VIEW_SMILE}<br /></td>
		<td class="row2"><input class="post" type="text" size="5" maxlength="4" name="smilies_row" value="{SMILIES_ROW}" />&nbsp;X&nbsp;<input class="post" type="text" size="5" maxlength="4" name="smilies_column" value="{SMILIES_COLUMN}" /></td>
	</tr>
	<tr>
		<td class="catBottom" colspan="2" align="center">{S_HIDDEN_FIELDS}<input type="submit" name="submit" value="{L_SUBMIT}" class="mainoption" />&nbsp;&nbsp;<input type="reset" value="{L_RESET}" class="liteoption" />
		</td>
	</tr>
</table></form>

<br clear="all" />

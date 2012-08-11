
{NAVIGATION_BOX}

<table class="forumline" width="100%" cellspacing="1" cellpadding="3" border="0">
	<tr>
		<th class="thHead" height="25" valign="middle">{MESSAGE_TITLE}</th>
	</tr>
	<tr>
		<td class="row1" align="center">
      <form action="{S_CONFIRM_ACTION}" method="post">
         <span class="gen"><br />{MESSAGE_TEXT}<br /><br />
         Adresse des Empfängers:<br />
         <input type="text" value="{TITEL}" align="LEFT" size="45" maxlength="255" class="post" name="mailaddy" title="Adresse des Empfängers" /><br />
       
               
         {S_HIDDEN_FIELDS}

         <input type="submit" name="confirm" value="{L_YES}" class="mainoption" />&nbsp;&nbsp;
         <input type="submit" name="cancel" value="{L_NO}" class="liteoption" />
         </span>
      </form>
    </td>
	</tr>
</table>

<br clear="all" />

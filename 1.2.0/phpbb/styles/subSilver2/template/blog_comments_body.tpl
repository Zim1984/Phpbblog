<script language="JavaScript">
<!--
	self.focus();

	function textCounter() 
  {

    var maxlimit = 255;
 
    var Laenge = document.post.comments.value.length;
 
    if(Laenge > maxlimit)
    {
        document.post.comments.value = document.post.comments.value.substring(0, maxlimit);
        RestlicheZeichen = 0;
    }
    else
    {
        RestlicheZeichen = maxlimit-Laenge;
    }
 
    document.post.counter.value = RestlicheZeichen;

	}


	function bDel(id,id_com) {
		document.location.href='blog_comments.php?perform=del&id=' + id + '&id_com=' + id_com;
	}

// -->
</script>

<form enctype="multipart/form-data" action="{U_FORM_ACTION}" method="POST" name="post">

<table width="100%" cellpadding="4" cellspacing="1" border="0" class="forumline">
<tr>
	<th class="thHead">{L_COMMENTS}</th>
</tr>

<!-- BEGIN blog_comments -->
<tr>
	<td class="{blog_comments.ROW_CLASS}">
		<span class="genmed">
		{blog_comments.COMMENT}<br />
		<b>{blog_comments.USERNAME}: {blog_comments.TIME}, {blog_comments.DATE}</b> {blog_comments.U_ADMIN_COMMAND}
		</span>
	</td>
</tr>
<!-- END blog_comments -->

<tr>
	<td height="1" class="spaceRow"><img src="../templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
</tr>

<tr>
	<td class="row3">
	<span class="genmed">
	<b>{L_COMMENT}:</b><br />
	<textarea name="comments" rows="5" cols="30" onkeypress="textCounter();" onblur="textCounter();" onchange="textCounter();" onfocus="textCounter();" onkeydown="textCounter();" onkeyup="textCounter();"></textarea><br />
	<input type="text" name="counter" maxlength="3" size="3" value="255" > {L_CHARACTERS_REMAINING}
	</span>
	</td>
</tr>
<tr>
	<td class="catBottom" align="center"><input type="submit" name="submit" value="{L_SUBMIT}" class="liteoption"></td>
</tr>
</table>

</form>

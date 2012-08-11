<?php

 /***************************************************************************
 *                                blog_comments.php
 *                            -------------------
 *   begin                : Friday, Mar 19, 2004
 *   copyright           : (C) 2004 Scerni Gianluca - Sko22
 *   email                : webmaster@quellicheilpc.com
 *
 *   $Id: blog_comments.php,v 1.0.9 2006/08/29 13:39:27 Sko22
 *
 *
 ***************************************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.'.$phpEx);


//
// Start session management
//
$user->session_begin();
$user->setup();
$auth->acl($user->data);
//
// End session management
//

include($phpbb_root_path . '/includes/phpBBlog/blog_class.'.$phpEx);
$user->add_lang('mods/phpBBlog/common');


$blog_config = array();
$phpbblog_c = new phpbblog();
$blog_config = $phpbblog_c->blog_config();


// define team to manage guest
if ( $user->data['is_registered'] )
{

   switch ($user->data['group_id'])	
        { 
         case "5": define('STAFF', true); 
         break;
         case "4": 
		          if ( $blog_config['permit_mod'] )
			         {
			           define('STAFF', true);  
			         }
	       break;
         default: 
        		  if ( $blog_config['blogger_group'] )
			         {
			           include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
			           //$gruppen_id = array(5,10,12,15);
			           $st_group_id = explode(',', $blog_config['blogger_group']);
			           $is_in_group = group_memberships($st_group_id, $user->data['user_id'] , true);
			         }
		          if ( $blog_config['blogger_user'] )
			         {
			           $st_user_id = explode(',', $blog_config['blogger_user']);
			           $is_in_userlist = false;
			         }
			        if ( $is_in_group == true || $is_in_userlist == true )
			         {
			          define('STAFF2', true);  
			         } 
	       break;
        }
}

$id = (int) request_var('id', '');
$perform    = request_var('perform', '');
$id_com = (int) request_var('id_com', '');
$error_msg = '';

		if ( $perform == 'confirm' )
		{
			if ( isset($HTTP_GET_VARS['cid']) )
			{
			$GBid = $HTTP_GET_VARS['cid'];
			}
			else
			{
			$GBid = '';
			}

			include($phpbb_root_path . 'includes/phpBBlog/blog_captcha.'.$phpEx);
			$captcha_class = new blog_captcha;
			$captcha_class->generateImage($GBid);
			exit;
		}


		$sql = "SELECT permit_com FROM " . BLOG_TABLE . " WHERE id = $id LIMIT 1";
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, "Could not select the blog text.", '', __LINE__, __FILE__, $sql);
  	}
  	else
  	{

		  while( $row = $db->sql_fetchrow($result) )
		  {
        $permit_com = $row['permit_com'];
		  }

  	}

	if (!empty($blog_config['guest_com_captcha']) && !$user->data['is_registered'])
	{
		include($phpbb_root_path . 'includes/phpBBlog/blog_captcha.'.$phpEx);
		$captcha_class = new blog_captcha;
	}

if ($perform == "del" && $id && $id_com )
{

		//
		// Check privileges
		//
		if(!defined('STAFF') /*$userdata['user_level'] != ADMIN*/ )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog_comments.$phpEx?id=$id") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);	
		}

		$confirm = (isset($_POST['confirm'])) ? true : false;
		$cancel	 = (isset($_POST['cancel'])) ? true : false;

		if ( $cancel )
		{
			redirect(append_sid("{$phpbb_root_path}blog_comments.$phpEx?id=" . $id . "", true));
		}

		if (!confirm_box(true))
		{

			confirm_box(false, $user->lang['BLOG_CONFIRM_DELETE'], build_hidden_fields(array(
				'perform'	=> $perform,
				'id'		=> $id,
				'$id_com'	=> $$id_com)) );
		}
		else
		{
		
			$sql = "DELETE FROM " . BLOG_COM_TABLE . " WHERE id = $id_com LIMIT 1";
			if( !($db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not delete the comment', '', __LINE__, __FILE__, $sql);
			}

		$sql2 = "UPDATE " . BLOG_TABLE . " SET blog_com_count = blog_com_count - '1' WHERE id = '$id_com'";

		if( !($result = $db->sql_query($sql2)) )
		{
				message_die(GENERAL_ERROR, 'Could not query guestbook', '', __LINE__, __FILE__, $sql2);
		}

			/*$message = "Deleted.<br /><br />" . 
			sprintf("Clicca %squi%s per tornare al blog.", "<a href=\"" . append_sid("blog_comments.$phpEx?id=" . $id) . "\">", "</a>");
			message_die(GENERAL_MESSAGE, $message);*/

			redirect(append_sid("{$phpbb_root_path}blog_comments.$phpEx?id=" . $id . "", true));

		}


}
else if ($perform == "edit" && $id && $id_com )
{

}
else if ($perform == "mod" && $id && $id_com )
{

}
else
{

	$comments  =  utf8_normalize_nfc(request_var('comments', '', true));
	$comments = trim( nl2br( htmlspecialchars( $comments ) ) );
	$username =  utf8_normalize_nfc(request_var('username', '', true));
	$usermail =  utf8_normalize_nfc(request_var('usermail', '', true));

	if ( isset( $HTTP_POST_VARS['submit'] ) && $comments != "" )
	{

		$user_id = $user->data['user_id'];
		$date = time();

    //-- mod : visual confirmation for cricca guestbook 
    //-- add
    if (!empty($blog_config['guest_com_captcha']) && !$user->data['is_registered'])
    {
		  $captcha_class->checkCaptchaCode($error_msg, $HTTP_POST_VARS['confirm_id'], $HTTP_POST_VARS['confirm_code']);
    }
    //-- fin mod : visual confirmation for cricca guestbook

		if( defined('STAFF') || defined('STAFF2') || $user->data['is_registered'] || $blog_config['allow_guest_com'] )
		{
		  $sql = "INSERT INTO " . BLOG_COM_TABLE . " (id_blog, user_id, user_name, user_email, com, date) VALUES ($id, $user_id, '$username', '$usermail', '".$db->sql_escape($comments)."', '".$db->sql_escape($date)."' )";		
		  $db->sql_query($sql);

	
		  $sql2 = "UPDATE " . BLOG_TABLE . " SET blog_com_count = blog_com_count + '1' WHERE id = '$id'";
		  $db->sql_query($sql2);


		}
		else
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_COM'], "<a href=\"" . append_sid("{$phpbb_root_path}blog_comments.$phpEx?id=$id") . "\">", "</a>");
			trigger_error( $message );		
		}

		      if ( $blog_config['onentry_sendmail'] )
			      {
				      $domain = ($config['server_name']);
				      $to  = $config['board_email'];
				      $subject = $user->lang['MAIL_SUBJECT'];
				      $content = $user->lang['MAIL_CONTENT'] . "\n\n" . 'Submitted by: ' .  $user->data['username'] . "\n\n" . "$comments";

				      mail($to, $subject, $content, "From:" . $to);
			      }

		trigger_error($user->lang['GUESTBOOK_DELETE_SEND']);
	}

	$sql = "SELECT * FROM " . BLOG_COM_TABLE . " WHERE id_blog = $id  ORDER by date DESC";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Could not select comments.", '', __LINE__, __FILE__, $sql);
	}

	$i=0;
	while( $row = $db->sql_fetchrow($result) )
	{
		$id_com = $row['id'];
		$username = "";

		$sql_user = "SELECT username FROM " . USERS_TABLE . " WHERE  user_id = " . $row['user_id'];
		if( !($result_user = $db->sql_query($sql_user)) )
		{
			message_die(GENERAL_ERROR, "Could not select comment username", '', __LINE__, __FILE__, $sql);
		}
		while( $row_user = $db->sql_fetchrow($result_user) )
		{
			$username = ( $row['user_id'] <= 0 ) ? $lang['Guest'].': '.$row["user_name"] : $row_user["username"];
		}

		$usermail = $row['user_email'];
		$comment = $row['com'];
		$date = $row['date'];
		$row_class = ( !($i % 2) ) ? 'post bg1' : 'post bg2';

$admin_command = '';
	//
	// Check privileges
	//
	if(defined('STAFF') /*$userdata['user_level'] == ADMIN*/ )
	{
		$admin_command = '| <a href="javascript:bDel(' . $id . ',' . $id_com . ')">' . $user->lang['BLOG_DELETE'] . '</a>';
	}

		$template->assign_block_vars("blog_comments", array(
			'TIME' => date( 'H:i', $date ),
			'DATE' => date( 'd F, Y', $date ),
			'ROW_CLASS' => $row_class,
			'USERNAME' => $username,
			'USERMAIL' => $usermail,
			'COMMENT' => $comment,

			'U_ADMIN_COMMAND' => $admin_command
			)
		);
		$i++;
	}

}

//
// Lets build a page ...
//
$page_title = $user->lang['BLOG_COM_TITLE'];

$gen_simple_header = TRUE;

page_header($page_title);

$template->set_filenames(array(
	'body' => 'blog_comments_body.html')
);

//-- mod : visual confirmation for cricca guestbook ------------------------------------------------
//-- add
	$confirm_image = '';
	$s_hidden_fields = '';
	if (!empty($blog_config['guest_com_captcha']) && !$blog_config['allow_guest_com'] == 0 && !$user->data['is_registered'])
	{
		$captcha_class->generateCode($confirm_image, $s_hidden_fields);
    $template->assign_block_vars("switch_confirm", array(
    	'CONFIRM_IMG' => $confirm_image,
		  'L_CONFIRM_CODE_IMPAIRED'	=> sprintf($user->lang['CONFIRM_CODE_IMPAIRED'], '<a href="mailto:' . $config['board_email'] . '">', '</a>'),
		  'L_CONFIRM_CODE' => $user->lang['CONFIRM_CODE'],
		  'L_CONFIRM_CODE_EXPLAIN' => $user->lang['CONFIRM_CODE_EXPLAIN']
    ));
	}
//-- fin mod : visual confirmation for cricca guestbook

$template->assign_vars(array(
	'L_COMMENTS' => ( $permit_com ) ? ucfirst( $user->lang['BLOG_COMMENTS'] ) : ucfirst( $user->lang['BLOG_COMMENTS_DEACTIVATED'] ),
	'L_COMMENT' => $user->lang['BLOG_COM_COMMENT'],
	'L_CHARACTERS_REMAINING' => $user->lang['BLOG_COM_CHARS_REM'],
	'L_SUBMIT' => ( $permit_com ) ? $user->lang['SUBMIT'] : $user->lang['BLOG_COMMENTS_DEAKTIVATET'],
  'S_HIDDEN_FIELDS' => $s_hidden_fields,
	'U_FORM_ACTION' => append_sid("{$phpbb_root_path}blog_comments.$phpEx?id=$id")
	)
);

//$template->pparse('body');

page_footer();
?>

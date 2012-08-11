<?php
/**
*
* @package phpBB3
* @version $Id: blog.php,v 1.2.0 2007/09/26 00:35:25 Sko22 and Skippy Exp $
* @copyright  (C) 2004 Scerni Gianluca - Sko22
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/


/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/phpBBlog/blog_class.'.$phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);


//
// Start session management
//
$user->session_begin();
$user->setup();
$auth->acl($user->data);
//
// End session management
//

$user->add_lang('mods/phpBBlog/common');

/*
// May the user see the blogs
if(!$auth->acl_get('u_blogs_view'))
{
	trigger_error('NOT_AUTHORIZED');
}*/

// Get and set some variables
$perform    = request_var('perform', '');
$id = (int) request_var('id', '');
$cats = (int) request_var('cats', '');
$b = (int) request_var('b', '');
$submit  = (isset($_POST['post'])) ? true : false;
$confirm = (isset($_POST['confirm'])) ? true : false;
$cancel	 = (isset($_POST['cancel'])) ? true : false;

$category = utf8_normalize_nfc(request_var('category', '', true));
$titel =  utf8_normalize_nfc(request_var('titel', '', true));
$permit_com = (isset($_POST['permit_com'])) ? true : false;
$text  =  utf8_normalize_nfc(request_var('text', '', true));

$contents = utf8_normalize_nfc(request_var('contents', '', true));
$post_id = request_var('post_id', 0);


if( isset($_POST['blog_name']) AND isset($_POST['url']) AND isset($_POST['excerpt']) AND isset($_POST['title']) )
{
	redirect(append_sid("{$phpbb_root_path}blog_trackback.$phpEx", 'perform=view&amp;id=' . $blog_id . '&amp;item=' . $item_id));
}


function make_cat_list($id)
{
	global $db;
	$cat_option = '';
	$sql = 'SELECT cat_id, cat_titel 
		FROM ' . BLOG_CAT_TABLE . ' 
		ORDER BY cat_id';
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{

    $categorie_titel = @$user->lang[utf8_strtoupper($row['cat_titel'])];
    // take db entry if not exist a lang entry
	  if ($categorie_titel == '')
	  {
		  $categorie_titel = $row['cat_titel'];
	  }

		$cat_option .= ($id == $row['cat_id']) ?  '<option value="' .$row['cat_id']. '" selected >' .$categorie_titel. '</option>' : '<option value="' .$row['cat_id']. '">' .$categorie_titel. '</option>';
	}
	$db->sql_freeresult($result);
	return $cat_option;
}




// Was cancel pressed? If so then redirect to the appropriate page
if($perform == 'view' AND $blog_id AND $item_id AND $cancel)
{
	redirect(append_sid("{$phpbb_root_path}blog.$phpEx", 'perform=view&amp;id=' . $blog_id . '&amp;item=' . $item_id));
}


$blog_config = array();
$phpbblog_c = new phpbblog();
$blog_config = $phpbblog_c->blog_config();




// define team to manage Blog
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
/*
   if (is_string($group_ids)) 
   { 
      $group_ids = explode(',', $group_ids); 
   }
   elseif (is_int($group_ids)) 
   { 
      $group_ids = array($group_ids); 
   }*/


// If the blog isn't online show error
if( !$blog_config['blog_online'] && (!defined('STAFF') || !defined('STAFF2') ) )
{
	trigger_error('BLOG_NOT_ONLINE');
  // trigger_error('NO_MODE', E_USER_ERROR);
}


$l_text_field = "";
$l_titel_field = "";
$l_cat_list = '0';

switch($perform)
{


	case 'add':

		//
		// Check privileges
		//
		if(!defined('STAFF') && !defined('STAFF2') /*$userdata['user_level'] != ADMIN*/ )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog.$phpEx") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);	
		}

               // $bbcode_uid = make_bbcode_uid();
		
		if ( $titel != "" )
		{
			$titel = nl2br( htmlspecialchars($titel) );
    }

		//
		// Check text
		//
		if ( $text != "" )
		{


			// first, initialize some variables that you will need:
			$bbcode_uid = $bbcode_bitfield = $flags = '';
			$allow_bbcode = $allow_urls = $allow_smilies = true;


			// this function will do all the smiley, bbcode, and other parsing for you:
			generate_text_for_storage($text, $bbcode_uid, $bbcode_bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);

      $sql = "INSERT " . BLOG_TABLE . " (user_id, date, titel, text, bbcode_uid, bbcode_bitfield, flags, permit_com, category_id) VALUES (" . $user->data['user_id'] . " , '" . time() . "','".$db->sql_escape($titel)."','".$db->sql_escape($text)."', '$bbcode_uid', '$bbcode_bitfield', '$flags', '$permit_com','$category') "; 
			if ( !($result = $db->sql_query($sql)) )
			{
				//message_die(GENERAL_ERROR, "Could not insert in the blog table.", "", __LINE__, __FILE__, $sql);
				trigger_error('Could not insert in the blog table. ' , E_USER_ERROR); 
			}

			$message = $user->lang['BLOG_ADDED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog.$phpEx") . "\">", "</a>");
			trigger_error($message);
		}
		else
		{
			$message = $user->lang['BLOG_ADDED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog.$phpEx") . "\">", "</a>");
			trigger_error($message);
		}

		break;


	case 'edit':

		//
		// Check privileges
		//
		if(!defined('STAFF') )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog.$phpEx") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);
		}



		$sql = "SELECT titel, text, bbcode_uid, bbcode_bitfield, flags, permit_com, category_id FROM " . BLOG_TABLE . " WHERE id = $id LIMIT 1";
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, "Could not select the blog text.", '', __LINE__, __FILE__, $sql);
		}

		while( $row = $db->sql_fetchrow($result) )
		{
       $titel = utf8_normalize_nfc($row['titel']);
		   $text = generate_text_for_edit(utf8_normalize_nfc($row['text']), $row['bbcode_uid'], $row['flags']);
	     //$text = preg_replace('/\:(([a-z0-9]:)?)' . $row['bbcode_uid'] . '/s', '', $row['text']/*$text*/);
	     $text = str_replace("<br />", "", $text/*$row["text"]*/);

       $permit_com = $row['permit_com'];
       $l_cat_list = $row['category_id'];
		}

		//
		// Check privileges
		//
		if(defined('STAFF') )
		{

			$permit_com_yes = ( $permit_com ) ? "checked=\"checked\"" : "";
			$permit_com_no = ( !$permit_com ) ? "checked=\"checked\"" : "";
			$l_text_field   = $titel;
			$l_titel_field  = $text;

			$l_add_blog	= $user->lang['MODIFY_BLOG'];
			$l_submit	= ucfirst( $user->lang['BLOG_MODIFY'] );
			$l_titel	= $user->lang['BLOG_ENTRY_TITEL'];
			$s_hidden_field	= '<input type="hidden" name="perform" value="mod" /><input type="hidden" name="id" value="' . $id .'" />';

		}

		break;


	case 'mod':

		//
		// Check privileges
		//
		if(!defined('STAFF') )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog.$phpEx") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);
		}


    		//$bbcode_uid = make_bbcode_uid();

		//
		// Check text
		//
		if ( $text != "" )
		{

			// first, initialize some variables that you will need:
			$bbcode_uid = $bbcode_bitfield = $flags = '';
			$allow_bbcode = $allow_urls = $allow_smilies = true;

			// this function will do all the smiley, bbcode, and other parsing for you:
			generate_text_for_storage($text, $bbcode_uid, $bbcode_bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);



			$sql = "UPDATE " . BLOG_TABLE . "	SET titel = '$titel', text = '" . bbencode_first_pass( $text, $bbcode_uid ) . "' , bbcode_uid = '$bbcode_uid' , permit_com = '$permit_com' , category_id = '$category'  WHERE id = $id";
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not modify the blog text.', '', __LINE__, __FILE__, $sql);
			}

			//
			// Check privileges
			//
			if(defined('STAFF') /*$userdata['user_level'] == ADMIN*/ )
			{

			$permit_com_yes = "checked=\"checked\"";
			$permit_com_no = "";
			$l_add_blog	= $user->lang['ADD_BLOG'];
			$l_submit	= $user->lang['SUBMIT'];
			$l_titel	= $user->lang['BLOG_ENTRY_TITEL'];
			$s_hidden_field	= '<input type="hidden" name="perform" value="add" />';

			}

		}
			else
		{
			$message = $user->lang['BLOG_ADDED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"javascript:history.back(-1)\">", "</a>");
			trigger_error($message);	
		}

		break;


	case 'del':

		//
		// Check privileges
		//
		if( !defined('STAFF') /*$userdata['user_level'] != ADMIN*/ )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog.$phpEx") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);	
		}


		if ( $cancel )
		{
			redirect(append_sid("{$phpbb_root_path}blog.$phpEx?b=" . $b . "", true));
		}


		if (!confirm_box(true))
		{

			confirm_box(false, $user->lang['BLOG_CONFIRM_DELETE'], build_hidden_fields(array(
				'perform'	=> $perform,
				'id'		=> $id,
				'b'		=> $b)) );
		}
		else
		{
			unset($b);

			/*$sql = "DELETE FROM " . BLOG_TABLE . " WHERE id = $id LIMIT 1";
			if( !($db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not delete blog event', '', __LINE__, __FILE__, $sql);
			}

			$sql = "DELETE FROM " . BLOG_COM_TABLE . " WHERE id_blog=$id";
			if( !($db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not delete the comments for this blog event', '', __LINE__, __FILE__, $sql);
			}

			$sql = "DELETE FROM " . BLOG_TRACKBACKS_TABLE . " WHERE tb_entry_ID=$id";
			if( !($db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not delete the trackbacks for this blog event', '', __LINE__, __FILE__, $sql);
			}*/

			$sql = "DELETE FROM " . BLOG_TABLE . " as b, " . BLOG_COM_TABLE . " as c, " . BLOG_TRACKBACKS_TABLE . " as t 
      WHERE (t.tb_entry_ID=$id)
      AND (t.tb_entry_ID = b.id)
      AND (t.tb_entry_ID = c.id_blog)";
			if( !($db->sql_query($sql)) )
			{
			  trigger_error('Could not delete the blog entry , his comments and trackbacks', E_USER_ERROR);
				//message_die(GENERAL_ERROR, 'Could not delete the blog entry , his comments and trackbacks', '', __LINE__, __FILE__, $sql);
			}

			//
			// Check privileges
			//
			if(defined('STAFF') )
			{

			$permit_com_yes = "checked=\"checked\"";
			$permit_com_no = "";
			$l_add_blog	= $user->lang['ADD_BLOG'];
			$l_submit	= $user->lang['SUBMIT'];
			$l_titel	= $user->lang['BLOG_ENTRY_TITEL'];
			$s_hidden_field	= '<input type="hidden" name="perform" value="add" />';

			}

		}
		break;
	case 'mail':

		//
		// Check privileges
		//
		if( !defined('STAFF') )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog.$phpEx") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);
		}

		$confirm = (isset($HTTP_POST_VARS['confirm'])) ? $HTTP_POST_VARS['confirm'] : $HTTP_GET_VARS['confirm'];
		$cancel = (isset($HTTP_POST_VARS['cancel'])) ? $HTTP_POST_VARS['cancel'] : $HTTP_GET_VARS['cancel'];
		$b = (isset($HTTP_POST_VARS['b'])) ? $HTTP_POST_VARS['b'] : $HTTP_GET_VARS['b'];
		$perform = (isset($HTTP_POST_VARS['perform'])) ? $HTTP_POST_VARS['perform'] : $HTTP_GET_VARS['perform'];
		$id = (isset($HTTP_POST_VARS['id'])) ? $HTTP_POST_VARS['id'] : $HTTP_GET_VARS['id'];
		$mailaddy = (isset($HTTP_POST_VARS['mailaddy'])) ? $HTTP_POST_VARS['mailaddy'] : $HTTP_GET_VARS['mailaddy'];



		if ( $cancel )
		{
			redirect(append_sid("{$phpbb_root_path}blog.$phpEx?b=" . $b . "", true));
		}

		if ( !$confirm )
		{

			//
			// Confirm deletion
			//
			$s_hidden_fields = '<input type="hidden" name="perform" value="' . $perform . '" /><input type="hidden" name="id" value="' . $id . '" /><input type="hidden" name="b" value="' . $b . '" />';
			$l_confirm = $user->lang['Blog_Enter_MailAddy'];

			//
			// Output confirmation page
			//
      $page_title = $user->lang['Blog_Enter_MailAddy'];

      $gen_simple_header = TRUE;

      page_header($page_title);

			$template->set_filenames(array(
				'confirm_body' => 'blog_sendblog_body.html')
			);


			$template->assign_vars(array(
				'MESSAGE_TITLE' => $user->lang['Information'],
				'MESSAGE_TEXT' => $l_confirm,

				'L_YES' => $user->lang['YES'],
				'L_NO' => $user->lang['NO'],

				'S_CONFIRM_ACTION' => append_sid("{$phpbb_root_path}blog.$phpEx?b=" . $b),
				'S_HIDDEN_FIELDS' => $s_hidden_fields)
			);

			//$template->pparse('confirm_body');

			page_footer();

		}

		else
		
		{
//			unset($b);

		 $sql = "SELECT titel , text , bbcode_uid , bbcode_bitfield , flags FROM " . BLOG_TABLE . " WHERE id = $id LIMIT 1";
		 if( !($result = $db->sql_query($sql)) )
		 {
			message_die(GENERAL_ERROR, "Could not select the blog text.", '', __LINE__, __FILE__, $sql);
		 }

		 while( $row = $db->sql_fetchrow($result) )
		 {
			$text = generate_text_for_display($row['text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['flags']);
			//$text = preg_replace('/\:(([a-z0-9]:)?)' . $row['bbcode_uid'] . '/s', '', $row['text']/*$text*/);
			$text = str_replace("<br />", "", $text/*$row["text"]*/);
      $titel = $row['titel'];
		 }


		      if ( $config['onentry_sendmail'] )
			      {
              $domain = ($config['server_name']);
              $to  = $mailaddy;
              $subject = $user->lang['Mail_Subject2'];
              $content = $user->lang['Mail_Content2'] . "\n\n" . 'Submitted by: ' .  $user->data['username'] . "\n\n" . "$titel" . "\n\n Link: \n http://" . "$domain" . "/blog.$phpEx?b=" . $b . "&id=" . $id . "\n\n";


              mail($to, $subject, $content, "From:" . $config['board_email']);
			      }

			//
			// Check privileges
			//
			if(defined('STAFF') /*$userdata['user_level'] == ADMIN */)
			{

			$permit_com_yes = "checked=\"checked\"";
			$permit_com_no = "";
			$l_add_blog = $user->lang['ADD_BLOG'];
			$l_submit = $user->lang['SUBMIT'];
			$l_titel = $user->lang['BLOG_ENTRY_TITEL'];
			$s_hidden_field = '<input type="hidden" name="perform" value="add" />';

			}

		}
		break;
	case 'Quickedit':

		//
		// Check privileges
		//
		if(!defined('STAFF') )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog.$phpEx") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);
      //die($user->lang['USER_CANNOT_EDIT']);
		}

  if ($contents == 'cancel')
{
		$sql = "SELECT text, bbcode_uid, bbcode_bitfield, flags FROM " . BLOG_TABLE . " WHERE id = $post_id LIMIT 1";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
//	$row['bbcode_options'] = (($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) + (($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + (($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
	$text = generate_text_for_display($row['text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']);

	$template->assign_vars(array(
		'SEND_TEXT'	=> true,
		'TEXT'		=> $text,
		'EDIT_IMG' 	=> $user->img('icon_post_edit', 'EDIT_POST'),
		'DELETE_IMG'=> $user->img('icon_post_delete', 'DELETE_POST'),
	));
}
else if ($submit)
{

	$text = utf8_normalize_nfc(request_var('contents', '', true));
	$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
	$allow_bbcode = $allow_urls = $allow_smilies = true;
	generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

	$sql_ary = array(
		'text'			=> $post_text,
		'bbcode_uid'		=> $uid,
		'bbcode_bitfield'   => $bitfield,
	);

	$sql = 'UPDATE ' . BLOG_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE id = $post_id";
	$db->sql_query($sql);
	
	$text = generate_text_for_display($sql_ary['text'], $sql_ary['bbcode_uid'], $sql_ary['bbcode_bitfield'], 7);
	
	$template->assign_vars(array(
		'SEND_TEXT'	=> true,
		'TEXT'		=> $text,
		//'EDIT_IMG' 	=> $user->img('icon_post_edit', 'EDIT_POST'),
		//'DELETE_IMG'=> $user->img('icon_post_delete', 'DELETE_POST'),
	));

}
else if ($post_id)
{

		$sql = "SELECT text, bbcode_uid, bbcode_bitfield, flags FROM " . BLOG_TABLE . " WHERE id = $post_id LIMIT 1";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

	decode_message($row['text'], $row['bbcode_uid']);
	
	$template->assign_vars(array(
		'EDIT_FORM'	=> true,
		'POST_ID'	=> $post_id,
		'POST_TEXT'	=> $row['post_text'],
		//'EDIT_IMG' 	=> $user->img('icon_post_edit', 'EDIT_POST'),
		//'DELETE_IMG'=> $user->img('icon_post_delete', 'DELETE_POST'),
	));
}
else
{
	trigger_error('USER_CANNOT_EDIT');
}

$template->set_filenames(array(
	'body' => 'blog_quickedit.html')
);
page_footer();
die();		
		break;
	default:



		//
		// Check privileges
		//
		if(defined('STAFF') || defined('STAFF2')/*$userdata['user_level'] == ADMIN*/ )
		{
			$permit_com_yes = "checked=\"checked\"";
			$permit_com_no = "";
			$l_add_blog	= $user->lang['ADD_BLOG'];
			$l_submit	= $user->lang['SUBMIT'];
			$l_titel	= $user->lang['BLOG_ENTRY_TITEL'];
			$s_hidden_field = '<input type="hidden" name="perform" value="add" />';
		}


}



	//
	// Check privileges
	//
	if(defined('STAFF') || defined('STAFF2') /*$userdata['user_level'] == ADMIN*/ )
	{
		$template->assign_block_vars("blog_admin", array(
      'L_ADD_BLOG' => $l_add_blog,
			'L_SUBMIT' => $l_submit,
			'L_TITEL' => $l_titel,

			'TEXT' => ( $l_text_field ) ? $l_text_field : "",
			'TITEL' => ( $l_titel_field ) ? $l_titel_field : "",

			'CAT_LIST'	=> make_cat_list($l_cat_list),

      'PERMIT_COM_YES' => $permit_com_yes,
			'PERMIT_COM_NO' => $permit_com_no,
			'L_PERMIT_COM' => $user->lang['PERMIT_COM'],
			'L_YES' => $user->lang['YES'],
			'L_NO' => $user->lang['NO'], 	

			'S_HIDDEN_FIELD' => $s_hidden_field
			));
	}



// smilies

	if(defined('STAFF') || defined('STAFF2') )
	{

	$sql = "SELECT code, emotion, smiley_url   
		FROM " . SMILIES_TABLE . " 
		ORDER BY smiley_id";
	if ($result = $db->sql_query($sql))
	{
		$num_smilies = 0;
		$rowset = array();
		while ($row = $db->sql_fetchrow($result))
		{
			if (empty($rowset[$row['smiley_url']]))
			{
				$rowset[$row['smiley_url']]['code'] = str_replace("'", "\\'", str_replace('\\', '\\\\', $row['code']));
				$rowset[$row['smiley_url']]['emotion'] = $row['emotion'];
				$num_smilies++;
			}
		}
		
      	$db->sql_freeresult($result);
      	
		if ($num_smilies)
		{
			$smilies_count = min(19, $num_smilies);
			$smilies_split_row = intval($blog_config['smilies_column']) - 1;

			$s_colspan = 0;
			$row = 0;
			$col = 0;

			while (list($smile_url, $data) = @each($rowset))
			{
				if (!$col)
				{
					$template->assign_block_vars('blog_admin.smilies_row', array());
				}

				$template->assign_block_vars('blog_admin.smilies_row.smilies_col', array(
					'SMILEY_CODE' => $data['code'],
					'SMILEY_IMG' => $config['smilies_path'] . '/' . $smile_url,
					'SMILEY_DESC' => $data['emotion'])
				);

				$s_colspan = max($s_colspan, $col + 1);

				if ($col == $smilies_split_row)
				{
					if ($row == intval($blog_config['smilies_row']) - 1)
					{
						break;
					}
					$col = 0;
					$row++;
				}
				else
				{
					$col++;
				}
			}



			$template->assign_vars(array(
				'L_SMILIES' => $user->lang['SMILIES'], 
        'L_MORE_SMILIES' => $user->lang['MORE_SMILIES'],
      	'U_ALL_SMILIES' => append_sid("{$phpbb_root_path}posting.$phpEx?mode=smilies"),	
				'S_SMILIES_COLSPAN' => $s_colspan)
			);
		}
	}

}
// end smilies



$sql_data = "SELECT DISTINCT FROM_UNIXTIME(date, '%m%Y') AS blog FROM " . BLOG_TABLE . " ORDER by date DESC";
if( !($result_data = $db->sql_query($sql_data)) )
{
	message_die(GENERAL_ERROR, "Could not select the blog events", '', __LINE__, __FILE__, $sql_data);
}

while( $row_data = $db->sql_fetchrow($result_data) )
{

	switch ( substr($row_data['blog'], 0, 2) )
	{
		case 1: $mounths_name = $user->lang['datetime']['January']; break;
		case 2: $mounths_name = $user->lang['datetime']['February']; break;
		case 3: $mounths_name = $user->lang['datetime']['March']; break;
		case 4: $mounths_name = $user->lang['datetime']['April']; break;
		case 5: $mounths_name = $user->lang['datetime']['May']; break;
		case 6: $mounths_name = $user->lang['datetime']['June']; break;
		case 7: $mounths_name = $user->lang['datetime']['July']; break;
		case 8: $mounths_name = $user->lang['datetime']['August']; break;
		case 9: $mounths_name = $user->lang['datetime']['September']; break;
		case 10: $mounths_name = $user->lang['datetime']['October']; break;
		case 11: $mounths_name = $user->lang['datetime']['November']; break;
		case 12: $mounths_name = $user->lang['datetime']['December']; break;
	}

	$template->assign_block_vars("mounths_list", array(
		'MOUNTHS' => $row_data['blog'],
		'MOUNTHS_NAME' => $mounths_name . ", " . substr($row_data['blog'], -4)
		)
	);
}



/**
* Generates the left side menu
*
* @param int $user_id The user_id of the user whom we are building the menu for
*/
if($blog_config['new_archiv_menu'])
{


	// archive menu
	// Last Month's ID(set to 0 now, will be updated in the loop)
	$last_mon = 0;

	$archive_rows = array();



	$sql = "SELECT * FROM " . BLOG_TABLE . " ORDER by date DESC";

	$result = $db->sql_query($sql);

	while($row = $db->sql_fetchrow($result))
	{
		$date = getdate($row['date']);

		// If we are starting a new month
		if ($date['mon'] != $last_mon)
		{
			$archive_row = array(
				'MONTH'			=> $date['month'],
				'YEAR'			=> $date['year'],

				'monthrow'		=> array(),
			);

			$archive_rows[] = $archive_row;
		}

		if ( !$row['titel'] )
		{
			$row['titel'] = "No Titel Given";
		}

		$archive_row_month = array(
			'TITLE'			=> substr(censor_text($row['titel']), 0, 25) . '...',
			'U_VIEW'		=> append_sid("{$phpbb_root_path}blog.$phpEx", 'id=' . $row['id']),
			'DATE'			=> $user->format_date($row['date']),
		);

		$archive_rows[count($archive_rows) - 1]['monthrow'][] = $archive_row_month;

		// set the last month variable as the current month
		$last_mon = $date['mon'];
	}
	$db->sql_freeresult($result);


	//$cache_data = $archive_rows;

	if (count($archive_rows))
	{
		foreach($archive_rows as $row)
		{
		$template->assign_block_vars('archiverow', $row);
		}
	}

	// output some data
	$template->assign_vars(array(
		// are there any archives?
		//'S_ARCHIVES'	=> (count($archive_rows)) ? true : false,
		'S_ARCHIVES'	=> true,
	));

}


/**
* Generates the left side menu
*
* 
*/
if($blog_config['cal_archiv_menu'])
{



if(isset($_GET['b']))
{
$jahrneu  = intval( substr( $HTTP_GET_VARS['b'], 2, 4 ) );
$mo = $monat    =  /*intval(*/ substr( $HTTP_GET_VARS['b'], 0, 2 ) /*)*/;
}

if(isset($_GET['ta']))
{

$ta =  substr( $HTTP_GET_VARS['b'], 0, 2 ) ;

}
else
{
$ta = '';
}

 $zeit = time(); 
 $datum = getdate($zeit); 
 $tag = $datum['mday']; 
 $jahr = $datum['year']; 
 if(!empty($jahrneu))
 {$jahr = $jahrneu;}
 $dieser_monat=$datum['mon'];
 
 if(!isset($monat)) 
 { $monat=$datum['mon']; }
 //$monat_text = array("null","Januar","Februar","M&auml;rz","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
 $monat_text = array("null",$user->lang['datetime']['January'],$user->lang['datetime']['February'],$user->lang['datetime']['March'],$user->lang['datetime']['April'],$user->lang['datetime']['May'],$user->lang['datetime']['June'], $user->lang['datetime']['July'], $user->lang['datetime']['August'], $user->lang['datetime']['September'], $user->lang['datetime']['October'], $user->lang['datetime']['November'], $user->lang['datetime']['December']);



 $schaltjahr = gettype($jahr/4);
 if($schaltjahr=="integer") {
    $monat_tage = array(0,31,29,31,30,31,30,31,31,30,31,30,31);
    } 
 else {
    $monat_tage = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
    }
 $start = getdate(mktime(0,0,0,$monat,1,$jahr)); 
 $beginn = $start['wday']; 
 if($beginn==0) 
 { $beginn=7; }

 if($monat>1) { $l_last_month = ($monat-1).$jahr; }
 if($monat==1) { $l_last_month = "12".($jahr-1); }


 if($monat<12) { $l_next_month = ($monat+1).$jahr; }
 if($monat==12) { $l_next_month = "01".($jahr+1); }


$sql_data = "SELECT DISTINCT FROM_UNIXTIME(date, '%d%m%Y') AS bdate FROM " . BLOG_TABLE . " ORDER by date DESC";
if( !($result_data = $db->sql_query($sql_data)) )
{
	message_die(GENERAL_ERROR, "Could not select the blog events", '', __LINE__, __FILE__, $sql_data);
}

	$num_entrys = 0;
	$rowset = array();
	while ($row = $db->sql_fetchrow($result_data))
	{
		if (empty($rowset[$row['bdate']]))
		{
			$rowset[$row['bdate']] = "1";
			$num_entrys++;
			
		}
	}
	$db->sql_freeresult($result_data);

 $zeile = 1; 
 $spalte = 1; 
 $tagnummer = 1; 



$l_rest_cal = '';

for($y=1;$y<($monat_tage[$monat]+$beginn);$y++) 
 {

 $found = "0";
 $fafound = "0";

include($phpbb_root_path . "includes/phpBBlog/feiertage.php");
//$fa = $tagnummer.$monat.$jahr;
if(!empty($rowset[$tagnummer.$monat.$jahr]))
{
$fafound = "1";
}
	if($y<$beginn) 
	{ 
		$l_rest_cal .= "<td width=\"20\" class=\"tage\" align=\"center\">&nbsp;</td>"; 
	}
	else if($tagnummer==$tag && $monat==$dieser_monat && $wd != "6" && $wd != "0" && $found != "1") 
	{	
		if($fafound == "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"eintrag\" align=\"center\">";
			$l_rest_cal .= "<a href=\"index.php?ta=$tagnummer&b=$monat$jahr\">$tagnummer</a>";
		}
		if($fafound != "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"heute\" align=\"center\">";
			$l_rest_cal .= "$tagnummer";
		}
		$l_rest_cal .= "</td>"; 
		$tagnummer++;
		unset($found);
		unset($fafound);
        } 
	else if($tagnummer==$ta && $monat==$mo && $wd != "6" && $wd != "0" && $found != "1") 
	{
		if($fafound == "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"eintrag\" align=\"center\">";
			$l_rest_cal .= "<a href=\"index.php?ta=$tagnummer&jahrneu=$jahr&monat=$monat\">$tagnummer</a>";
		}
		if($fafound != "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"aktuell\" align=\"center\">";
			$l_rest_cal .= "$tagnummer";
		}
		$l_rest_cal .= "</td>";
		$tagnummer++;
		unset($found);
		unset($fafound);
        } 
	else if($wd == "0") 
	{
		if($fafound == "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"feiertageintrag\" align=\"center\">";
			$l_rest_cal .= "<a href=\"index.php?ta=$tagnummer&jahrneu=$jahr&monat=$monat\">$tagnummer</a>";
		}
		if($fafound != "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"feiertag\" align=\"center\">";
			$l_rest_cal .= "$tagnummer";
		}
		$l_rest_cal .= "</td><td width=\"20\" class=\"kw\" align=\"center\">$kw</td>";
		$tagnummer++; 
		unset($found);
		unset($ft);
		unset($fafound);
	}
	else if($wd == "6" or $found == "1" and $wd!="0") 
	{
		if($fafound == "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"feiertageintrag\" align=\"center\">";
			$l_rest_cal .= "<a href=\"index.php?ta=$tagnummer&jahrneu=$jahr&monat=$monat\">$tagnummer</a>";
		}
		if($fafound != "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"feiertag\" align=\"center\">";
			$l_rest_cal .= "$tagnummer";
		}
		$l_rest_cal .= "</td>";
		$tagnummer++; 
		unset($found);
		unset($ft);
		unset($fafound);
        }
	else 
	{ 
		if($fafound == "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"eintrag\" align=\"center\">";
			$l_rest_cal .= "<a href=\"index.php?ta=$tagnummer&jahrneu=$jahr&monat=$monat\">$tagnummer</a>";
		}
		if($fafound != "1")
		{
			$l_rest_cal .= "<td width=\"20\" class=\"tage\" align=\"center\">";
			$l_rest_cal .= "$tagnummer";
		}
		$l_rest_cal .= "</td>";
		$tagnummer++;
		unset($fafound);
	}
		
	$temp = gettype($spalte/7);
     	if($temp=="integer" && $y<($monat_tage[$monat]+$beginn-1)) 
	 { $l_rest_cal .= "</tr>\n<tr>"; $zeile++; }
     $spalte++;
 }



 $ende = $zeile * 7; 
 $rest = ($ende - $spalte) + 1; 
 if($rest>=7) 
 { $rest=0; }
 for($r=0;$r<$rest;$r++) 
 { $l_rest_cal .= "<td width=\"20\" class=\"tage\" align=\"center\">&nbsp;</td>
 "; }
 if($rest != 0)
 {
 $l_rest_cal .= "<td width=\"20\" class=\"kw\" align=\"center\">$kw</td>";
 }



	$template->assign_vars(array(
		'L_LAST_MONTH' => $l_last_month,
		'L_THIS_DAY' => $tag,
    'L_THIS_MONTH' => $monat_text[$monat] . $jahr,
		'L_THIS_MY_URI' => $dieser_monat . $datum['year'],
		'L_NEXT_MONTH' => $l_next_month,

		'L_REST_CAL'	=> $l_rest_cal
		));


	// output some data
	$template->assign_vars(array(
		// are there any archives?
		//'S_ARCHIVES'	=> (count($archive_rows)) ? true : false,
		'S_CALENDAR'	=> true,
	));

}



if($blog_config['allow_cat'])
{

$cat_option = '';
$sql = 'SELECT cat_id, cat_titel 
	FROM ' . BLOG_CAT_TABLE . ' 
	ORDER BY cat_id';
$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result))
{

	//$lexicon_categories[$row['cat_id']] = @$lang[$row['cat_titel']];
	//$lexicon_categories[$row['cat_id']] = @$user->lang[utf8_strtoupper(chr($row['cat_titel']))];
  $categorie_titel = @$user->lang[utf8_strtoupper($row['cat_titel'])];
	// take db entry if not exist a lang entry
	if ($categorie_titel == '')
	{
		$categorie_titel = $row['cat_titel'];
	}

	      $template->assign_block_vars("cats_list", array(
		      'CATS' => $row['cat_id'],
		      'CATS_NAME' => /*$row['cat_titel']*/$categorie_titel
		      )
	       );
}
$db->sql_freeresult($result);

}




if(isset($_POST['b']))
{
//  WHY WAS THIS LINES HERE SKIPPY
$i = 0;
$b = "";
while ( $i <= 5 )
{
	$b .= intval( substr( $HTTP_GET_VARS['b'], $i, 1 ) );
        //$b .=  substr((int) request_var('b', ''), $i, 1 );
$i++;
}
}

$last_date = '';

//Selected Categorie
if ($cats != "" && $blog_config['allow_cat'])
{
  $sql = "SELECT * FROM " . BLOG_TABLE . " WHERE category_id = '" . $cats . "' ORDER by date DESC";
}
//Only one entry
else if ($id)
{
  $sql = "SELECT * FROM " . BLOG_TABLE . " WHERE id = '" . $id . "' LIMIT 1";
}
//Selected Month
else if ($b != 000000)
{
	$sql = "SELECT * FROM " . BLOG_TABLE . " WHERE FROM_UNIXTIME(date, '%m%Y') = '" . $b . "' ORDER by date DESC";
}
//Last Month
else
{
	$sql_last = "SELECT date FROM " . BLOG_TABLE . " ORDER by date DESC LIMIT 1";
	if( !($result_last = $db->sql_query($sql_last)) )
	{
		message_die(GENERAL_ERROR, "Could not select the blog events", '', __LINE__, __FILE__, $sql_last);
	}

	while( $row_last = $db->sql_fetchrow($result_last) )
	{
		$last_date = $row_last["date"];
	}

	$sql = "SELECT * FROM " . BLOG_TABLE . " WHERE FROM_UNIXTIME(date, '%m%Y') = '" . date( 'mY', $last_date ) . "' ORDER by date DESC";
}

if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "Could not select the blog events", '', __LINE__, __FILE__, $sql);
}

$total_blog = 0;
while( $row = $db->sql_fetchrow($result) )
{

// to update the read count, we are only doing this if the user is not the owner or a bot, and the user doesn't view the shortened version, and we are not viewing the deleted blogs page
if ($user->data['user_id'] != $row['user_id'] && $id && $total_blog == 0 && !$user->data['is_bot'])
{
	$sqlu = 'UPDATE ' . BLOG_TABLE . " SET blog_read_count = blog_read_count + 1 WHERE id = $id ";
	$db->sql_query($sqlu);
}

	$id = $row['id'];
	$user_id = $row['user_id'];
	$date = $row['date'];



  if ($b == 000000)
  {
   //$b = date( 'mY', $last_date );
   $b = ( $last_date ) ? date( 'mY', $last_date ) : date( 'mY', $date );
  }

  if ($blog_config['allow_cat'])
  {

   // Select categorie data
   $sql2 = 'SELECT cat_titel
	 FROM ' . BLOG_CAT_TABLE . "	
	 WHERE cat_id = " . $row['category_id'] . " LIMIT 1";
	 if( !($result2 = $db->sql_query($sql2)) )
	 {
		message_die(GENERAL_ERROR, "Could not select the blog events", '', __LINE__, __FILE__, $sql_last);
	 }

	 while( $row_2 = $db->sql_fetchrow($result2) )
	 {

		$categories_titel = @$user->lang[utf8_strtoupper(chr($row_2["cat_titel"]))];
		// take db entry if not exist a lang entry
		if ($categories_titel == '')
		{
			$categories_titel = $row_2["cat_titel"];
		}
    $categorie_titel = @$user->lang[utf8_strtoupper($row_2['cat_titel'])];
	  // take db entry if not exist a lang entry
	  if ($categorie_titel == '')
	  {
		  $categorie_titel = $row_2["cat_titel"];
	  }

	 }
   $db->sql_freeresult($result2);

   $titel = '<a href="' . append_sid("{$phpbb_root_path}blog.$phpEx?b=$b&id=$id") . '">' . $categorie_titel . ':&nbsp;&nbsp;' . $row['titel'] . '</a>';
  }
  else
  {
   $titel = '<a href="' . append_sid("{$phpbb_root_path}blog.$phpEx?b=$b&id=$id") . '">' . $row['titel'] . '</a>'; 
  }
/*
  //parse message for display
  $text = $row['text'];
  $bbcode_bitfield = '';
  $bbcode_bitfield = $bbcode_bitfield | base64_decode($row['bbcode_bitfield']);
  if ($bbcode_bitfield !== '')
   {
	$bbcode = new bbcode(base64_encode($bbcode_bitfield));
   }
  $text = censor_text($text);
  if ($row['bbcode_bitfield'])
   {
	$bbcode->bbcode_second_pass($text, $row['bbcode_uid'], $row['bbcode_bitfield']);
   }
  $text = str_replace("\n", '<br />', $text);
  $text = smiley_text($text);*/

  $text = generate_text_for_display($row['text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['flags']);
 


	$username = "";
	$sql_user = "SELECT username FROM " . USERS_TABLE . " WHERE  user_id = $user_id AND user_id <> " . ANONYMOUS . " LIMIT 1";
	if( !($result_user = $db->sql_query($sql_user)) )
	{
		message_die(GENERAL_ERROR, "Could not select username", '', __LINE__, __FILE__, $sql);
	}
	while( $row_user = $db->sql_fetchrow($result_user) )
	{
		$username = $row_user["username"];
	}



	/*$sql_com = "SELECT count(*) as comcount FROM " . BLOG_COM_TABLE . " WHERE id_blog = '" . $id . "'"; 
	if( !($result_com = $db->sql_query($sql_com)) )
	{
		message_die(GENERAL_ERROR, 'Could not count blog comments!', '', __LINE__, __FILE__, $sql);
	}
	$row_com = $db->sql_fetchrow($result_com);
	$com = $row_com['comcount'];*/

	$sql_com = "SELECT blog_com_count, blog_read_count, blog_tb_count FROM " . BLOG_TABLE . " WHERE id = '" . $id . "'"; 
	if( !($result_com = $db->sql_query($sql_com)) )
	{
		message_die(GENERAL_ERROR, 'Could not count blog comments!', '', __LINE__, __FILE__, $sql);
	}
	$row_com = $db->sql_fetchrow($result_com);
  $com = $row_com['blog_com_count'];
  $view_count = $row_com['blog_read_count'];
  $tb_count = $row_com['blog_tb_count'];

  $tmp_com = $user->lang['BLOG_COMMENTS'];
  $tmp_tb = $user->lang['BLOG_TRACKBACKS'];
	if( $com == 1 )
	{
    $tmp_com = $user->lang['BLOG_COMMENT'];
    $tmp_tb = $user->lang['BLOG_TRACKBACK'];
  }
	
	$row['permit_com']  ? $comments = $tmp_com . " (" . $com . ")" : $comments = $user->lang['BLOG_COMMENTS_DEAKTIVATED'] . " (" . $com . ")";
	$row['permit_tb']  ? $comments .= $tmp_tb . " (" . $com . ")" : $comments = $user->lang['BLOG_COMMENTS_DEAKTIVATED'] . " (" . $com . ")";
	$comments .= " views (" . $row['blog_read_count'] . ")";


	$row_class = ( !($total_blog % 2) ) ? 'post bg1' : 'post bg2';
	//
	// Check privileges
	//
	if(defined('STAFF') /*$userdata['user_level'] == ADMIN*/ )
	{
		$admin_command = '| <a href="javascript:bMod(\'' . $id . '\')">' . $user->lang['BLOG_MODIFY'] . '</a> | <a href="#" id="quick_edit'.$id.'" onclick="quick_edit(\''.$id.'\'); return false;">' . $user->lang['BLOG_QUICKEDIT'] . '</a> | <a href="javascript:bDel(\'' . $id . '\')">' . $user->lang['BLOG_DELETE'] . '</a>';
	}
	else
  {
		$admin_command = '';
	}

	$template->assign_block_vars("blog", array(
		'USERNAME' => $username,
		'DATE' => date('l, F d, Y', $date),//create_date( 'l, F d, Y', $date, $config['board_timezone'] ),
		'TEXT' => $text,
		'TITEL' => $titel,
		'TIME' => date('H:i', $date),//create_date( 'H:i', $date, $config['board_timezone'] ),
		'ROW_CLASS' => $row_class,

    'QUICK_ID' =>  'id="postdiv'.$id.'"',

		'L_FROM' => $user->lang['FROM'],

		'L_COMMENTS' => $comments,
		'L_SENDBLOG' => $user->lang['SEND_AS_MAIL'],

		'U_ADMIN_COMMAND' => $admin_command,
		'U_COMMENTS' => $id
		)
	);

$total_blog++;
}

if( !$total_blog)
{
	//redirect(append_sid("{$phpbb_root_path}blog.$phpEx", true));
}
if( $id && $total_blog == 1 )
{
	$template->assign_block_vars("full_view", array(
		'SOURCE_LINK' => $user->lang['BLOG_SOURCE'] . $row['quelle'],
		'TRACKBACK_URL' $user->lang['BLOG_TB_URL'] . append_sid("{$phpbb_root_path}blog_trackback.$phpEx?mode=trackback&id=" . $id . ""),
		'RSS_URL' => $user->lang['BLOG_RSS_URL'] . append_sid("{$phpbb_root_path}blog_rss.$phpEx?blogid=" . $id . "")
		)
	);
}
//
// Lets build a page ...
//
// Header und Titel der Seite
page_header($user->lang['BLOG']);
//page_header('Blogs');

$template->set_filenames(array(
	'body' => 'blog_body.html')
);

  $b = (int) request_var('b', '');
  if ($b == '')
  {
   $b = date( 'mY', $last_date );
  }



$template->assign_vars(array(
	'BLOG' => $b,

	'BLOG_AUTHOR' => '<a href="' . PHPBBLOG_SITE_AUTHOR . '">' . PHPBBLOG_AUTHOR . '</a>',
	'BLOG_VERSION' => $blog_config['version'], 
 
	'BLOG_CHECK_VERSION' =>  (defined('STAFF') /*$userdata['user_level'] == ADMIN*/ ) ? '<a href="' . PHPBBLOG_CHECK_NEW_VERSION . '">' . $user->lang['CHECK_NEW_VERSION'] . '</a>':'',
	'L_BLOG' => $user->lang['BLOG_NAME'],
	'L_BLOG_ARCHIVES' => $user->lang['BLOG_ARCHIVES'],
	'L_BLOG_CATEGORIES' => ( $blog_config['allow_cat'] ) ? $user->lang['BLOG_CATEGORIES'] : "",

  'U_QUICKEDIT' 		=> append_sid("{$phpbb_root_path}blog.$phpEx"),
	'U_FORM_ACTION' => append_sid("{$phpbb_root_path}blog.$phpEx?b=" . $b . "")
	)
);



// Footer
page_footer();
?>

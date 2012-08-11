<?php
/**
*
* @package phpBB3
* @version $Id: trackback.php,v 1.2.1 2008/02/18 15:35:25 Skippy Exp $
* @copyright  (C) 2008 Sebastian Schmidt - Skippy
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
include($phpbb_root_path . 'includes/phpBBlog/blog_trackback.'.$phpEx);
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

$blog_config = array();
$phpbblog_c = new phpbblog();
$blog_config = $phpbblog_c->blog_config();


if ($blog_config['allow_trackbacks']) {
	die('Trackbacks are not enabled on this blog.');
}



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







// pingback or trackback?
$method = (isset($HTTP_RAW_POST_DATA) && strpos($HTTP_RAW_POST_DATA, 'pingback.ping') !== false) ? 'pingback' : 'trackback';
$perform    = request_var('mode', '');


$permit_tb = '';
$sql = "SELECT permit_tb FROM " . BLOG_TABLE . " WHERE id = $tb_id LIMIT 1";
if( $result = $db->sql_query($sql) )
{
		while( $row = $db->sql_fetchrow($result) )
		{
            $permit_tb = $row['permit_tb'];
		}
}

switch($perform)
{

	case 'add':
		break;
	case 'pingback':
/*	
	// some checks:
if (!$type) {
   response("400 Bad Request", -32300, "invalid request: pinged item must be specified in URI");
}
if (!$blog->get_item($type, "id=$id")) {
   response("200 OK", 33, "target does not exist");
}
$item_prop = $method.'_item';
if (!isset($blog->$item_prop)) {
   response("200 OK", 0, "$method is not properly set up here");
}
if ($method == 'pingback') {
   if (!preg_match("/<methodName>\s*pingback.ping\s*<\/methodName>.+<string>\s*(.+)\s*<\/string>.+<string>\s*(.+)\s*<\/string>/sU", $HTTP_RAW_POST_DATA, $matches)) {
      response("200 OK", 0, "request does not look like a valid pingback request");
   }
   $url = $matches[1];
}

if ($blog->num_items($blog->trackback_item, "{$type}_id=$id&{$blog->tb_field['url']}=$url") != 0) {
   response("200 OK", 48, "$method ping already registered");
}
$http = new HTTP();
$result = $http->get($url);
if (!strpos($result->content, $blog->url)) {
   response("200 OK", 17, "can't find link to {$blog->url} on $url");
}

// create item:
$it = new Item($blog->$item_prop);
$it->set($type.'_id', $id);
if ($method == 'trackback') {
   foreach ($_REQUEST as $key => $value) {
      if (isset($blog->tb_field[$key])) $it->set($blog->tb_field[$key], stripslashes($value));
   }
   if (isset($blog->tb_auto)) {
      foreach ($blog->tb_auto as $key => $value) $it->set($key, $value);
   }
}
else {
   if (isset($blog->pb_field['url'])) $it->set($blog->pb_field['url'], $url);
   if (isset($blog->pb_field['title']) && preg_match("/<title>\s*([^<]+)\s*<\/title>/s", $result->content, $matches)) {
      $it->set($blog->pb_field['title'], $matches[1]);
   }
   if (isset($blog->pb_auto)) {
      foreach ($blog->pb_auto as $key => $value) $it->set($key, $value);
   }
}
if ($blog->save($it)) {
   response("200 OK", "", "OK");
}
response("200 OK", 0, "server error: could not save ping");
*/



		break;
	case 'trackback':
      /*
      Process a trackback submission.
      */
      if (isset($_POST['url'])) {

        $tb_id = (int) request_var('id', '');
        if ( !$tb_id) trackback_response(1, 'I really need an ID for this to work.');

	      $tb_url = $_POST['url'];
	
	      $tb_blog_name = $_POST['blog_name'];
        $tb_blog_name = (strlen($tb_blog_name) > 255) ? substr($tb_blog_name, 0, 252).'...' : $tb_blog_name;
	
        $tb_title = $_POST['title'];
        $tb_title = (strlen($tb_title) > 255) ? substr($tb_title, 0, 252).'...' : $tb_title;
	
        $tb_exerpt = $_POST['exerpt'];
        $tb_exerpt = (strlen($tb_expert) > 255) ? substr($tb_expert, 0, 252).'...' : $tb_exerpt;

        if ( empty($title) && empty($blog_name) && empty($exerpt) ) {
          // If it doesn't look like a trackback at all...
          trackback_response(1, 'U need title blog_name and description for this to work.');
        }

        $tb_approved = '1';
        if ( $blog_config['url_same_ips'] ) {


	      //get domain from url
	      //eregi('^(http://)([^/]+)/?(.*)?$', $tb_url, $domain);
	      //$command = 'host ' . $domain[2];
	      $command = 'host ' . parse_url($tb_url, '2');
	      $ar_host_addr = Array();
	      //lookup ip for domain
	      exec($command, $ar_return);
	      //parse result of lookup
	      for ($i=0; $i<count($ar_return); $i++)
	      {
		      $tmp = $ar_return[$i];
		      $tmp = explode(chr(32), $tmp);
		      if ('Host' == $tmp[0])
		      {
			       break;
		      }
		      else
		      {
			       $ar_host_addr[] = $tmp[count($tmp)-1];
		      }
	      } //end for
			
	      // host found and matches remote_addr
	      if ( count($ar_host_addr) == 0 or in_array(getIP(), $ar_host_addr) == 0 ) {	
		    // Comment needs to be moderated
		      $tb_approved = '0';

		      if ( $blog_config['modentry_sendmail'] )
			      {
				      $domain = ($config['server_name']);
				      $to  = $config['board_email'];
				      $subject = $user->lang['MAIL_SUBJECT'];
				      $content = $user->lang['MAIL_CONTENT'] . "\n\n" . 'Submitted by: ' .  $user->data['username'] . "\n\n" . "$comments";

				      mail($to, $subject, $content, "From:" . $to);
			      }
          trackback_response(1, 'Youre IP don´t match the ip of youre blog, so a admin must proof your trackback first.');
	      }
        }



		      if( $permit_tb )
		      {
            $sql = 'INSERT INTO ' . BLOG_TRACKBACKS_TABLE . ' (tb_entry_ID, tb_title, tb_blog_name, tb_blog_url, tb_blog_IP, tb_date, tb_excerpt, tb_approved, tb_agent) 
            VALUES("' . $tb_id . '","' . $tb_title . '","' . $tb_blog_name . '","' . $tb_url . '","' . getIP() . '","' . time() . '","' . $tb_exerpt . '","' . $tb_approved . '","' . addslashes($_SERVER['HTTP_USER_AGENT']) . '")';

            if ($result = $db->sql_query($sql)) {
		          if ( $blog_config['ontrackback_sendmail'] )
			         {
				        $domain = ($config['server_name']);
				        $to  = $config['board_email'];
				        $subject = $user->lang['MAIL_SUBJECT'];
				        $content = $user->lang['MAIL_CONTENT'] . "\n\n" . 'Submitted by: ' .  $user->data['username'] . "\n\n" . "$comments";

				        mail($to, $subject, $content, "From:" . $to);
			         }

		          $sql = "UPDATE " . BLOG_TABLE . " SET blog_tb_count = blog_tb_count + '1' WHERE id = '$tb_id'";
		          $db->sql_query($sql)

              trackback_response(0, '');
              exit();
	          }
            else {
              trackback_response(1, 'There was an error, your trackback was not processed.');
              exit();
            }

  	      }
		      else
		      {
            trackback_response(1, 'Sorry, trackbacks are closed for this item.');
  	      }
  	    

	    }
      else {
        trackback_response(1, 'No URL included in TrackBack.');
      }

        trackback_response(1, 'An error ocurred your trackback was not processed');
		break;
	case 'send':
		//
		// Check privileges
		//
		if(!defined('STAFF') /*$userdata['user_level'] != ADMIN*/ )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog_trackback.$phpEx?id=$id") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);	
		}
    $tb_id = (int) request_var('id', '');
	  $tb_url = utf8_normalize_nfc(request_var('url', '', true));
	
	  $tb_blog_name = utf8_normalize_nfc(request_var('blog_name', '', true));
    $tb_blog_name = (strlen($tb_blog_name) > 255) ? substr($tb_blog_name, 0, 252).'...' : $tb_blog_name;
	  $tb_title =  utf8_normalize_nfc(request_var('title', '', true));
    $tb_title = (strlen($tb_title) > 255) ? substr($tb_title, 0, 252).'...' : $tb_title;
	  $tb_exerpt =  utf8_normalize_nfc(request_var('exerpt', '', true));
    $tb_exerpt = (strlen($tb_exerpt) > 255) ? substr($tb_exerpt, 0, 252).'...' : $tb_exerpt;

    $message = '';
    trackback_send(&$message, $tb_title, $tb_url, $blog_url, $tb_blog_name, $tb_exerpt);

		break;
	case 'delete':
		//
		// Check privileges
		//
		if(!defined('STAFF') /*$userdata['user_level'] != ADMIN*/ )
		{
			$message = $user->lang['BLOG_NOT_AUTHORIZED'] . ".<br /><br />" . 
			sprintf($user->lang['BLOG_CLICK_RETURN_BLOG'], "<a href=\"" . append_sid("{$phpbb_root_path}blog_comments.$phpEx?id=$id") . "\">", "</a>");
			trigger_error($message, E_USER_ERROR);	
		}

		break;
	default:
      $tb_id = (int) request_var('id', '');
	$sql = "SELECT * FROM " . BLOG_TRACKBACKS_TABLE . " WHERE tb_entry_ID = $tb_id  ORDER by tb_date DESC";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Could not select comments.", '', __LINE__, __FILE__, $sql);
	}

	$i=0;
	while( $row = $db->sql_fetchrow($result) )
	{
		$id_com = $row['id'];
		$username = "";



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

//
// Lets build a page ...
//
$page_title = $user->lang['BLOG_COM_TITLE'];

$gen_simple_header = TRUE;

page_header($page_title);

$template->set_filenames(array(
	'body' => 'blog_trackback.html')
);


$template->assign_vars(array(
	'L_COMMENTS' => ( $permit_tb ) ? ucfirst( $user->lang['BLOG_COMMENTS'] ) : ucfirst( $user->lang['BLOG_COMMENTS_DEACTIVATED'] ),
	'L_COMMENT' => $user->lang['BLOG_COM_COMMENT'],
	'L_CHARACTERS_REMAINING' => $user->lang['BLOG_COM_CHARS_REM'],
	'L_SUBMIT' => ( $permit_tb ) ? $user->lang['SUBMIT'] : $user->lang['BLOG_COMMENTS_DEAKTIVATET'],
  'S_HIDDEN_FIELDS' => $s_hidden_fields,
	'U_FORM_ACTION' => append_sid("blog_trackback.$phpEx?id=$id")
	)
);

page_footer();

}





?>


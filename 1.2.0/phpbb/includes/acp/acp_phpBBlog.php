<?php
/** 
*
* @package acp
* @version $Id: acp_lexikon.php V0.1.3 2007-08-30 23:54:18 tas2580 $
* @copyright (c) 2005 phpBB Group; 2006 phpBB.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package acp
*/

define('BLOG_TABLE', $table_prefix.'blog');
define('BLOG_COM_TABLE', $table_prefix.'blog_com');
define('BLOG_CAT_TABLE', $table_prefix.'blog_cat');
define('BLOG_CONFIG_TABLE', $table_prefix.'blog_config');


$user->add_lang('mods/phpBBlog/phpBBlog_acp');
$user->add_lang('mods/phpBBlog/common');
class acp_lexikon
{
	var $u_action;
	var $parent_id = 0;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;
		include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);

		$user->add_lang('acp/common');
		
		$this->tpl_name = 'acp_phpBBlog';
		$this->page_title = $user->lang['BLOG_ACP_SETTINGS'];
		$mode = request_var('mode', '');
		$submit	= (isset($_POST['post'])) ? true : false;
		$setup = false;
		$select_cat = false;
		$new_cat = false;
		$edit_cat = false;
		$delete_cat = false;
		$bt_group_dd = '';

		// Update
		switch ($mode)
		{
			case 'new_cat':
if ($mode)
{
					$new_cat = $cat_status = true;
					$error = $bug_cat_name = $message_parser->message = '';
					$cat_par_id = 0;
					if ($submit)
					{
						$message_parser = new parse_message();
						$message_parser->message = utf8_normalize_nfc(request_var('lexikon_cat_desc', '', true));
						if($message_parser->message)
						{
							$message_parser->parse(true, true, true, true, false, true, true, true);
						}

						$bug_cat_name	= utf8_normalize_nfc(request_var('bug_cat_name', '', true));
						//$cat_status		= utf8_normalize_nfc(request_var('bug_cat_status', '', true));
						//$cat_par_id		= utf8_normalize_nfc(request_var('bug_parent_cat_id', '', true));
						if(!utf8_clean_string($bug_cat_name))
						{
							$error .= $user->lang['CAT_NAME_EMPTY'];
						}
						if(!$error)
						{
							$sql = "INSERT INTO " . BLOG_CAT_TABLE . " (cat_titel, cat_desc) VALUES
								('" . $db->sql_escape($bug_cat_name) . "','" . $db->sql_escape($message_parser->message) . "')";
							$db->sql_query($sql);
							trigger_error($user->lang['NEW_CAT_ADDED'] . adm_back_link($this->u_action));
						}
					}
					$template->assign_vars(array(
							'ERROR_MSG'				=> $error,
							'BUG_CAT_NAME'			=> $bug_cat_name,
							'BUG_CAT_DESC'			=> $message_parser->message,
					));
}
				break;
			case 'new_entry':
					if ($mode)
					{
					$new_cat = $cat_status = true;
					$error = $keyword = $message_parser->message = '';
					$cat_par_id = 0;
					if ($submit)
					{
						$message_parser = new parse_message();
						$message_parser->message = utf8_normalize_nfc(request_var('lexikon_cat_desc', '', true));
						if($message_parser->message)
						{
							$message_parser->parse(true, true, true, true, false, true, true, true);
						}

						$keyword	= utf8_normalize_nfc(request_var('bug_cat_name', '', true));
						//$cat_status		= utf8_normalize_nfc(request_var('bug_cat_status', '', true));
						$cat_par_id		= utf8_normalize_nfc(request_var('bug_parent_cat_id', '', true));
						if(!utf8_clean_string($keyword))
						{
							$error .= $user->lang['ENTRY_KEYWORD_EMPTY'];
						}
						if(!$error)
						{
							$sql = "INSERT INTO " . BLOG_TABLE . " (user_id, keyword, expl_short, explanation, bbcode_uid, bbcode_bitfield, cat) VALUES
								('".$user->data['user_id']."', '" . $db->sql_escape($keyword) . "', 0, '" . $db->sql_escape($message_parser->message) . "', '" . $db->sql_escape($message_parser->bbcode_bitfield) . "', '" . $db->sql_escape($message_parser->bbcode_uid) . "', '$cat_par_id')";
							$db->sql_query($sql);
							trigger_error($user->lang['NEW_ENTRY_ADDED'] . adm_back_link($this->u_action));
						}
					}
					$cat_list = '';
					$cat_list .= '<option value="0" selected="selected">' . $user->lang['NO_CAT_SELECTED'] . '</option>';
					$sql2 = 'SELECT bc.*
						FROM ' . BLOG_CAT_TABLE . " bc
						ORDER BY bc.cat_id";
					$result2 = $db->sql_query($sql2);
					while ($row2 = $db->sql_fetchrow($result2))
					{
						$cat_list .= '<option value="' . $row2['cat_id'] . '">' . $row2['cat_titel'] . '</option>';
					}
					$db->sql_freeresult($result2);
					$template->assign_vars(array(
							'ERROR_MSG'			=> $error,
							'BUG_CAT_NAME'			=> $keyword,
							'BUG_CAT_DESC'			=> $message_parser->message,
							'BUG_CAT_STATUS'		=> $cat_status,
							'S_PARENT_OPTIONS'		=> $cat_list,
					));
}
				break;
			case 'select_cat':
if ($mode)
{
					$parents_id = (request_var('c', '')) ? request_var('c', '') : 0;
					$select_cat = true;

					$sql = 'SELECT bc.*
						FROM ' . BLOG_CAT_TABLE . " bc
						WHERE bc.bug_cat_id = " . $parents_id . "
						LIMIT 1";
					$result = $db->sql_query($sql);
					$cat_data = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					$bug_cat_name = $cat_data['bug_cat_name'];
					$bug_parent_cat_id = $cat_data['bug_parent_cat_id'];
					$bug_cat_url = append_sid("{$phpbb_admin_path}index.$phpEx",  'i=lexikon&amp;mode=select_cat&amp;c=' . $cat_data['bug_cat_id']);
					$bug_cat = ($bug_cat_name) ? ' &#8249; <a href="' . $bug_cat_url . '"><strong>' . censor_text($bug_cat_name) . '</strong></a>' : '';
					while ($bug_parent_cat_id > 0)
					{
						$sql = 'SELECT bc.*
							FROM ' . BLOG_CAT_TABLE . " bc
							WHERE bc.bug_cat_id = " . $bug_parent_cat_id ."
							ORDER BY bc.bug_cat_id";
						$result = $db->sql_query($sql);
						while ($row = $db->sql_fetchrow($result))
						{
							$bug_cat = ' &#8249; <a href="' . append_sid("{$phpbb_admin_path}index.$phpEx",  'i=lexikon&amp;mode=select_cat&amp;c=' . $row['bug_cat_id']) . '"><strong>' . censor_text($row['bug_cat_name']) . '</strong></a>' . $bug_cat;
							$bug_parent_cat_id = $row['bug_parent_cat_id'];
						}
						$db->sql_freeresult($result);
					}
					$bug_cat = ($bug_cat) ? '<a href="' . append_sid("{$phpbb_admin_path}index.$phpEx",  'i=lexikon&amp;mode=select_cat') . '"><strong>' . $user->lang['BT_BUGTRACKER_INDEX'] . '</strong></a>' . $bug_cat : '';

					if ($submit)
					{
						include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);

						$message_parser = new parse_message();
						$message_parser->message = utf8_normalize_nfc(request_var('lexikon_cat_desc', '', true));
						$message_parser->parse(true, true, true, true, false, true, true, true);

						$bug_cat_name	= utf8_normalize_nfc(request_var('bug_cat_name', '', true));
						$cat_status	= utf8_normalize_nfc(request_var('bug_cat_status', '', true));
						$cat_par_id	= 1;//utf8_normalize_nfc(request_var('bug_cat_parent_id', '', true));
						$sql = "INSERT INTO " . BLOG_CAT_TABLE . " (bug_parent_cat_id, bug_cat_name, bug_cat_desc, bug_cat_bbcode_bitfield, bug_cat_bbcode_uid, bug_cat_status) VALUES
							('$cat_par_id', '" . $db->sql_escape($bug_cat_name) . "', '" . $db->sql_escape($message_parser->message) . "', '" . $db->sql_escape($message_parser->bbcode_bitfield) . "', '" . $db->sql_escape($message_parser->bbcode_uid) . "', '$cat_status')";
						$db->sql_query($sql);
						//trigger_error($user->lang['BT_SAVED_GROUP'] . adm_back_link($this->u_action));
					}
					$rowset = $post_list = array();
					$sql = 'SELECT bc.bug_cat_id
						FROM ' . BLOG_CAT_TABLE . " bc
						WHERE bc.bug_parent_cat_id = " . $parents_id . "
						ORDER BY bc.bug_cat_id";
					$result = $db->sql_query($sql);

					$i = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						$post_list[$i] = $row['bug_cat_id'];
						$i++;
					}
					$db->sql_freeresult($result);
				if ($i)
				{
					$sql = 'SELECT bc.*
						FROM ' . BLOG_CAT_TABLE . " bc
						WHERE bc.bug_parent_cat_id = " . $parents_id . "
						ORDER BY bc.bug_cat_id ASC";
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$rowset[$row['bug_cat_id']] = array(
							'bug_cat_id'			=> $row['bug_cat_id'],
							'bug_cat_name'			=> $row['bug_cat_name'],
							'bug_cat_status'		=> $row['bug_cat_status'],
							'bug_cat_desc'			=> $row['bug_cat_desc'],
							'bug_cat_bbcode_bitfield'	=> $row['bug_cat_bbcode_bitfield'],
							'bug_cat_bbcode_uid'		=> $row['bug_cat_bbcode_uid'],
						);
					}
					$db->sql_freeresult($result);
					$i_total = sizeof($rowset) - 1;
					$template->assign_vars(array(
						'S_NUM_POSTS' => sizeof($post_list))
					);
					$first_unread = $post_unread = false;
					for ($i = 0, $end = sizeof($post_list); $i < $end; ++$i)
					{
						if (!isset($rowset[$post_list[$i]]))
						{
							continue;
						}
						$row =& $rowset[$post_list[$i]];
						$message = $row['bug_cat_desc'];
						$row['bug_cat_desc'] = generate_text_for_display($row['bug_cat_desc'], $row['bug_cat_bbcode_uid'], $row['bug_cat_bbcode_bitfield'], 7);
						$subcats = '';
						$bug_cat_id = $row['bug_cat_id'];
							$sql2 = 'SELECT bc.*
								FROM ' . BLOG_CAT_TABLE . " bc
								WHERE bc.bug_parent_cat_id = " . $bug_cat_id . "
								ORDER BY bc.bug_cat_id ASC";
							$result2 = $db->sql_query($sql2);
							while ($row2 = $db->sql_fetchrow($result2))
							{
								$subcats .= ($subcats) ? ', <a href="' : '<a href="';
								$subcats .= append_sid("{$phpbb_admin_path}index.$phpEx",  'i=lexikon&amp;mode=select_cat&amp;c=' . $row2['bug_cat_id']);
								$subcats .= '">';
								if (!$row2['bug_cat_status'])
								{
									$subcats .= '<strike>';
								}
								$subcats .= censor_text($row2['bug_cat_name']);
								if (!$row2['bug_cat_status'])
								{
									$subcats .= '</strike>';
								}
								$subcats .= '</strong></a>';
							}
							if (!$row['bug_cat_status'])
							{
								$folder_image = '<img src="images/icon_folder_lock.gif" />';
							}
							else
							{
								$folder_image = ($subcats) ? '<img src="images/icon_subfolder.gif" />' : '<img src="images/icon_folder.gif" />';
							}
							$db->sql_freeresult($result2);
						$catrow = array(
							'FOLDER_IMAGE'			=> $folder_image,
							'BUG_ID'				=> $row['bug_cat_id'],
							'BUG_CAT_NAME'			=> censor_text($row['bug_cat_name']),
							'U_BUG_CAT'				=> append_sid("{$phpbb_admin_path}index.$phpEx",  'i=lexikon&amp;mode=select_cat&amp;c=' . $row['bug_cat_id']),
							'BUG_CAT_DESC'			=> $row['bug_cat_desc'],
							'BUG_CAT_STATUS'		=> $row['bug_cat_status'],
							'SUBCATS'				=> $subcats,

							'U_EDIT'			=> append_sid("{$phpbb_admin_path}index.$phpEx",  'i=lexikon&amp;catmode=edit_cat&amp;c=' . $row['bug_cat_id']),
							'U_DELETE'			=> append_sid("{$phpbb_admin_path}index.$phpEx",  'i=lexikon&amp;catmode=delete_cat&amp;c=' . $row['bug_cat_id']),
						);
						$template->assign_block_vars('cats', $catrow);
						unset($rowset[$post_list[$i]]);
					}
					unset($rowset);
					$template->assign_vars(array(
							'HAS_SUBCATS'			=> true,
							'BUG_CAT'				=> $bug_cat,
					));
				}
				else
				{
					$template->assign_vars(array(
							'HAS_SUBCATS'			=> false,
							'BUG_CAT'				=> $bug_cat,
					));
				}
}
				break;
			case 'setup':
if ($mode)
{
					$setup = true;
					if ($submit)
					{
						$bug_group_new	= request_var('bugtracker_group', '');
						if ($config['bugtracker_group'] != $bug_group_new)
						{
							set_config('bugtracker_group', $bug_group_new);
							trigger_error($user->lang['BT_SAVED_GROUP'] . adm_back_link($this->u_action));
						}
					}
					$sql = 'SELECT g.*
						FROM ' . GROUPS_TABLE . " g
						ORDER BY g.group_id";
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$bt_group_dd .= '<option value="' . $row['group_id'] . '"';
						if ($row['group_id'] <> $config['bugtracker_group'])
						{
							$bt_group_dd .= '';
						}
						else
						{
							$bt_group_dd .= ' selected="selected"';
						}
						$bt_group_dd .= '>';
						$bt_group_dd .= ($row['group_type'] != 3) ? $row['group_name'] : $user->lang['G_' . $row['group_name']];
						$bt_group_dd .= '</option>';
					}
					$db->sql_freeresult($result);
}
				break;
			default:
				$catmode = request_var('catmode', '', true);
				switch ($catmode)
				{
					case 'delete_cat':
if ($catmode)
{
					$cat_id = (request_var('c', '')) ? request_var('c', '') : 1;

					$sql3 = 'SELECT bc.*
						FROM ' . BLOG_CAT_TABLE . " bc
						WHERE bc.bug_parent_cat_id = " . $cat_id . "
						LIMIT 1";
					$result3 = $db->sql_query($sql3);
					$any_subs = ($db->sql_fetchrow($result3)) ? true : false;
					$db->sql_freeresult($result3);

					$sql4 = 'SELECT br.*
						FROM ' . BLOG_CAT_TABLE . " br
						WHERE br.bug_repo_cat_id = " . $cat_id . "
						LIMIT 1";
					$result4 = $db->sql_query($sql4);
					$any_bugs = ($db->sql_fetchrow($result4)) ? true : false;
					$db->sql_freeresult($result4);

					$delete_cat = true;
					$trigger_text = '';
					$bug_del_cat = request_var('bug_del_cat', '', true);
					if ($submit && $bug_del_cat)
					{
						if($any_subs)
						{
							$bug_move_subcats = request_var('bug_move_subcats', '', true);
							if ($bug_move_subcats)
							{
								// update subs mit cat id nach new cat id
								$bug_move_subcats_to = request_var('bug_move_subcats_to', '', true);
								$sql5 = 'SELECT bug_cat_name
									FROM ' . BLOG_CAT_TABLE . "
									WHERE bug_cat_id = " . $bug_move_subcats_to . "
									LIMIT 1";
								$result5 = $db->sql_query($sql5);
								while ($row5 = $db->sql_fetchrow($result5))
								{
									$cat_for_subs['bug_cat_name'] = $row5['bug_cat_name'];
								}
								$db->sql_freeresult($result5);
								$sql = 'UPDATE ' . BLOG_CAT_TABLE . " SET bug_parent_cat_id = '" . $bug_move_subcats_to . "' WHERE bug_parent_cat_id = '" . $cat_id . "'";
								$db->sql_query($sql);
								$trigger_text = sprintf($user->lang['BT_DEL_MOVED_SUBS'], $cat_for_subs['bug_cat_name']) . (($trigger_text) ? '<br />' : '') . $trigger_text;
							}
							else
							{
								// delete subs mit cat id
								$sql = 'DELETE FROM ' . BLOG_CAT_TABLE . " WHERE bug_parent_cat_id = '" . $cat_id . "'";
								$db->sql_query($sql);
								$trigger_text = $user->lang['BT_DEL_DELETED_SUBS'] . (($trigger_text) ? '<br />' : '') . $trigger_text;
							}
						}

						if($any_bugs)
						{
							$bug_move_bugs = request_var('bug_move_bugs', '', true);
							if ($bug_move_bugs)
							{
								// update bugs mit cat id nach new cat id
								$bug_move_bugs_to = request_var('bug_move_bugs_to', '', true);
								$sql6 = 'SELECT bug_cat_name
									FROM ' . BLOG_CAT_TABLE . "
									WHERE bug_cat_id = " . $bug_move_bugs_to . "
									LIMIT 1";
								$result6 = $db->sql_query($sql6);
								while ($row6 = $db->sql_fetchrow($result6))
								{
									$cat_for_bugs['bug_cat_name'] = $row6['bug_cat_name'];
								}
								$db->sql_freeresult($result6);
								$sql = 'UPDATE ' . BUG_REPORTS_TABLE . " SET bug_repo_cat_id = '" . $bug_move_bugs_to . "' WHERE bug_repo_cat_id = '" . $cat_id . "'";
								$db->sql_query($sql);
								$trigger_text = sprintf($user->lang['BT_DEL_MOVED_BUGS'], $cat_for_bugs['bug_cat_name']) . (($trigger_text) ? '<br />' : '') . $trigger_text;
							}
							else
							{
								// delete bugs mit cat id
								$sql = 'DELETE FROM ' . BUG_REPORTS_TABLE . " WHERE bug_repo_cat_id = '" . $cat_id . "'";
								$db->sql_query($sql);
								$trigger_text = $user->lang['BT_DEL_DELETED_BUGS'] . (($trigger_text) ? '<br />' : '') . $trigger_text;
							}
						}

						// delete the categorie
						$sql = 'DELETE FROM ' . BLOG_CAT_TABLE . " WHERE bug_cat_id = '" . $cat_id . "'";
						$db->sql_query($sql);
						$trigger_text = $user->lang['BT_DELETED_CAT'] . (($trigger_text) ? '<br />' : '') . $trigger_text;

						trigger_error($trigger_text . adm_back_link($this->u_action));
					}
					else if ($submit && !$bug_del_cat)
					{
						$trigger_text = $user->lang['BT_DELETED_CAT_NOT'];
						trigger_error($trigger_text . adm_back_link($this->u_action));
					}

					$cat_list = '';
					$cat_list .= '<option value="0" selected="selected">' . $user->lang['BT_NO_PARENT'] . '</option>';
					$sql2 = 'SELECT bc.*
						FROM ' . BLOG_CAT_TABLE . " bc
						ORDER BY bc.bug_cat_id";
					$result2 = $db->sql_query($sql2);
					while ($row2 = $db->sql_fetchrow($result2))
					{
						$cat_list .= '<option value="' . $row2['bug_cat_id'] . '">' . $row2['bug_cat_name'] . '</option>';
					}
					$db->sql_freeresult($result2);

					$template->assign_vars(array(
							'CAT_SUBS'			=> $any_subs,
							'CAT_BUGS'			=> $any_bugs,
							'S_PARENT_OPTIONS'	=> $cat_list,
					));
}
						break;
					case 'edit_cat':
if ($catmode)
{
					$edit_cat = $cat_status = true;
					$error = $bug_cat_name = $message_parser->message = '';
					$cat_par_id = 0;
					if ($submit)
					{
						$message_parser = new parse_message();
						$message_parser->message = utf8_normalize_nfc(request_var('lexikon_cat_desc', '', true));
						$message_parser->parse(true, true, true, true, false, true, true, true);

						$cat_id			= (request_var('c', '')) ? request_var('c', '') : 1;
						$bug_cat_name	= utf8_normalize_nfc(request_var('bug_cat_name', '', true));
						$cat_status		= utf8_normalize_nfc(request_var('bug_cat_status', '', true));
						$cat_par_id		= utf8_normalize_nfc(request_var('bug_parent_cat_id', '', true));
						if(!utf8_clean_string($bug_cat_name))
						{
							$error .= $user->lang['BT_CAT_NAME_EMPTY'];
						}
						if(!$error)
						{
							$sql = 'UPDATE ' . BLOG_CAT_TABLE . " SET
								bug_cat_name = '" . $db->sql_escape($bug_cat_name) . "',
								bug_parent_cat_id = '" . $db->sql_escape($cat_par_id) . "',
								bug_cat_desc = '" . $db->sql_escape($message_parser->message) . "',
								bug_cat_bbcode_uid = '" . $db->sql_escape($message_parser->bbcode_uid) . "',
								bug_cat_bbcode_bitfield = '" . $db->sql_escape($message_parser->bbcode_bitfield) . "',
								bug_cat_status = '" . $db->sql_escape($cat_status) . "'
								WHERE bug_cat_id = '" .$db->sql_escape($cat_id) . "'";
							$db->sql_query($sql);
							trigger_error($user->lang['BT_EDITED_CAT'] . adm_back_link(append_sid("{$phpbb_admin_path}index.$phpEx",  'i=lexikon&amp;mode=select_cat')));
						}
					}
					$cat_id = (request_var('c', '')) ? request_var('c', '') : 1;
					$sql = 'SELECT * FROM ' . BLOG_CAT_TABLE . "
						WHERE bug_cat_id = $cat_id
						LIMIT 1";
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$cat_list = '';
						$cat_list .= '<option value="0"';
						$cat_list .= ($row['bug_parent_cat_id'] <> 0) ? '' : ' selected="selected"';
						$cat_list .= '>' . $user->lang['BT_NO_PARENT'] . '</option>';
						$sql2 = 'SELECT bc.*
							FROM ' . BLOG_CAT_TABLE . " bc
							ORDER BY bc.bug_cat_id";
						$result2 = $db->sql_query($sql2);
						while ($row2 = $db->sql_fetchrow($result2))
						{
							$cat_list .= '<option value="' . $row2['bug_cat_id'] . '"';
							if ($row2['bug_cat_id'] <> $row['bug_parent_cat_id'])
							{
								$cat_list .= '';
							}
							else
							{
								$cat_list .= ' selected="selected"';
							}
							$cat_list .= '>' . $row2['bug_cat_name'] . '</option>';
						}
						$db->sql_freeresult($result2);
						//parse desc
						$message_parser = new parse_message();
						$message_parser->message = $row['bug_cat_desc'];
						$message_parser->decode_message($row['bug_cat_bbcode_uid']);
						$cat_status = $row['bug_cat_status'];
						$template->assign_vars(array(
							'ERROR_MSG'				=> $error,
							'BUG_CAT_NAME'			=> $row['bug_cat_name'],
							'BUG_CAT_DESC'			=> $message_parser->message,
							'BUG_CAT_STATUS'		=> $row['bug_cat_status'],
							'S_PARENT_OPTIONS'		=> $cat_list,
						));
					}
}
						break;
					default:
							//echo 'muh';
						break;
				}
				break;
		}


		$template->assign_vars(array(
			'BT_CAT_EDIT'			=> $edit_cat,
			'BT_CAT_DELETE'			=> $delete_cat,
			'BT_CAT_NEW'			=> $new_cat,
			'BT_CAT_SELECT'			=> $select_cat,
			'BT_SETUP'				=> $setup,
			'BT_GROUP_DROP_DOWN'	=> $bt_group_dd,
			'U_ACTION'				=> $this->u_action,
		));

	}
}

?>
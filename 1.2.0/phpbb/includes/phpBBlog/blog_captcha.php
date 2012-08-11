<?php
/***************************************************************************
 *                                blog_captcha.php
 *                            -------------------
 *   begin                : Skippy, Ap 16, 2005
 *   copyright            : (C) 2001 Cricca Hi!Wap
 *   email                : nardi@criccahiwap.it
 *
 *   $Id: blog_captcha.php,v 1.2.0 2006/02/5 12:02:15 nardi Exp $
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

class blog_captcha {
  
  /**
   * Generate the image based on the hash
   *
   * @param string $hash
   */
  function generateImage($GBid)
{
    global $db, $user;

$fontsdir = "./includes/phpbblog/fonts/";
$code = "";

$display_lines = "1";
$captcha_lines = "3";
$display_grid = "1"; //grid
$display_wave = "0";
$display_triangle = "0";
$display_border = "1";
// Imagesize
$x = 300; // width
$y = 40; // height
$y2 = $y/2; $x2 = $x/2;


	if (empty($GBid))
	{
		$error = TRUE;
		$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $user->lang['CONFIRM_CODE_WRONG'];


	}
	else
	{
		$confirm_id = htmlspecialchars($GBid);



		if (!preg_match('/^[A-Za-z0-9]+$/', $confirm_id))
		{
			$confirm_id = '';
		}

		$sql = 'SELECT code
			FROM ' . BLOG_CONFIRM_TABLE . "
			WHERE confirm_id = '$confirm_id'
			AND session_id = '" . $user->data['session_id'] . "'";
			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Could not obtain confirmation code', __LINE__, __FILE__, $sql);
			}

			if ($row = $db->sql_fetchrow($result))
			{
				$code = $row['code'];
				$db->sql_freeresult($result);
			}
	}



/*$fonts = array();
$fd = opendir($fontsdir);
while (false !== ($filename = readdir($fd)))
	if(strpos($filename, 'ttf') !== false)
   	$fonts[] = $filename;
rsort($fonts);
if(count($fonts) < 3) die('No fonts found!');

$font = $fontsdir.$fonts[mt_rand(0,(count($fonts)-3))];*/

header ("Content-type: image/png");
header('Cache-control: no-cache, no-store');
//echo $image;
$image = @imagecreatetruecolor($x,$y);
//$bgColor = ImageColorAllocate($image, 255, 255, 255);
$linecolor   = ImageColorAllocate($image, 0x33, 0x66, 0x99);
$bordercolor = ImageColorAllocate($image, 0x33, 0x66, 0x99);
$alphacolor  = ImageColorAllocate($image, 0, 255, 0);
//ImageFilledRectangle($image, 0, 0, $x/3, $y, $bgColor);


if ($display_grid) { // random grid color
	for ($i=0;$i<$x;$i+=10) {
		$color=imagecolorallocate($image,intval(rand(160,224)),intval(rand(160,224)),intval(rand(160,224)));
		imageline($image,$i,0,$i,$y,$color);
	}
	for ($i=0;$i<$y;$i+=10) {
		$color=imagecolorallocate($image,intval(rand(160,224)),intval(rand(160,224)),intval(rand(160,224)));
		imageline($image,0,$i,$x,$i,$color);
	}
}


for ($i=0;$i<=5;$i++)
    {
        $angel = rand(-20,20);
        $pos = $i*40+20;
        $size = rand(15,25);
        $z = 25+rand(0,10);
        $color = imagecolorallocate($image, rand(100,200),rand(150,255), rand(100,255));
	$font = $fontsdir.'font'.rand(0,4).'.ttf';
        imagettftext($image,$size,$angel,$pos,$z,$color,$font,substr($code,$i,1));
//        imagettftext($image,$size,$angel,$pos,$z,$color,$fontsdir.'font'.rand(1,9).'.ttf',substr($code,$i,1));
//        imagettftext($image,$size,$angel,$pos,$y,$color,$font,substr($code,$i,1));
    }

if ($display_lines) { // random lines
    //draw a bunch of lines
for($i=0;$i<$captcha_lines;$i++){
      $x_1 = rand(0, 150);
      $x_2 = rand(0, 150);
      $y_1 = rand(0, 30);
      $y_2 = rand(0, 30);
      $color = imagecolorallocate($image, rand(100,200),rand(150,255), rand(100,255));
      imageline($image, $x_1, $y_1, $x_2, $y_2, $color);
}
}


if ($display_wave) { // Wave
ImageSetThickness($image, 3);
$ux = $uy = 0;
$vx = 0; //mt_rand(10,15);
$vy = mt_rand($y2-3, $y2+3);

for($i = 0; $i < 10; $i++) {
	$ux = $vx + mt_rand(20,30);
	$uy = mt_rand($y2-8,$y2+8);
	ImageSetThickness($image, mt_rand(1,2));
	ImageLine($image, $vx, $vy, $ux, $uy, $linecolor);
	$vx = $ux;
	$vy = $uy;
}
ImageLine($image, $vx, $vy, $x, $y2, $linecolor);
}

if ($display_triangle) { // Triangle
ImageSetThickness($image, 3);
$ux = mt_rand($x2-10, $x2+10);
$uy = mt_rand($y2-10, $y2-30);
ImageLine($image, mt_rand(10,$x2-20), $y, $ux, $uy, $linecolor);
ImageSetThickness($image, 1);
ImageLine($image,  mt_rand($x2+20,$x-10), $y, $ux, $uy, $linecolor);
ImageSetThickness($image, 1);
}


if ($display_border) { // Border
ImageSetThickness($image, 1);
ImageLine($image, 0, 0, 0, $y, $bordercolor); // left
ImageLine($image, 0, 0, $x, 0, $bordercolor); // top
ImageLine($image, 0, $y-1, $x, $y-1, $bordercolor); // bottom
ImageLine($image, $x-1, 0, $x-1, $y-1, $bordercolor); // right
}




imagepng($image);
imagedestroy($image);



}


  
  /**
   * Generate new code
   *
   * @return int ID
   */
  function generateCode(&$confirm_image, &$s_hidden_fields){
    global $db, $user, $phpEx;
    
    
		$sql = 'SELECT session_id
			FROM ' . SESSIONS_TABLE;
		if (!($result = $db->sql_query($sql)))
		{
      trigger_error('Could not select session data');
			message_die(GENERAL_ERROR, 'Could not select session data', '', __LINE__, __FILE__, $sql);
		}

		if ($row = $db->sql_fetchrow($result))
		{
			$confirm_sql = '';
			do
			{
				$confirm_sql .= (($confirm_sql != '') ? ', ' : '') . "'" . $row['session_id'] . "'";
			}
			while ($row = $db->sql_fetchrow($result));

			$sql = 'DELETE FROM ' .  BLOG_CONFIRM_TABLE . "
				WHERE session_id NOT IN ($confirm_sql)";
			if (!$db->sql_query($sql))
			{
        trigger_error('Could not delete stale confirm data');
				message_die(GENERAL_ERROR, 'Could not delete stale confirm data', '', __LINE__, __FILE__, $sql);
			}
		}
		$db->sql_freeresult($result);


      $sql = 'SELECT COUNT(session_id) AS attempts 
         FROM ' . BLOG_CONFIRM_TABLE . " 
         WHERE session_id = '" . $db->sql_escape($user->data['session_id']) . "'"; 
      $result = $db->sql_query($sql); 
      $attempts = (int) $db->sql_fetchfield('attempts'); 
      $db->sql_freeresult($result); 

      if ( $attempts > 3) 
//      if ($guest_config['captcha_attempts'] && $attempts > $guest_config['captcha_attempts']) 
      { 
         trigger_error($user->lang['TOO_MANY_COMMENTS']); 
      } 




		// Generate the required confirmation code
		// NB 0 (zero) could get confused with O (the letter) so we make change it
    $code = gen_rand_string(mt_rand(5, 8));
		//$code = dss_rand();
		$code = substr(str_replace('0', 'Z', strtoupper(base_convert($code, 16, 35))), 2, 6);

 //   $confirm_id = md5(unique_id($user->ip)); 
//		$confirm_id = md5(uniqid($user_ip));
		$confirm_id = md5(uniqid($user->ip));

		$sql = 'INSERT INTO ' . BLOG_CONFIRM_TABLE . " (confirm_id, session_id, code)
			VALUES ('$confirm_id', '". /*(string) $user->session_id*/$user->data['session_id'] . "', '$code')";
		if (!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not insert new confirm code information', '', __LINE__, __FILE__, $sql);
		}

		unset($code);

		$confirm_image .= '<img src="' . append_sid("blog_comments.$phpEx?perform=confirm&cid=$confirm_id") . '" alt="" title="" />';
		$s_hidden_fields .= '<input type="hidden" name="confirm_id" value="' . $confirm_id . '" />';

  }


  /**
  * Generate new code
  *
  * @return int ID
  */
  function checkCaptchaCode(&$error_msg, $GB_confirm_id, $GB_confirm_code)
  {
    global $db, $user;

		if (empty(/*$HTTP_POST_VARS['confirm_id']*/$GB_confirm_id))
		{
			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $user->lang['CONFIRM_CODE_WRONG'];
		}
		else
		{
			$confirm_id = htmlspecialchars($GB_confirm_id/*$HTTP_POST_VARS['confirm_id']*/);
			$confirm_code= /*$HTTP_POST_VARS['confirm_code']*/$GB_confirm_code;


			if (!preg_match('/^[A-Za-z0-9]+$/', $confirm_id))
			{
				$confirm_id = '';
			}

      $sql = 'SELECT code 
        FROM ' . BLOG_CONFIRM_TABLE . " 
        WHERE confirm_id = '" . $db->sql_escape($confirm_id) . "' 
                  AND session_id = '" . $db->sql_escape($user->data['session_id']) . "'"; 
			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Could not obtain confirmation code', __LINE__, __FILE__, $sql);
			}
			if ($row = $db->sql_fetchrow($result))
			{
				if (strcasecmp($row['code'], $confirm_code) === 0) 
        //if ($row['code'] == $confirm_code)
				{
          $sql = 'DELETE FROM ' . BLOG_CONFIRM_TABLE . " 
            WHERE confirm_id = '" . $db->sql_escape($confirm_id) . "' 
            AND session_id = '" . $db->sql_escape($user->data['session_id']) . "'"; 
					if (!$db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, 'Could not delete confirmation code', __LINE__, __FILE__, $sql);
					}
				}
				else
				{
					$error = TRUE;
					$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $user->lang['CONFIRM_CODE_WRONG'];
				}
			}
			else
			{
				$error = TRUE;
				$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $user->lang['CONFIRM_CODE_WRONG'];
			}
			$db->sql_freeresult($result);
		}
  }



/**
* handle_captcha
*
* @param string $mode The mode, build or check, to either build the captcha/confirm box, or to check if the user entered the correct confirm_code
*
* @return Returns
*	- True if the captcha code is correct and $mode is check or they do not need to view the captcha (permissions) 
*	- False if the captcha code is incorrect, or not given and $mode is check
*/
function handle_captcha($mode)
{
	global $db, $template, $phpbb_root_path, $phpEx, $user, $config, $s_hidden_fields, $blog_plugins;

	if ($user->data['user_id'] != ANONYMOUS || !$config['user_blog_guest_captcha'])
	{
		return true;
	}

	blog_plugins::plugin_do_arg('function_handle_captcha', $mode);

	if ($mode == 'check')
	{
		$confirm_id = request_var('confirm_id', '');
		$confirm_code = request_var('confirm_code', '');

		if ($confirm_id == '' || $confirm_code == '')
		{
			return false;
		}

		$sql = 'SELECT code
			FROM ' . CONFIRM_TABLE . "
			WHERE confirm_id = '" . $db->sql_escape($confirm_id) . "'
				AND session_id = '" . $db->sql_escape($user->session_id) . "'
				AND confirm_type = " . CONFIRM_POST;
		$result = $db->sql_query($sql);
		$confirm_row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (empty($confirm_row['code']) || strcasecmp($confirm_row['code'], $confirm_code) !== 0)
		{
			return false;
		}

		// add confirm_id and confirm_code to hidden fields if not already there so the user doesn't need to retype in the confirm code if 
		if (strpos($s_hidden_fields, 'confirm_id') === false)
		{
			$s_hidden_fields .= build_hidden_fields(array('confirm_id' => $confirm_id, 'confirm_code' => $confirm_code));
		}

		return true;
	}
	else if ($mode == 'build' && !handle_captcha('check'))
	{
		// Show confirm image
		$sql = 'DELETE FROM ' . CONFIRM_TABLE . "
			WHERE session_id = '" . $db->sql_escape($user->session_id) . "'
				AND confirm_type = " . CONFIRM_POST;
		$db->sql_query($sql);

		// Generate code
		$code = gen_rand_string(mt_rand(5, 8));
		$confirm_id = md5(unique_id($user->ip));
		$seed = hexdec(substr(unique_id(), 4, 10));

		// compute $seed % 0x7fffffff
		$seed -= 0x7fffffff* floor($seed / 0x7fffffff);

		$sql = 'INSERT INTO ' . CONFIRM_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'confirm_id'	=> (string) $confirm_id,
			'session_id'	=> (string) $user->session_id,
			'confirm_type'	=> (int) CONFIRM_POST,
			'code'			=> (string) $code,
			'seed'			=> (int) $seed)
		);
		$db->sql_query($sql);

		$template->assign_vars(array(
			'S_CONFIRM_CODE'			=> true,
			'CONFIRM_ID'				=> $confirm_id,
			'CONFIRM_IMAGE'				=> '<img src="' . append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=confirm&amp;id=' . $confirm_id . '&amp;type=' . CONFIRM_POST) . '" alt="" title="" />',
			'L_POST_CONFIRM_EXPLAIN'	=> sprintf($user->lang['POST_CONFIRM_EXPLAIN'], '<a href="mailto:' . htmlspecialchars($config['board_contact']) . '">', '</a>'),
		));
	}
}



}

?>
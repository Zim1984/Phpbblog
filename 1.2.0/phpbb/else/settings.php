<?php
/*
What is the name of your blog?  This will be used for the title shown in the
RSS file.  NOTE: lots of people have blog names like "Tim's blog" and that's
fine, except don't forget to escape the apostrophe!  Like this:
$blog_name = 'Tim\'s blog';
*/
$blog_name = 'Blog name here';

/*
If many different people will able to post entries to this blog, leave this
blank (the default).  However, if there will be a single author of this blog,
enter that name (if you desire the name to be displayed in the HTML META tags).
Don't forget to escape any apostrophes!  Like this:
$author_name = 'Sandra O\'Connor';
*/
$author_name = '';

/*
Below is the copyright used for the RSS file.  Feel free to change it.
*/
$copyright = 'Copyright 2006 ' . $blog_name;

/*
What is the short summary or tagline of your blog?  This will also be used for
the RSS file.  Remember to escape the apostrophes!
*/
$blog_summary = 'A blog.';

/*
What keywords are relevant to your blog?  List them comma-separated.  Remember
to escape the apostrophes!
*/
$keywords = 'blog, journal';

/*
What language does your blog use?  A list of codes can be found here:
http://blogs.law.harvard.edu/tech/stories/storyReader$15
*/
$language = 'en-us';

/*
Ready to double-up on the language?  Due to competing standards, we also would
like to have your language in the 3-character ISO 639 format.  See here:
http://xml.coverpages.org/nisoLang3-1994.html
*/
$iso639_language = 'ENG';

/*
How often do you publish your blog?  Think of your more productive averages.
Then express that in seconds.  That's how frequently aggregators will check
your blog.  Here are some quickies: weekly = 604800; daily = 86400.
*/
$frequency = '43200';

/*
How do you want your blog to look?  There are 7 styles:
   default = red, blue
   ken = blue
   barbie = pink
   hendrix = purple
   marley = red, green, yellow
   neo = green, black
   musuem = pale green, tan
Pick the name you prefer and enter it below.
*/
$stylesheet = 'museum';

/*
What is the web site URL for your logo?  You can leave this blank, like this:
$logo_url = '';
*/
$logo_url = 'http://www.mywebsite.com/images/logo.png';

/*
What is the height of your logo image?  You can leave this blank, like this:
$logo_height = '';
*/
$logo_height = '49';

/*
What is the width of your logo image?  You can leave this blank, like this:
$logo_width = '';
*/
$logo_width = '100';

/*
Hide Announcements & Stickies on the blog/RSS pages?  Hide = 1.  Show = 0.
*/
$hide_stickies = 1;

/*
If you want your blog to light up on the map at "World as a Blog" you'll need
to add you latitude and longitude here.  The World as a Blog is at:
http://brainoff.com/geoblog/
And you can find the coordinates of your location using these resources:
http://brainoff.com/geoblog/resources.html
*/
$latitude = '';
$longitude = '';

/*
If you want your blog to be categorized on Syndic8, you need to specify your
country.  The list of 2-character country codes is here:
http://www.iso.org/iso/en/prods-services/iso3166ma/02iso-3166-code-lists/list-en1.html
*/
$geo_country = '';

/*
Where is your PHPBB directory?  This should be the path on the server.  Here is
how you get this data: telnet or SSH to your server, and cd to the phpBB
directory; enter "pwd" to get the full server path; copy that to here.  It
should start with a slash and end with a slash.  Note: if you can't telnet or
SSH, I have included a file called pwd.php.  Put it into the phpBB folder on
your server, and then view it with your Web browser (probably at
yoursite.com/phpBB/pwd.php).  It will tell you what $phpbb_root_path should be.
*/
$phpbb_root_path = '/home/something/something/';

/*
What is the web site URL for phpBB?  This should end in a slash.
*/
$phpbb_url = 'http://www.yoursite.com/phpBB/';

/*
What is the web site URL for your blog directory?  This should end in a slash.
*/
$blog_url = 'http://www.yoursite.com/blog/';

/*
Set $enable_calendar to 0 to disable the calendar.
*/
$enable_calendar = 1;

/*
Set $cal_link_type to 1 to make calendar links point to forum entries instead of
permalinks.
*/
$cal_link_type = 0;

/*
What forum should we use?  To find out, view the forum in a Web browser.
Look in the URL for a forum ID, and enter it here.
*/
$forum = 2;

/*
How many blogs should show on the main blog page?
*/
$max_blogs = 3;

/*
How long can the blog text get before we truncate it for display on the main
blog page?  For unlimited length, set this to zero.  I don't suggest truncating
blogs, as your HTML can be truncated too.  For example, if a closing bold tag
were truncated, everything after your blog entry would appear bolded.
*/
$blog_length = 0;

/*
In addition to showing however many blogs you set above, you can opt to show
title-only links to previous blogs at the end of the entries.  Set this to
zero to show no links, or any positive integer to show that many links.
*/
$parting_shots = 3;

/*
How many blog entries should appear in the RSS file?
*/
$max_rss_entries = 10;

/*
How long can an RSS entry get before we truncate it for the rss.php file?  For
unlimited length, set this to zero.  I suggest truncating this.  It won't cut
off any HTML mid-tag, because we strip tags for the RSS feed.
*/
$rss_length = 255;

/*
Do you want to show the date of each blog entry on the main blog page?  For yes
set this to 1.  For no, set it to zero.
*/
$show_dates = 1;

/*
The permalink page shows next/previous/first links.  Since the first link never
changes, you can save 1 SQL query by entering the id of your first blog.  The
easiest way to do this is to leave this blank at first, get the blog running,
click a permalink link for any entry, & then click the "first" link to view the
first entry.  In the URL for the first entry is a number, such as permalink=12
-- the 12 (or whatever number is listed) is what you would enter here.
*/
$first_id = '';

/*
Do you want to disable trackbacks?  Yes = 1.  No = 0.
*/
$hide_trackbacks = 0;

/*
How should the "comment" link work?  Traditionally, when someone uses
that link, he/she is dropped into the phpBB forum, in the topic
being read.  If you set this to 0 (zero), then a more blog-like interface
will take over.  Note: only set this to zero if you allow anyone to post to
your forum.
*/
$comment_via_forum = 1;

/*
Do you want the bylines to link to phpBB profiles?  Yes = 1.  No = 0.
*/
$show_profiles = 1;

/*
Finally, do you want to hide avatars?  Yes = 1.  No = 0.
*/
$hide_avatars = 1;

/*
Everything after this point just works.  No more variables to set.
*/
define('IN_PHPBB', true);
$phpbb_root_path = (preg_match("/\/$/i", $phpbb_root_path)) ? $phpbb_root_path : $phpbb_root_path . '/';
$phpbb_url = (preg_match("/\/$/i", $phpbb_url)) ? $phpbb_url : $phpbb_url . '/';
$more_url = $phpbb_url . 'viewforum.' . $phpEx . '?f=' . $forum;

$geo_meta_tags = '';
if ($latitude && $longitude) {
	$geo_meta_tags = '<meta name="ICBM" content="' . $latitude . ',' . $longitude . '" />' . "\n";
	$geo_meta_tags .= '<meta name="geo.position" content="' . $latitude . ';' . $longitude . '" />' . "\n";
	$geo_meta_tags .= '<meta name="DC.title" content="' . $blog_name . '" />' . "\n";
}
if ($geo_country) {
	$geo_meta_tags .= '<meta name="geo.country" content="' . $geo_country . '" />' . "\n";
}
$author_tag = '';
if ($author_name) {
	$author_tag = '<meta name="author" content="' . $author_name . '" />' . "\n";
}

$logo_tag = '';
if ($logo_url) {
	$logo_tag = '<img alt="' . $blog_name . '" id="logo" src="' . $logo_url . '" ';
	$logo_tag .= ($logo_height) ? 'height="' . $logo_height . '" ' : '';
	$logo_tag .= ($logo_width) ? 'width="' . $logo_width . '" ' : '';
	$logo_tag .= '/>';
}

function cleanup($str) {
	/*
	The replacements that follow should be expanded.  Basically, any block-
	level tag should be replaced with a space.  However, I'm trying to get a
	big bang without a speed hit, so I'm only doing the biggies.
	*/
	$str = preg_replace("/\[[^\]]+\]/i", " ", $str);
	$str = preg_replace("/<\/?(p|td|th|li|dt|dd)[^>]*>/i", " ", $str);
	$str = preg_replace("/<br[^>]*>/i", " ", $str);
	$str = preg_replace("/[\r\n\t ]+/", " ", $str);
	$str = strip_tags($str);
	$str = trim($str);
	return $str;
}

/*
Thanks to geniusdex & hwlab for posting sample code to php.net.
*/
function shorten($str, $max_chars) {
	if (strlen($str) > $max_chars) {
		$str = substr($str, 0, ($max_chars - 3));
		$last_space = strrpos($str, ' ');
		$str = substr($str, 0, $last_space);
		/*
		Here we check to see if we just truncated right in the middle of a tag,
		like "<a " (if that happens, adding the ">" character will at least
		keep the page from crashing & burning)!
		*/
		if (strrpos($str, '>') < strrpos($str, '<')) {
			$str .= '>';
		}
		$str .= '...';
	}
	return $str;
}

/*
Fetch the needed phpBB files.
*/
if (!@include_once($phpbb_root_path . 'extension.inc')) {
	die('Unable to open extension.inc.  Feeling helpful?  Send an email to the webmaster.');
}
if (!@include_once($phpbb_root_path . 'common.' . $phpEx)) {
	die('Unable to open config file.  Feeling helpful?  Send an email to the webmaster.');
}
if (!@include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx)) {
	die('Unable to open bbcode file.  Feeling helpful?  Send an email to the webmaster.');
}

/*
I need to run a SESSION just to have the bbcode file work!
*/
$userdata = session_pagestart($user_ip, $forum);
init_userprefs($userdata);
?>

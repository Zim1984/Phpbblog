#
# $Id: $
#

# Table: 'phpbb_blog'
CREATE TABLE phpbb_blog (
  id mediumint(8) UNSIGNED NOT NULL auto_increment,
  user_id mediumint(8) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  modifytime int(11) NOT NULL default '0',
  modifier varchar(30) NOT NULL default '',
  status enum('live','draft') NOT NULL default 'live',
  titel varchar(255) NOT NULL default '',
  text mediumtext NOT NULL,
  bbcode_uid varchar(8) NOT NULL,
  bbcode_bitfield varchar(255) NOT NULL,
  flags int(2) unsigned NOT NULL,
  quelle varchar(255) NOT NULL default '',
  trackbacks mediumtext NOT NULL,
  permit_com int(1) NOT NULL default '1',
  permit_com enum('allow','timed','disallow') NOT NULL default 'allow',
  permit_tb int(1) NOT NULL default '1',
  permit_guest int(1) NOT NULL default '1',
  autodisabledate int(11) NOT NULL default '0',
  passw varchar(50) NOT NULL default '',
  category_id int(8) NOT NULL default '0',
  rating_all int(8) default '0',
  rating_count int(8) default '0',
  blog_com_count int(6) NOT NULL default '0',
  blog_read_count int(6) NOT NULL default '0',
  blog_tb_count int(6) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY category_id (category_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_blog_trackbacks'
CREATE TABLE IF NOT EXISTS phpbb_blog_trackbacks (
 tb_ID bigint(20) unsigned NOT NULL auto_increment,
 tb_entry_ID mediumint(8) NOT NULL default '0',
 tb_title varchar(255) NOT NULL default '',
 tb_blog_name varchar(255) NOT NULL default '',
 tb_blog_favicon varchar(255) NOT NULL default '',
 tb_blog_url varchar(200) NOT NULL default '',
 tb_blog_IP varchar(100) NOT NULL default '',
 tb_date int(11) NOT NULL default '0',
 tb_excerpt text NOT NULL,
 tb_approved int(1) NOT NULL default '1',
 tb_type enum('trackback','pingback') NOT NULL default 'trackback',
 tb_agent varchar(255) NOT NULL default '',
 PRIMARY KEY (tb_ID),
 KEY tb_approved (tb_approved),
 KEY tb_entry_ID (tb_entry_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


# Table: 'phpbb_blog_com'
CREATE TABLE phpbb_blog_com (
  id mediumint(8) NOT NULL auto_increment,
  id_blog tinyint(5) NOT NULL default '0',
  user_id mediumint(8) NOT NULL default '0',
  user_name varchar(25) NOT NULL default '',
  user_email varchar(255) default NULL,
  protect_email smallint(1) NOT NULL default '0',
  email_advice smallint(1) NOT NULL default '0',
  com varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  rating smallint(2) default '0',
  PRIMARY KEY  (id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_blog_cat'
CREATE TABLE phpbb_blog_cat (
  cat_id int(8) NOT NULL auto_increment,
  cat_titel varchar(255) NOT NULL default '',
  cat_desc mediumtext NOT NULL,
  PRIMARY KEY  (cat_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;

# Table: 'phpbb_blog_config'
CREATE TABLE phpbb_blog_config (
  config_name varchar(255) NOT NULL default '',
  config_value varchar(255) NOT NULL default '',
  PRIMARY KEY  (config_name)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;

# Table: 'phpbb_blog_confirm'
CREATE TABLE phpbb_blog_confirm (
  confirm_id char(32) NOT NULL default '',
  session_id char(32) NOT NULL default '',
  code char(6) NOT NULL default '',
  PRIMARY KEY  (session_id,confirm_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;
#
# $Id: schema_data.sql,v 1.257 2007/09/20 21:19:00 stoffel04 Exp $
#

# POSTGRES BEGIN #

# -- Config

INSERT INTO phpbb_blog_config VALUES ('blog_online', '1');
INSERT INTO phpbb_blog_config VALUES ('permit_mod', '0');
INSERT INTO phpbb_blog_config VALUES ('blogger_group', '');
INSERT INTO phpbb_blog_config VALUES ('blogger_user', '');

INSERT INTO phpbb_blog_config VALUES ('onentry_sendmail', '0');
INSERT INTO phpbb_blog_config VALUES ('category_id', '1');

INSERT INTO phpbb_blog_config VALUES ('allow_cat', '1');
INSERT INTO phpbb_blog_config VALUES ('allow_trackbacks', '1');
INSERT INTO phpbb_blog_config VALUES ('allow_rss', '1');
INSERT INTO phpbb_blog_config VALUES ('allow_rating', '1');

INSERT INTO phpbb_blog_config VALUES ('allow_guest_com', '1');
INSERT INTO phpbb_blog_config VALUES ('guest_com_captcha', '1');
INSERT INTO phpbb_blog_config VALUES ('allow_guest_rating', '1');

INSERT INTO phpbb_blog_config VALUES ('smilies_column', '6');
INSERT INTO phpbb_blog_config VALUES ('smilies_row', '3');

INSERT INTO phpbb_blog_config VALUES ('new_archiv_menu', '1');
INSERT INTO phpbb_blog_config VALUES ('cal_archiv_menu', '1');
INSERT INTO phpbb_blog_config VALUES ('blog_wordcloud', '1');


INSERT INTO phpbb_blog_config VALUES ('version', '1.2.1');

# -- categorien
INSERT INTO phpbb_blog_cat VALUES (1, 'default', 'Standard Kategorie');
INSERT INTO phpbb_blog_cat VALUES (2, 'news', 'Neuigkeiten ');
INSERT INTO phpbb_blog_cat VALUES (3, 'private', 'Über Skippy Urlaub usw');
INSERT INTO phpbb_blog_cat VALUES (4, 'around the board', 'Rund um die Seite galvano-atelier');



# POSTGRES COMMIT #


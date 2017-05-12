<?php
/**
 * Country Flag
 * Copyright 2013 Starpaul20
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Neat trick for caching our custom template(s)
if(THIS_SCRIPT == 'usercp.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'usercp_profile_country_required,usercp_profile_country_optional,usercp_profile_country_country';
}

if(THIS_SCRIPT == 'showthread.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_country';
}

if(THIS_SCRIPT == 'private.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_country';
}

if(THIS_SCRIPT == 'announcements.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_country';
}

if(THIS_SCRIPT == 'newthread.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_country';
}

if(THIS_SCRIPT == 'newreply.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_country';
}

if(THIS_SCRIPT == 'editpost.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_country';
}

if(THIS_SCRIPT == 'member.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'global_country,member_register_country,member_register_country_country';
}

if(THIS_SCRIPT == 'memberlist.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'global_country';
}

// Tell MyBB when to run the hooks
$plugins->add_hook("postbit", "country_run");
$plugins->add_hook("postbit_pm", "country_run");
$plugins->add_hook("postbit_announcement", "country_run");
$plugins->add_hook("postbit_prev", "country_run");
$plugins->add_hook("member_profile_end", "country_profile");
$plugins->add_hook("usercp_profile_start", "country_select");
$plugins->add_hook("usercp_do_profile_end", "country_do_select");
$plugins->add_hook("member_register_start", "country_register");
$plugins->add_hook("datahandler_user_validate", "country_validate");
$plugins->add_hook("datahandler_user_insert_end", "country_do_register");
$plugins->add_hook("memberlist_user", "country_memberlist");

$plugins->add_hook("admin_formcontainer_output_row", "country_user_editing");
$plugins->add_hook("admin_user_users_edit_commit", "country_user_editing_commit");
$plugins->add_hook("admin_config_menu", "country_admin_menu");
$plugins->add_hook("admin_config_action_handler", "country_admin_action_handler");
$plugins->add_hook("admin_config_permissions", "country_admin_permissions");
$plugins->add_hook("admin_tools_get_admin_log_action", "country_admin_adminlog");

// The information that shows up on the plugin manager
function country_info()
{
	global $lang;
	$lang->load("country", true);

	return array(
		"name"				=> $lang->country_info_name,
		"description"		=> $lang->country_info_desc,
		"website"			=> "http://galaxiesrealm.com/index.php",
		"author"			=> "Starpaul20",
		"authorsite"		=> "http://galaxiesrealm.com/index.php",
		"version"			=> "1.1.1",
		"codename"			=> "country",
		"compatibility"		=> "18*"
	);
}

// This function runs when the plugin is installed.
function country_install()
{
	global $db;
	country_uninstall();
	$collation = $db->build_create_table_collation();

	switch($db->type)
	{
		case "pgsql":
			$db->write_query("CREATE TABLE ".TABLE_PREFIX."countries (
				cid serial,
				name varchar(150) NOT NULL default '',
				flag varchar(255) NOT NULL default '',
				PRIMARY KEY (cid)
			);");
			break;
		case "sqlite":
			$db->write_query("CREATE TABLE ".TABLE_PREFIX."countries (
				cid INTEGER PRIMARY KEY,
				name varchar(150) NOT NULL default '',
				flag varchar(255) NOT NULL default ''
			);");
			break;
		default:
			$db->write_query("CREATE TABLE ".TABLE_PREFIX."countries (
				cid int unsigned NOT NULL auto_increment,
				name varchar(150) NOT NULL default '',
				flag varchar(255) NOT NULL default '',
				PRIMARY KEY (cid)
			) ENGINE=MyISAM{$collation};");
			break;
	}

	switch($db->type)
	{
		case "pgsql":
			$db->add_column("users", "country", "int NOT NULL default '0'");
			break;
		case "sqlite":
			$db->add_column("users", "country", "int NOT NULL default '0'");
			break;
		default:
			$db->add_column("users", "country", "int unsigned NOT NULL default '0'");
			break;
	}

	$db->write_query("INSERT INTO ".TABLE_PREFIX."countries (cid, name, flag) VALUES
(1, '<lang:country_afghanistan>', 'images/flags/afghanistan.png'),
(2, '<lang:country_aland_islands>', 'images/flags/aland_islands.png'),
(3, '<lang:country_albania>', 'images/flags/albania.png'),
(4, '<lang:country_algeria>', 'images/flags/algeria.png'),
(5, '<lang:country_american_samoa>', 'images/flags/american_samoa.png'),
(6, '<lang:country_andorra>', 'images/flags/andorra.png'),
(7, '<lang:country_angola>', 'images/flags/angola.png'),
(8, '<lang:country_anguilla>', 'images/flags/anguilla.png'),
(9, '<lang:country_antarctica>', 'images/flags/antarctica.png'),
(10, '<lang:country_antigua_and_barbuda>', 'images/flags/antigua_and_barbuda.png'),
(11, '<lang:country_argentina>', 'images/flags/argentina.png'),
(12, '<lang:country_armenia>', 'images/flags/armenia.png'),
(13, '<lang:country_aruba>', 'images/flags/aruba.png'),
(14, '<lang:country_australia>', 'images/flags/australia.png'),
(15, '<lang:country_austria>', 'images/flags/austria.png'),
(16, '<lang:country_azerbaijan>', 'images/flags/azerbaijan.png'),
(17, '<lang:country_azores>', 'images/flags/azores.png'),
(18, '<lang:country_bahamas>', 'images/flags/bahamas.png'),
(19, '<lang:country_bahrain>', 'images/flags/bahrain.png'),
(20, '<lang:country_bangladesh>', 'images/flags/bangladesh.png'),
(21, '<lang:country_barbados>', 'images/flags/barbados.png'),
(22, '<lang:country_belarus>', 'images/flags/belarus.png'),
(23, '<lang:country_belgium>', 'images/flags/belgium.png'),
(24, '<lang:country_belize>', 'images/flags/belize.png'),
(25, '<lang:country_benin>', 'images/flags/benin.png'),
(26, '<lang:country_bermuda>', 'images/flags/bermuda.png'),
(27, '<lang:country_bhutan>', 'images/flags/bhutan.png'),
(28, '<lang:country_bolivia>', 'images/flags/bolivia.png'),
(29, '<lang:country_bosnia_and_herzegovina>', 'images/flags/bosnia_and_herzegovina.png'),
(30, '<lang:country_botswana>', 'images/flags/botswana.png'),
(31, '<lang:country_brazil>', 'images/flags/brazil.png'),
(32, '<lang:country_british_antarctic_territory>', 'images/flags/british_antarctic_territory.png'),
(33, '<lang:country_british_indian_ocean_territory>', 'images/flags/british_indian_ocean_territory.png'),
(34, '<lang:country_british_virgin_islands>', 'images/flags/british_virgin_islands.png'),
(35, '<lang:country_brunei>', 'images/flags/brunei.png'),
(36, '<lang:country_bulgaria>', 'images/flags/bulgaria.png'),
(37, '<lang:country_burkina_faso>', 'images/flags/burkina_faso.png'),
(38, '<lang:country_burundi>', 'images/flags/burundi.png'),
(39, '<lang:country_cambodia>', 'images/flags/cambodia.png'),
(40, '<lang:country_cameroon>', 'images/flags/cameroon.png'),
(41, '<lang:country_canada>', 'images/flags/canada.png'),
(42, '<lang:country_canary_islands>', 'images/flags/canary_islands.png'),
(43, '<lang:country_cape_verde>', 'images/flags/cape_verde.png'),
(44, '<lang:country_cayman_islands>', 'images/flags/cayman_islands.png'),
(45, '<lang:country_central_african_republic>', 'images/flags/central_african_republic.png'),
(46, '<lang:country_chad>', 'images/flags/chad.png'),
(47, '<lang:country_chile>', 'images/flags/chile.png'),
(48, '<lang:country_china>', 'images/flags/china.png'),
(49, '<lang:country_christmas_island>', 'images/flags/christmas_island.png'),
(50, '<lang:country_cocos_islands>', 'images/flags/cocos_islands.png'),
(51, '<lang:country_colombia>', 'images/flags/colombia.png'),
(52, '<lang:country_comoros>', 'images/flags/comoros.png'),
(53, '<lang:country_congo_democratic_republic>', 'images/flags/congo_democratic_republic.png'),
(54, '<lang:country_congo_republic>', 'images/flags/congo_republic.png'),
(55, '<lang:country_cook_islands>', 'images/flags/cook_islands.png'),
(56, '<lang:country_costa_rica>', 'images/flags/costa_rica.png'),
(57, '<lang:country_cote_divoire>', 'images/flags/cote_divoire.png'),
(58, '<lang:country_croatia>', 'images/flags/croatia.png'),
(59, '<lang:country_cuba>', 'images/flags/cuba.png'),
(60, '<lang:country_curacao>', 'images/flags/curacao.png'),
(61, '<lang:country_cyprus>', 'images/flags/cyprus.png'),
(62, '<lang:country_czech_republic>', 'images/flags/czech_republic.png'),
(63, '<lang:country_denmark>', 'images/flags/denmark.png'),
(64, '<lang:country_djibouti>', 'images/flags/djibouti.png'),
(65, '<lang:country_dominica>', 'images/flags/dominica.png'),
(66, '<lang:country_dominican_republic>', 'images/flags/dominican_republic.png'),
(67, '<lang:country_east_timor>', 'images/flags/east_timor.png'),
(68, '<lang:country_ecuador>', 'images/flags/ecuador.png'),
(69, '<lang:country_egypt>', 'images/flags/egypt.png'),
(70, '<lang:country_el_salvador>', 'images/flags/el_salvador.png'),
(71, '<lang:country_equatorial_guinea>', 'images/flags/equatorial_guinea.png'),
(72, '<lang:country_eritrea>', 'images/flags/eritrea.png'),
(73, '<lang:country_estonia>', 'images/flags/estonia.png'),
(74, '<lang:country_ethiopia>', 'images/flags/ethiopia.png'),
(75, '<lang:country_falkland_islands>', 'images/flags/falkland_islands.png'),
(76, '<lang:country_faroe_islands>', 'images/flags/faroe_islands.png'),
(77, '<lang:country_fiji>', 'images/flags/fiji.png'),
(78, '<lang:country_finland>', 'images/flags/finland.png'),
(79, '<lang:country_france>', 'images/flags/france.png'),
(80, '<lang:country_french_guiana>', 'images/flags/french_guiana.png'),
(81, '<lang:country_french_polynesia>', 'images/flags/french_polynesia.png'),
(82, '<lang:country_french_southern_and_antarctic_lands>', 'images/flags/french_southern_and_antarctic_lands.png'),
(83, '<lang:country_gabon>', 'images/flags/gabon.png'),
(84, '<lang:country_gambia>', 'images/flags/gambia.png'),
(85, '<lang:country_georgia>', 'images/flags/georgia.png'),
(86, '<lang:country_germany>', 'images/flags/germany.png'),
(87, '<lang:country_ghana>', 'images/flags/ghana.png'),
(88, '<lang:country_gibraltar>', 'images/flags/gibraltar.png'),
(89, '<lang:country_greece>', 'images/flags/greece.png'),
(90, '<lang:country_greenland>', 'images/flags/greenland.png'),
(91, '<lang:country_grenada>', 'images/flags/grenada.png'),
(92, '<lang:country_guadeloupe>', 'images/flags/guadeloupe.png'),
(93, '<lang:country_guam>', 'images/flags/guam.png'),
(94, '<lang:country_guatemala>', 'images/flags/guatemala.png'),
(95, '<lang:country_guernsey>', 'images/flags/guernsey.png'),
(96, '<lang:country_guinea>', 'images/flags/guinea.png'),
(97, '<lang:country_guinea_bissau>', 'images/flags/guinea_bissau.png'),
(98, '<lang:country_guyana>', 'images/flags/guyana.png'),
(99, '<lang:country_haiti>', 'images/flags/haiti.png'),
(100, '<lang:country_honduras>', 'images/flags/honduras.png'),
(101, '<lang:country_hong_kong>', 'images/flags/hong_kong.png'),
(102, '<lang:country_hungary>', 'images/flags/hungary.png'),
(103, '<lang:country_iceland>', 'images/flags/iceland.png'),
(104, '<lang:country_india>', 'images/flags/india.png'),
(105, '<lang:country_indonesia>', 'images/flags/indonesia.png'),
(106, '<lang:country_iran>', 'images/flags/iran.png'),
(107, '<lang:country_iraq>', 'images/flags/iraq.png'),
(108, '<lang:country_ireland>', 'images/flags/ireland.png'),
(109, '<lang:country_isle_of_man>', 'images/flags/isle_of_man.png'),
(110, '<lang:country_israel>', 'images/flags/israel.png'),
(111, '<lang:country_italy>', 'images/flags/italy.png'),
(112, '<lang:country_jamaica>', 'images/flags/jamaica.png'),
(113, '<lang:country_japan>', 'images/flags/japan.png'),
(114, '<lang:country_jersey>', 'images/flags/jersey.png'),
(115, '<lang:country_jordan>', 'images/flags/jordan.png'),
(116, '<lang:country_kazakhstan>', 'images/flags/kazakhstan.png'),
(117, '<lang:country_kenya>', 'images/flags/kenya.png'),
(118, '<lang:country_kiribati>', 'images/flags/kiribati.png'),
(119, '<lang:country_kosovo>', 'images/flags/kosovo.png'),
(120, '<lang:country_kuwait>', 'images/flags/kuwait.png'),
(121, '<lang:country_kyrgyzstan>', 'images/flags/kyrgyzstan.png'),
(122, '<lang:country_laos>', 'images/flags/laos.png'),
(123, '<lang:country_latvia>', 'images/flags/latvia.png'),
(124, '<lang:country_lebanon>', 'images/flags/lebanon.png'),
(125, '<lang:country_lesotho>', 'images/flags/lesotho.png'),
(126, '<lang:country_liberia>', 'images/flags/liberia.png'),
(127, '<lang:country_libya>', 'images/flags/libya.png'),
(128, '<lang:country_liechtenstein>', 'images/flags/liechtenstein.png'),
(129, '<lang:country_lithuania>', 'images/flags/lithuania.png'),
(130, '<lang:country_luxembourg>', 'images/flags/luxembourg.png'),
(131, '<lang:country_macau>', 'images/flags/macau.png'),
(132, '<lang:country_macedonia>', 'images/flags/macedonia.png'),
(133, '<lang:country_madagascar>', 'images/flags/madagascar.png'),
(134, '<lang:country_madeira>', 'images/flags/madeira.png'),
(135, '<lang:country_malawi>', 'images/flags/malawi.png'),
(136, '<lang:country_malaysia>', 'images/flags/malaysia.png'),
(137, '<lang:country_maldives>', 'images/flags/maldives.png'),
(138, '<lang:country_mali>', 'images/flags/mali.png'),
(139, '<lang:country_malta>', 'images/flags/malta.png'),
(140, '<lang:country_marshall_islands>', 'images/flags/marshall_islands.png'),
(141, '<lang:country_martinique>', 'images/flags/martinique.png'),
(142, '<lang:country_mauritania>', 'images/flags/mauritania.png'),
(143, '<lang:country_mauritius>', 'images/flags/mauritius.png'),
(144, '<lang:country_mayotte>', 'images/flags/mayotte.png'),
(145, '<lang:country_mexico>', 'images/flags/mexico.png'),
(146, '<lang:country_micronesia>', 'images/flags/micronesia.png'),
(147, '<lang:country_moldova>', 'images/flags/moldova.png'),
(148, '<lang:country_monaco>', 'images/flags/monaco.png'),
(149, '<lang:country_mongolia>', 'images/flags/mongolia.png'),
(150, '<lang:country_montenegro>', 'images/flags/montenegro.png'),
(151, '<lang:country_montserrat>', 'images/flags/montserrat.png'),
(152, '<lang:country_morocco>', 'images/flags/morocco.png'),
(153, '<lang:country_mozambique>', 'images/flags/mozambique.png'),
(154, '<lang:country_myanmar>', 'images/flags/myanmar.png'),
(155, '<lang:country_namibia>', 'images/flags/namibia.png'),
(156, '<lang:country_nauru>', 'images/flags/nauru.png'),
(157, '<lang:country_nepal>', 'images/flags/nepal.png'),
(158, '<lang:country_netherlands>', 'images/flags/netherlands.png'),
(159, '<lang:country_new_caledonia>', 'images/flags/new_caledonia.png'),
(160, '<lang:country_new_zealand>', 'images/flags/new_zealand.png'),
(161, '<lang:country_nicaragua>', 'images/flags/nicaragua.png'),
(162, '<lang:country_niger>', 'images/flags/niger.png'),
(163, '<lang:country_nigeria>', 'images/flags/nigeria.png'),
(164, '<lang:country_niue>', 'images/flags/niue.png'),
(165, '<lang:country_norfolk_island>', 'images/flags/norfolk_island.png'),
(166, '<lang:country_north_korea>', 'images/flags/north_korea.png'),
(167, '<lang:country_northern_mariana_islands>', 'images/flags/northern_mariana_islands.png'),
(168, '<lang:country_norway>', 'images/flags/norway.png'),
(169, '<lang:country_oman>', 'images/flags/oman.png'),
(170, '<lang:country_pakistan>', 'images/flags/pakistan.png'),
(171, '<lang:country_palau>', 'images/flags/palau.png'),
(172, '<lang:country_palestine>', 'images/flags/palestine.png'),
(173, '<lang:country_panama>', 'images/flags/panama.png'),
(174, '<lang:country_papua_new_guinea>', 'images/flags/papua_new_guinea.png'),
(175, '<lang:country_paraguay>', 'images/flags/paraguay.png'),
(176, '<lang:country_peru>', 'images/flags/peru.png'),
(177, '<lang:country_philippines>', 'images/flags/philippines.png'),
(178, '<lang:country_pitcairn_islands>', 'images/flags/pitcairn_islands.png'),
(179, '<lang:country_poland>', 'images/flags/poland.png'),
(180, '<lang:country_portugal>', 'images/flags/portugal.png'),
(181, '<lang:country_puerto_rico>', 'images/flags/puerto_rico.png'),
(182, '<lang:country_qatar>', 'images/flags/qatar.png'),
(183, '<lang:country_reunion>', 'images/flags/reunion.png'),
(184, '<lang:country_romania>', 'images/flags/romania.png'),
(185, '<lang:country_russia>', 'images/flags/russia.png'),
(186, '<lang:country_rwanda>', 'images/flags/rwanda.png'),
(187, '<lang:country_saint_barthelemy>', 'images/flags/saint_barthelemy.png'),
(188, '<lang:country_saint_helena_ascension_and_tristan_da_cunha>', 'images/flags/saint_helena_ascension_and_tristan_da_cunha.png'),
(189, '<lang:country_saint_kitts_and_nevis>', 'images/flags/saint_kitts_and_nevis.png'),
(190, '<lang:country_saint_lucia>', 'images/flags/saint_lucia.png'),
(191, '<lang:country_saint_martin>', 'images/flags/saint_martin.png'),
(192, '<lang:country_saint_pierre_and_miquelon>', 'images/flags/saint_pierre_and_miquelon.png'),
(193, '<lang:country_saint_vincent_and_the_grenadines>', 'images/flags/saint_vincent_and_the_grenadines.png'),
(194, '<lang:country_samoa>', 'images/flags/samoa.png'),
(195, '<lang:country_san_marino>', 'images/flags/san_marino.png'),
(196, '<lang:country_sao_tome_and_principe>', 'images/flags/sao_tome_and_principe.png'),
(197, '<lang:country_saudi_arabia>', 'images/flags/saudi_arabia.png'),
(198, '<lang:country_senegal>', 'images/flags/senegal.png'),
(199, '<lang:country_serbia>', 'images/flags/serbia.png'),
(200, '<lang:country_seychelles>', 'images/flags/seychelles.png'),
(201, '<lang:country_sierra_leone>', 'images/flags/sierra_leone.png'),
(202, '<lang:country_singapore>', 'images/flags/singapore.png'),
(203, '<lang:country_sint_maarten>', 'images/flags/sint_maarten.png'),
(204, '<lang:country_slovakia>', 'images/flags/slovakia.png'),
(205, '<lang:country_slovenia>', 'images/flags/slovenia.png'),
(206, '<lang:country_solomon_islands>', 'images/flags/solomon_islands.png'),
(207, '<lang:country_somalia>', 'images/flags/somalia.png'),
(208, '<lang:country_south_africa>', 'images/flags/south_africa.png'),
(209, '<lang:country_south_georgia_and_the_south_sandwich_islands>', 'images/flags/south_georgia_and_the_south_sandwich_islands.png'),
(210, '<lang:country_south_korea>', 'images/flags/south_korea.png'),
(211, '<lang:country_south_sudan>', 'images/flags/south_sudan.png'),
(212, '<lang:country_spain>', 'images/flags/spain.png'),
(213, '<lang:country_sri_lanka>', 'images/flags/sri_lanka.png'),
(214, '<lang:country_sudan>', 'images/flags/sudan.png'),
(215, '<lang:country_suriname>', 'images/flags/suriname.png'),
(216, '<lang:country_swaziland>', 'images/flags/swaziland.png'),
(217, '<lang:country_sweden>', 'images/flags/sweden.png'),
(218, '<lang:country_switzerland>', 'images/flags/switzerland.png'),
(219, '<lang:country_syria>', 'images/flags/syria.png'),
(220, '<lang:country_taiwan>', 'images/flags/taiwan.png'),
(221, '<lang:country_tajikistan>', 'images/flags/tajikistan.png'),
(222, '<lang:country_tanzania>', 'images/flags/tanzania.png'),
(223, '<lang:country_thailand>', 'images/flags/thailand.png'),
(224, '<lang:country_togo>', 'images/flags/togo.png'),
(225, '<lang:country_tokelau>', 'images/flags/tokelau.png'),
(226, '<lang:country_tonga>', 'images/flags/tonga.png'),
(227, '<lang:country_trinidad_and_tobago>', 'images/flags/trinidad_and_tobago.png'),
(228, '<lang:country_tunisia>', 'images/flags/tunisia.png'),
(229, '<lang:country_turkey>', 'images/flags/turkey.png'),
(230, '<lang:country_turkmenistan>', 'images/flags/turkmenistan.png'),
(231, '<lang:country_turks_and_caicos_islands>', 'images/flags/turks_and_caicos_islands.png'),
(232, '<lang:country_tuvalu>', 'images/flags/tuvalu.png'),
(233, '<lang:country_uganda>', 'images/flags/uganda.png'),
(234, '<lang:country_ukraine>', 'images/flags/ukraine.png'),
(235, '<lang:country_united_arab_emirates>', 'images/flags/united_arab_emirates.png'),
(236, '<lang:country_united_kingdom>', 'images/flags/united_kingdom.png'),
(237, '<lang:country_united_states>', 'images/flags/united_states.png'),
(238, '<lang:country_united_states_minor_outlying_islands>', 'images/flags/united_states_minor_outlying_islands.png'),
(239, '<lang:country_united_states_virgin_islands>', 'images/flags/united_states_virgin_islands.png'),
(240, '<lang:country_uruguay>', 'images/flags/uruguay.png'),
(241, '<lang:country_uzbekistan>', 'images/flags/uzbekistan.png'),
(242, '<lang:country_vanuatu>', 'images/flags/vanuatu.png'),
(243, '<lang:country_vatican_city>', 'images/flags/vatican_city.png'),
(244, '<lang:country_venezuela>', 'images/flags/venezuela.png'),
(245, '<lang:country_vietnam>', 'images/flags/vietnam.png'),
(246, '<lang:country_wallis_and_futuna_islands>', 'images/flags/wallis_and_futuna_islands.png'),
(247, '<lang:country_western_sahara>', 'images/flags/western_sahara.png'),
(248, '<lang:country_yemen>', 'images/flags/yemen.png'),
(249, '<lang:country_zambia>', 'images/flags/zambia.png'),
(250, '<lang:country_zimbabwe>', 'images/flags/zimbabwe.png')");

	update_countries();
}

// Checks to make sure plugin is installed
function country_is_installed()
{
	global $db;
	if($db->field_exists("country", "users"))
	{
		return true;
	}
	return false;
}

// This function runs when the plugin is uninstalled.
function country_uninstall()
{
	global $db, $cache;

	if($db->table_exists("countries"))
	{
		$db->drop_table("countries");
	}

	if($db->field_exists("country", "users"))
	{
		$db->drop_column("users", "country");
	}

	$cache->delete('countries');
}

// This function runs when the plugin is activated.
function country_activate()
{
	global $db;
	$query = $db->simple_select("settinggroups", "gid", "name='member'");
	$gid = $db->fetch_field($query, "gid");

	// Insert settings
	$insertarray = array(
		'name' => 'countryrequired',
		'title' => 'Require Country Field',
		'description' => 'If you wish to make the country field required, set this option to yes.',
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 39,
		'gid' => (int)$gid
	);
	$db->insert_query("settings", $insertarray);

	rebuild_settings();

	// Insert templates
	$insert_array = array(
		'title'		=> 'postbit_country',
		'template'	=> $db->escape_string('<br />{$lang->country}: <img src="{$country[\'flag\']}" alt="{$country[\'name\']}" title="{$country[\'name\']}" />'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'global_country',
		'template'	=> $db->escape_string('<img src="{$country[\'flag\']}" alt="{$country[\'name\']}" title="{$country[\'name\']}" />'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'usercp_profile_country_required',
		'template'	=> $db->escape_string('<tr>
	<td>
		<span>{$lang->country}</span>:
		<br />
		<span class="smalltext">{$lang->country_description}</span>
	</td>
</tr>
<tr>
	<td>
		<select name="country">
			<option value="">{$lang->not_specified}</option>
			{$countryoptions}
		</select>
	</td>
</tr>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'usercp_profile_country_optional',
		'template'	=> $db->escape_string('<tr>
	<td colspan="3">
		<span class="smalltext">{$lang->your_country}</span>
	</td>
</tr>
<tr>
	<td colspan="3">
		<select name="country">
			<option value="">{$lang->not_specified}</option>
			{$countryoptions}
		</select>
	</td>
</tr>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'usercp_profile_country_country',
		'template'	=> $db->escape_string('<option value="{$country[\'cid\']}"{$selected}>{$countryname}</option>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'member_register_country',
		'template'	=> $db->escape_string('<br />
<fieldset class="trow2">
	<legend><strong>{$lang->country}</strong></legend>
	<table cellspacing="0" cellpadding="{$theme[\'tablespace\']}">
		<tr>
			<td colspan="2"><span class="smalltext">{$please_select_country}</span></td>
		</tr>
		<tr>
			<td colspan="2">
				<select name="country">
					<option value="">{$lang->not_specified}</option>
					{$countryoptions}
				</select>
			</td>
		</tr>
	</table>
</fieldset>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'member_register_country_country',
		'template'	=> $db->escape_string('<option value="{$country[\'cid\']}">{$countryname}</option>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}{$post[\'usercountry\']}');
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}{$post[\'usercountry\']}');
	find_replace_templatesets("memberlist_user", "#".preg_quote('{$user[\'profilelink\']}')."#i", '{$user[\'profilelink\']}{$user[\'usercountry\']}');
	find_replace_templatesets("member_register", "#".preg_quote('{$requiredfields}')."#i", '{$countryfield}{$requiredfields}');
	find_replace_templatesets("member_profile", "#".preg_quote('</strong></span>')."#i", '</strong></span>{$usercountry}');
	find_replace_templatesets("usercp_profile", "#".preg_quote('{$requiredfields}')."#i", '{$requiredcountryfield}{$requiredfields}');
	find_replace_templatesets("usercp_profile", "#".preg_quote('</table>
</fieldset>
{$customfields}')."#i", '{$optionalcountryfield}</table>
</fieldset>
{$customfields}');
}

// This function runs when the plugin is deactivated.
function country_deactivate()
{
	global $db;
	$db->delete_query("settings", "name='countryrequired'");
	$db->delete_query("templates", "title IN('postbit_country','global_country','usercp_profile_country_required','usercp_profile_country_optional','usercp_profile_country_country','member_register_country','member_register_country_country')");
	rebuild_settings();

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'usercountry\']}')."#i", '', 0);
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'usercountry\']}')."#i", '', 0);
	find_replace_templatesets("memberlist_user", "#".preg_quote('{$user[\'usercountry\']}')."#i", '', 0);
	find_replace_templatesets("member_register", "#".preg_quote('{$countryfield}')."#i", '', 0);
	find_replace_templatesets("member_profile", "#".preg_quote('{$usercountry}')."#i", '', 0);
	find_replace_templatesets("usercp_profile", "#".preg_quote('{$requiredcountryfield}')."#i", '', 0);
	find_replace_templatesets("usercp_profile", "#".preg_quote('{$optionalcountryfield}')."#i", '', 0);
}

// Add country flag to postbit
function country_run($post)
{
	global $lang, $templates, $cache, $country;
	$lang->load("country");
	$country_cache = $cache->read("countries");

	$post['usercountry'] = '';
	if(!empty($post['country']))
	{
		$post['country'] = (int)$post['country'];
		$country = $country_cache[$post['country']];

		$country['name'] = $lang->parse($country['name']);

		eval("\$post['usercountry'] = \"".$templates->get('postbit_country')."\";");
	}

	return $post;
}

// Add country flag to profile
function country_profile()
{
	global $lang, $templates, $cache, $usercountry, $memprofile;
	$lang->load("country");
	$country_cache = $cache->read("countries");

	$usercountry = '';
	if(!empty($memprofile['country']))
	{
		$memprofile['country'] = (int)$memprofile['country'];
		$country = $country_cache[$memprofile['country']];

		$country['name'] = $lang->parse($country['name']);

		eval("\$usercountry = \"".$templates->get("global_country")."\";");
	}
}

// Editing country field in profile
function country_select()
{
	global $db, $mybb, $lang, $templates, $requiredcountryfield, $optionalcountryfield, $user, $cache;
	$lang->load("country");
	$country_cache = $cache->read("countries");

	$countryoptions = '';
	foreach($country_cache as $cid => $country)
	{
		$countryname = $lang->parse($country['name']);

		$selected = '';
		if($user['country'] == $country['cid'])
		{
			$selected = "selected=\"selected\"";
		}

		eval("\$countryoptions .= \"".$templates->get("usercp_profile_country_country")."\";");
	}

	$requiredcountryfield = $optionalcountryfield = '';
	if($mybb->settings['countryrequired'] == 1)
	{
		eval("\$requiredcountryfield = \"".$templates->get("usercp_profile_country_required")."\";");
	}
	else
	{
		eval("\$optionalcountryfield = \"".$templates->get("usercp_profile_country_optional")."\";");
	}
}

function country_do_select()
{
	global $db, $mybb;

	$update_country = array(
		"country" => $mybb->get_input('country', MyBB::INPUT_INT)
	);
	$db->update_query("users", $update_country, "uid='".(int)$mybb->user['uid']."'");
}

// Country on registration
function country_register()
{
	global $mybb, $lang, $templates, $theme, $cache, $countryfield;
	$lang->load("country");
	$country_cache = $cache->read("countries");

	if($mybb->settings['countryrequired'] != 1)
	{
		$please_select_country = $lang->country_description;
	}
	else
	{
		$please_select_country = $lang->country_description_required;
	}

	$countryoptions = $countryfield = '';
	foreach($country_cache as $cid => $country)
	{
		$countryname = $lang->parse($country['name']);

		eval("\$countryoptions .= \"".$templates->get("member_register_country_country")."\";");
	}

	eval("\$countryfield = \"".$templates->get("member_register_country")."\";");
}

// Validate country
function country_validate($user)
{
	global $mybb, $lang, $cache;
	$lang->load("country", true);
	$country_cache = $cache->read("countries");

	$cid = $mybb->get_input('country', MyBB::INPUT_INT);

	if($mybb->settings['countryrequired'] == 1 && $cid == 0)
	{
		$user->set_error($lang->missing_country);
	}

	if($cid != 0)
	{
		$country = $country_cache[$cid];

		if(!$country)
		{
			$user->set_error($lang->invalid_country);
		}
	}

	return $user;
}

function country_do_register($user)
{
	global $db, $mybb;

	$update_country = array(
		"country" => $mybb->get_input('country', MyBB::INPUT_INT)
	);
	$db->update_query("users", $update_country, "uid ='{$user->uid}'");

	return $user;
}

// Show flag on member list
function country_memberlist($user)
{
	global $lang, $cache, $templates;
	$lang->load("country");
	$country_cache = $cache->read("countries");

	$user['usercountry'] = '';
	if(!empty($user['country']))
	{
		$user['country'] = (int)$user['country'];
		$country = $country_cache[$user['country']];

		$country['name'] = $lang->parse($country['name']);

		eval("\$user['usercountry'] = \"".$templates->get("global_country")."\";");
	}

	return $user;
}

// Admin CP user editing
function country_user_editing($above)
{
	global $mybb, $lang, $form, $cache;
	$lang->load("country", true);

	if($above['title'] == $lang->other_options && $lang->other_options)
	{
		$country_cache = $cache->read("countries");
		$options[0] = "&nbsp";
		foreach($country_cache as $cid => $country)
		{
			$country['name'] = $lang->parse($country['name']);
			$options[$country['cid']] = $country['name'];
		}

		$above['content'] .="<div class=\"user_settings_bit\"><label for=\"country\">{$lang->country}:</label><br />".$form->generate_select_box('country', $options, $mybb->input['country'], array('id' => 'country'))."</div>";
	}

	return $above;
}

// Admin CP user editing
function country_user_editing_commit()
{
	global $db, $mybb, $user;

	$update_user = array(
		"country" => $mybb->get_input('country', MyBB::INPUT_INT)
	);
	$db->update_query("users", $update_user, "uid='{$user['uid']}'");
}

// Add country manage section in Admin CP
function country_admin_menu($sub_menu)
{
	global $lang;
	$lang->load("config_countries");

	$sub_menu['230'] = array('id' => 'countries', 'title' => $lang->countries, 'link' => 'index.php?module=config-countries');

	return $sub_menu;
}

function country_admin_action_handler($actions)
{
	$actions['countries'] = array('active' => 'countries', 'file' => 'countries.php');

	return $actions;
}

function country_admin_permissions($admin_permissions)
{
	global $lang;
	$lang->load("config_countries");

	$admin_permissions['countries'] = $lang->can_manage_countries;

	return $admin_permissions;
}

// Admin Log display
function country_admin_adminlog($plugin_array)
{
	global $lang;
	$lang->load("config_countries");

	return $plugin_array;
}

/**
 * Update the countries cache.
 *
 */
function update_countries()
{
	global $db, $cache;

	$countries = array();

	$query = $db->simple_select("countries", "cid, name, flag", "", array('order_by' => 'name', 'order_dir' => 'asc'));
	while($country = $db->fetch_array($query))
	{
		$countries[$country['cid']] = $country;
	}

	$cache->update("countries", $countries);
}

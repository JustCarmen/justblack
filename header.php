<?php
// Header for the JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
// Copyright (C) 2012 JustCarmen.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id: header.php 2012-10-24 JustCarmen $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

ob_start();
// This theme uses the jQuery “colorbox” plugin to display images
$this
	->addExternalJavascript(WT_JQUERY_COLORBOX_URL)
	->addExternalJavascript(WT_JQUERY_WHEELZOOM_URL);
	
echo
	'<!DOCTYPE html>',
	'<html ', WT_I18N::html_markup(), '>',
	'<head>';
	
	// use IE8 for medialist page if browser is IE 9 or 10
	if (basename($_SERVER['PHP_SELF']) == 'medialist.php' && (strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 9") || strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 10"))) {
		echo '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />';
	}
	
echo
	'<meta charset="utf-8">',
	'<title>', htmlspecialchars($title), '</title>',
	header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL),
	'<link rel="icon" href="', WT_THEME_URL, 'favicon.png" type="image/png">',
	'<link rel="stylesheet" type="text/css" href="', WT_THEME_URL, 'css/jquery-ui/jquery-ui-1.10.3.custom.css">',
	'<link rel="stylesheet" type="text/css" href="', WT_THEME_URL, 'css/colorbox/colorbox.css', '">',	
	'<link rel="stylesheet" type="text/css" href="', WT_THEME_URL, 'css/style.css', '">';

switch ($BROWSERTYPE) {
case 'msie':
	echo '<link type="text/css" rel="stylesheet" href="', WT_THEME_URL, 'css/', $BROWSERTYPE, '.css">';
	break;
}	

if ($view=='simple') {
	// Popup windows need space for the save/close buttons
	echo '<style>body{margin-bottom:50px;}</style>';
}

echo
	'</head>',
	'<body id="body">';

if ($view!='simple') { // no headers for dialogs
		// begin header section
		echo getJBheader();	
		
		echo
		'<div id="optionsmenu">',		
			'<div id="fav-menu"><ul class="dropdown">', WT_MenuBar::getFavoritesMenu(), '</ul></div>',
			'<div id="search-menu">', getJBSearch(), '</div>', getJBFlags(),
		'</div>',
		'<div class="clearfloat"></div>';
	
		// Print the TopMenu
		echo 
		'<div id="topMenu">'.getJBTopMenu().'</div>',			
	    '<div class="divider"></div>'.
        WT_FlashMessages::getHtmlMessages(); // Feedback from asynchronous actions
		
	// JustBlack
	$this->addExternalJavascript(WT_THEME_URL.'js/justblack.js');	
	
	// activate ColorBox
	getColorBox();	
}

echo $javascript, '<div id="content">';


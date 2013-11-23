<?php

// Options for the JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2012 JustCarmen.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
// $Id: options.php 2013-04-01 JustCarmen $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$JB_SETTINGS = justblack_theme_options_WT_Module::JBSettings();

if(!empty($JB_SETTINGS)) {
	if ($JB_SETTINGS['TREETITLE'] == 1) setThemeOption('treetitle');
	if ($JB_SETTINGS['HEADERIMG'] == 'custom') setThemeOption('headerimg');
}

function setThemeOption($option) {
	global $controller;
			
	switch($option) {
		case 'treetitle':
			$treetitle = str_replace('"','\"', WT_TREE_TITLE);
			$controller->addInlineJavascript('
				jQuery("#header").prepend("<div class=\"title\" dir=\"auto\"><h1>'.$treetitle.'</h1></div>");	
				
				// when the title is spread over multiple lines we need to recalculate the top of the title div.
				var titleHeight = jQuery("#header .title").height();				
				var lines = titleHeight / 22;							// the height of 1 line = 22
				if (lines > 4) {lines = 4}; 							// max 4 lines, more lines won\'t fit in the image.					
				var top = 110 - (22 * (lines -1)) + "px"; 				// by default the top of the div is positioned at 110px.
				jQuery("#header .title").css("top", top);		
			');			
		break;
		case 'headerimg':
			$path = WT_THEME_DIR.'css/images/';
			$exts = array('png','jpg', 'gif');
			foreach($exts as $ext) {
				if(file_exists($path.'custom_header.'.$ext)){
					$headerImg = $path.'custom_header.'.$ext;
					$controller->addInlineJavascript('
						jQuery("#header").css({"background-image":"url('.$headerImg.')"});		
					');	
				}
			}
		break;	
	}
}

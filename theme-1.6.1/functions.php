<?php

// Functions for the JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 JustCarmen.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// This theme comes with an optional module to set a few theme options
function getThemeOption($setting) {
	if (array_key_exists('justblack_theme_options', WT_Module::getActiveModules())) {
		$module = new justblack_theme_options_WT_Module;
		return $module->options($setting);
	}
}

// variables needed in justblack.js
function getJBScriptVars() {
	global $controller;
	$controller->addInlineJavascript('
			// JustBlack Theme variables
			var WT_SERVER_NAME	= "' . WT_SERVER_NAME . '";
			var WT_SCRIPT_PATH	= "' . WT_SCRIPT_PATH . '";
			var WT_CSS_URL		= "' . WT_CSS_URL . '";
			var JB_THEME_URL	= "' . JB_THEME_URL . '";
			var JB_COLORBOX_URL	= "' . JB_COLORBOX_URL . '";
			var WT_TREE_TITLE	= "' . strip_tags(WT_TREE_TITLE) . '";
			var authID	        = "' . Auth::id() . '";
	', WT_Controller_Base::JS_PRIORITY_HIGH);
}

// Theme setting for the header section
function getJBheader() {
	switch (getThemeOption('header')) {
		case '1':
			$image = WT_DATA_DIR . getThemeOption('image');
			if (file_exists($image)) {
				$bg					 = file_get_contents($image); // The data dir is a protected directory.
				$type				 = @getimagesize($image);
				$header_image_style	 = 'background-image:url(data:' . $type['mime'] . ';base64,' . base64_encode($bg) . '); height: ' . getThemeOption('headerheight') . 'px';
				$header_menu_style	 = 'height: ' . getThemeOption('headerheight') . 'px';
			} else {
				$header_image_style	 = 'background-image:url(' . WT_CSS_URL . 'images/header.jpg)';
				$header_menu_style	 = '';
			}
			break;
		case '2':
			$header_image_style	 = 'height: ' . getThemeOption('headerheight') . 'px';
			$header_menu_style	 = $header_image_style;
			break;
		default:
			$header_image_style	 = 'background-image:url(' . WT_CSS_URL . 'images/header.jpg)';
			$header_menu_style	 = '';
			break;
	}

	switch (getThemeOption('treetitle')) {
		case '0':
			$title		 = '';
			break;
		case '1':
			$pos		 = getThemeOption('titlepos');
			$posV		 = 'top:' . $pos['V']['size'] . $pos['V']['fmt'];
			$posH		 = $pos['H']['pos'] . ':' . $pos['H']['size'] . $pos['H']['fmt'];
			$posH		 = $pos['H']['pos'] == 'left' ? 'right:auto;' . $posH : 'left:auto;' . $posH;
			$font_size	 = 'font-size:' . getThemeOption('titlesize') . 'px';
			$title		 = '<h1 style="' . $font_size . ';' . $posV . ';' . $posH . '"><a href="index.php">' . WT_TREE_TITLE . '</a></h1>';
			break;
		default:
			$title		 = '<h1><a href="index.php">' . WT_TREE_TITLE . '</a></h1>';
			break;
	}

	$html = '	<div id="header-image" style="' . $header_image_style . '"></div>
				<div id="header-menu" style="' . $header_menu_style . '">' . $title . '
					<div id="extra-menu">
						<ul class="dropdown">' . WT_MenuBar::getThemeMenu();
	if (!getThemeOption('flags') || getThemeOption('flags') == 0) {
		$html .= WT_MenuBar::getLanguageMenu();
	}
	$html .= '			</ul></div>
					<div id="login-menu">' . getJBLoginMenu() . '</div>
				</div>';

	return $html;
}

// Menus
function getJBTopMenu() {
	global $controller, $SEARCH_SPIDER;

	$menus = getThemeOption('menu');

	if ($menus && WT_GED_ID && !$SEARCH_SPIDER) {
		$jb_controller	 = new justblack_theme_options_WT_Module;
		$menus			 = $jb_controller->checkModule($menus);
		$list			 = null;
		foreach ($menus as $menu) {
			$label		 = $menu['label'];
			$sort		 = $menu['sort'];
			$function	 = $menu['function'];
			if ($sort > 0) {
				if ($function == 'getModuleMenu') {
					$module		 = $label . '_WT_Module';
					$modulemenu	 = new $module;
					$item		 = $modulemenu->getMenu();
				} elseif ($label == 'compact') {
					$item = $jb_controller->$function();
				} elseif ($label == 'media') {
					$item = $jb_controller->$function();
					// hide the original submenu item
					$controller->addInlineJavascript('
						jQuery("li#menu-list-obje").hide();
					');
				} else {
					if (method_exists('WT_MenuBar', $function)) {
						$item = WT_MenuBar::$function();
					}
				}
				$list[] = $item;
			}
		}
		$output = implode('', $list);
	} else {
		$output = WT_MenuBar::getGedcomMenu() .
			WT_MenuBar::getMyPageMenu() .
			WT_MenuBar::getChartsMenu() .
			WT_MenuBar::getListsMenu() .
			WT_MenuBar::getCalendarMenu() .
			WT_MenuBar::getReportsMenu() .
			WT_MenuBar::getSearchMenu() .
			implode('', WT_MenuBar::getModuleMenus());
	}
	return '<ul id="main-menu">' . $output . '</ul>';
}

function getJBSearch() {
	$searchform = '	<form action="search.php" method="post">
					<input type="hidden" name="action" value="general">
					<input type="hidden" name="topsearch" value="yes">
					<input type="search" name="query" size="20" placeholder="' . WT_I18N::translate('Search') . '">
					<input type="image" class="searchbtn" src="' . WT_CSS_URL . 'images/buttons/search_go.png" alt="' . WT_I18N::translate('Search') . '" title="' . WT_I18N::translate('Search') . '">
					</form>';
	return $searchform;
}

function getJBFlags() {
	if (getThemeOption('flags') == 1) {
		$menu = WT_MenuBar::getLanguageMenu();

		if ($menu && $menu->getSubmenus()) {
			$output = '<div id="lang-menu"><ul>';
			foreach ($menu->getSubmenus() as $submenu) {
				if ($submenu) {
					$lang = explode('-', $submenu->getId());
					$class='';
					if (WT_LOCALE == $lang[2]) {
						$class = ' class="lang-active" ';
					}
					$output .= '<li id="' . $submenu->getId() . '"' . $class . 'title="' . $submenu->getLabel() . '">
								<a href="' . $submenu->getLink() . '"></a></li>';
				}
			}
			$output .= '</ul></div>';
			return $output;
		}
	}
}

function getJBLoginMenu() {
	if (Auth::check()) {
		$output = '<a class="link" href="edituser.php">' . WT_I18N::translate('Logged in as ') . WT_Filter::escapeHtml(WT_USER_NAME) . '</a> | ';
		if (WT_USER_CAN_ACCEPT && exists_pending_change()) {
			$output .= '<a class="link" href="#" onclick="window.open(\'edit_changes.php\', \'_blank\', chan_window_specs); return false;">' . WT_I18N::translate('Pending changes') . '</a>&nbsp;|&nbsp;';
		}
		$output .= logout_link();
	} else {
		$output = login_link();
	}
	return $output;
}

// Extended thumbnails - code original from library/WT/Media.php line 391.
// Called from  library/WT/Person.php function displayImage() from line 300.
// Thumbnails for personbox template
// Display the prefered image for this individual.
// Use an icon if no image is available.
function getJBThumb($person, $max_thumbsize, $square = '') {
	global $USE_SILHOUETTE;

	$media = $person->findHighlightedMedia();
	if ($media) {
		$mediasrc	 = $media->getServerFilename();
		if (file_exists($mediasrc) && $data		 = getimagesize($mediasrc)) { // extra check to be sure the thumb can be created.
			// Thumbnail exists - use it.
			if ($media->isExternal()) {
				// Use an icon
				$mime_type	 = str_replace('/', '-', $media->mimeType());
				$image		 = '<i' .
					' dir="' . 'auto' . '"' . // For the tool-tip
					' class="' . 'icon-mime-' . $mime_type . '"' .
					' title="' . strip_tags($media->getFullName()) . '"' .
					'></i>';
			} else {
				// Create a thumbnail image
				$type = $media->mimeType();
				if ($type == 'image/jpeg' || $type == 'image/png') {

					if (!list($width_orig, $height_orig) = @getimagesize($mediasrc)) {
						return $noThumb = true;
					}

					switch ($type) {
						case 'image/jpeg':
							$imagesrc	 = @imagecreatefromjpeg($mediasrc);
							break;
						case 'image/png':
							$imagesrc	 = @imagecreatefrompng($mediasrc);
							break;
					}

					$ratio_orig	 = $width_orig / $height_orig;
					$thumbwidth	 = $thumbheight = $max_thumbsize;

					if ($square == true) {
						if ($thumbwidth / $thumbheight > $ratio_orig) {
							$new_height	 = $thumbwidth / $ratio_orig;
							$new_width	 = $thumbwidth;
						} else {
							$new_width	 = $thumbheight * $ratio_orig;
							$new_height	 = $thumbheight;
						}
					} else {
						if ($width_orig > $height_orig) {
							$new_height	 = $thumbheight / $ratio_orig;
							$new_width	 = $thumbwidth;
						} elseif ($height_orig > $width_orig) {
							$new_width	 = $thumbheight * $ratio_orig;
							$new_height	 = $thumbheight;
						} else {
							$new_width	 = $thumbwidth;
							$new_height	 = $thumbheight;
						}
					}

					$process = imagecreatetruecolor(round($new_width), round($new_height));
					imagecopyresampled($process, $imagesrc, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
					$square == true ? $thumb	 = imagecreatetruecolor($thumbwidth, $thumbheight) : $thumb	 = imagecreatetruecolor($new_width, $new_height);
					imagecopyresampled($thumb, $process, 0, 0, 0, 0, $thumbwidth, $thumbheight, $thumbwidth, $thumbheight);

					imagedestroy($process);
					imagedestroy($imagesrc);

					ob_start(); imagejpeg($thumb, null, 80); $thumb	 = ob_get_clean();
					$src	 = 'data:image/jpeg;base64,' . base64_encode($thumb);

					$image = '<img' .
						' dir="' . 'auto' . '"' . // For the tool-tip
						' src="' . $src . '"' .
						' alt="' . strip_tags($media->getFullName()) . '"' .
						' title="' . strip_tags($media->getFullName()) . '"' .
						'>';
				} else {
					$src = $media->getHtmlUrlDirect('thumb');
				}

				$image = '<img' .
					' dir="' . 'auto' . '"' . // For the tool-tip
					' src="' . $src . '"' .
					' alt="' . strip_tags($media->getFullName()) . '"' .
					' title="' . strip_tags($media->getFullName()) . '"' .
					'>';

				return
					'<a' .
					' class="' . 'gallery' . '"' .
					' href="' . $media->getHtmlUrlDirect('main') . '"' .
					' type="' . $media->mimeType() . '"' .
					' data-obje-url="' . $media->getHtmlUrl() . '"' .
					' data-obje-note="' . htmlspecialchars($media->getNote()) . '"' .
					' data-title="' . WT_Filter::escapeHtml($media->getFullName()) . '"' .
					'>' . $image . '</a>';
			}
		} else { $noThumb = true; }
	} else { $noThumb = true; }

	if ($noThumb == true) {
		if ($USE_SILHOUETTE) {
			return '<i class="icon-silhouette-' . $person->getSex() . '"></i>';
		} else {
			return '';
		}
	}
}

function getJBMessageTable() {
	global $controller;
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			function jb_expand_layer(sid) {
				var obj = jQuery("#"+sid+"_img");
				if (obj.hasClass("icon-plus")) {
					obj.removeClass("icon-plus").addClass("icon-minus");
					var $class = obj.parents("tr").attr("class");
					obj.parents("tr").after("<tr class=\"" + $class + "\"><td class=\"wrap\" colspan=\"4\">" + jQuery("#"+sid).html());
				} else {
					obj.removeClass("icon-minus").addClass("icon-plus");
					obj.parents("tr").next().remove();
				}
				return false;
			}

			var dataTable = jQuery(".user_messages_block");

			dataTable.removeClass("small_inner_block");
			dataTable.find("table").removeClass("list_table");
			dataTable.find("td").removeClass("list_value_wrap").addClass("wrap");
			dataTable.find("table tr:first").wrap("<thead>");
			dataTable.find("tbody").before(jQuery(".user_messages_block thead"));
			dataTable.find("tbody tr:odd").each(function(){
				jQuery(this).find("div[id^=message]").appendTo("body");
				jQuery(this).remove();
			});
			dataTable.find("tr:first").children("td").replaceWith(function(i, html) {
        		return "<th>" + html.replace(":", "") + "</th>";
      		});
			dataTable.find("th:first a").replaceWith("<input type=\"checkbox\" name=\"select_all\" style=\"vertical-align:middle;margin:0 3px\">");
			dataTable.find("th:first br").remove();

			dataTable.on("click", "input[name=select_all]", function(){
				if (jQuery(this).is(":checked") == true) {
					jQuery("input[id^=cb_message]").prop("checked", true);
				} else {
					jQuery("input[id^=cb_message]").prop("checked", false);
				}
			});

			dataTable.find("a[onclick*=expand_layer]").each(function(){
				jQuery(this).attr("onclick",function(index,attr){
					return attr.replace("expand_layer", "jb_expand_layer");
				});
			});

			dataTable.find("table").dataTable({
				dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				autoWidth:false,
				processing: true,
				filter: true,
                                sort: false,
				columns: [
					/* 0-Delete */          {class: "center"},
					/* 1-Subject */         {},
					/* 2-Date_send */       {},
					/* 3-User - email */	{}
				],
				pageLength: 10,
				pagingType: "full_numbers"
			});
		');
}

function getJBClippingsTable() {
	global $controller;
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			var dataTable = jQuery("table#mycart");
			dataTable.find("tr:first").wrap("<thead>"); dataTable.find("tbody").before(jQuery("thead"));
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			dataTable.dataTable({
				dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				autoWidth:false,
				processing: true,
				filter: true,
				columns: [
					/* 0-Name/Description */	{},
					/* 1-Delete */				{sortable: false, class: "center"}
				],
				pageLength: 10,
				pagingType: "full_numbers"
			});
		');
}

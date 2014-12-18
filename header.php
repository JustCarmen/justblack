<?php
// Header for the JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
// Copyright (C) 2014 JustCarmen.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$this
	// This theme uses the jQuery “colorbox” plugin to display images
	->addExternalJavascript(WT_JQUERY_COLORBOX_URL)
	->addExternalJavascript(WT_JQUERY_WHEELZOOM_URL)
	// JustBlack
	->addExternalJavascript(JB_THEME_URL . 'jquery-ui.min.js')
	->addExternalJavascript(JB_THEME_URL . 'justblack.js')
	->addExternalJavascript(JB_COLORBOX_URL . 'justblack.colorbox.js');

// extra dataTables
if (WT_SCRIPT_NAME == 'index.php') getJBMessageTable();
if (WT_Filter::get('mod') == 'clippings') getJBClippingsTable();

?>
<!DOCTYPE html>
<html <?php echo WT_I18N::html_markup(); ?>>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php echo header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL) ?>
	<title><?php echo WT_Filter::escapeHtml($title) ?></title>
	<link rel="icon" href="<?php echo WT_CSS_URL; ?>favicon.png" type="image/png">
	<link rel="stylesheet" type="text/css" href="<?php echo JB_JQUERY_UI_CSS; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo JB_COLORBOX_URL; ?>colorbox.css">
	<link rel="stylesheet" type="text/css" href="<?php echo WT_CSS_URL; ?>style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo WT_CSS_URL; ?>treeview.css">
	<!--[if IE 8]>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
	<![endif]-->
</head>

<?php
if ($view=='simple') {
	// Popup windows need space for the save/close buttons
	echo '<style>body{margin-bottom:50px;}</style>';
}
?>

<body id="body">
	<?php if ($view!='simple'): ?>
	<?php getJBScriptVars(); ?>
	<header>
		<?php echo getJBheader(); ?>
		<div id="optionsmenu">
			<div id="fav-menu">
				<ul class="dropdown"><?php echo WT_MenuBar::getFavoritesMenu(); ?></ul>
			</div>
			<div id="search-menu"><?php echo getJBSearch(); ?></div>'
			<?php echo getJBFlags(); ?>
		</div>
		<div class="clearfloat"></div>
		<div id="topMenu"><?php echo getJBTopMenu(); ?></div>
		<div class="divider"></div>
	</header>
	<?php endif; ?>
	
	<?php echo WT_FlashMessages::getHtmlMessages() ?>
	<main id="content">

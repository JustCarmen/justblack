<?php
/**
 * JustBlack Theme
 *
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * Copyright (C) 2016 JustCarmen
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace JustCarmen\WebtreesAddOns\JustBlack;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Theme\AbstractTheme;
use Fisharebest\Webtrees\Theme\ThemeInterface;
use JustCarmen\WebtreesAddOns\FancyImagebar\FancyImagebarClass;

class JustBlackTheme extends AbstractTheme implements ThemeInterface {

	/** @var string the location of this theme */
	private $theme_dir;

	/** @var string the location of the jquery-ui files */
	private $jquery_ui_url;

	/** @var string the location of the colorbox files */
	private $colorbox_url;

	/** {@inheritdoc} */
	public function assetUrl() {
		return 'themes/justblack/css-1.7.4/';
	}

	/** {@inheritdoc} */
	public function bodyHeader() {
		return
			'<body>' .
			'<header>' .
			$this->headerContent() .
			$this->primaryMenuContainer($this->primaryMenu()) .
			'</header>' .
			'<div class="divider"></div>' .
			$this->fancyImagebar() .
			'<main id="content">' .
			$this->flashMessagesContainer(FlashMessages::getMessages());
	}

	public function fancyImagebar() {
		if (Module::getModuleByName('fancy_imagebar')) {
			$fib = new FancyImagebarClass;
			if (method_exists($fib, 'loadFancyImagebar') && $fib->loadFancyImagebar()) {
				return
					$fib->getFancyImagebar() .
					'<div class="divider fancy-imagebar-divider"></div>';
			}
		}
	}

	/** {@inheritdoc} */
	protected function flashMessageContainer(\stdClass $message) {
		// This theme uses jQuery markup.
		return '<p class="ui-state-highlight">' . $message->text . '</p>';
	}

	/** {@inheritdoc} */
	public function footerContainer() {
		return
			'</main>' .
			'<div class="divider"></div>' .
			'<footer>' . $this->footerContent() . '</footer>';
	}

	protected function formatFavoritesMenu() {
		$menu = parent::menuFavorites();
		if (count($menu->getSubmenus())) {
			return
				'<div class="header-favorites">' .
				'<ul class="dropdown" role="menubar">' . $menu . '</ul>' .
				'</div>';
		} else {
			return null;
		}
	}

	protected function formatFlagsMenu() {
		if ($this->themeOption('flags') === '1') {
			return
				'<div class="header-flags">' .
				'<ul role="menubar">' . $this->menuFlags() . '</ul>' .
				'</div>';
		}
	}

	/** (@inheritdoc) */
	public function formatSecondaryMenu() {
		return
			'<div class="secondary-menu">' .
			'<ul class="dropdown" role="menubar">' .
			implode('', $this->secondaryMenu()) .
			'</ul>' .
			'</div>';
	}

	/** (@inheritdoc) */
	public function formatTreeTitle() {
		if ($this->tree && $this->themeOption('treetitle') === '1') {
			return '
			<h1 style="' . $this->headerTitleStyle() . '">
			<a href="index.php">' . $this->tree->getTitleHtml() . '</a>
			</h1>';
		} else {
			return '';
		}
	}

	/** {@inheritdoc} */
	public function headerContent() {
		return
			'<div class="header-top" style="' . $this->headerTopStyle() . '">' .
			$this->formatTreeTitle() .
			$this->formatSecondaryMenu() .
			'</div>' .
			'<div class="header-bottom">' .
			$this->formatFavoritesMenu() .
			$this->formQuickSearch() .
			$this->formatFlagsMenu() .
			'</div>';
	}

	// Theme setting for the tree title
	protected function headerTitleStyle() {
		$pos		 = $this->themeOption('titlepos');
		$posV		 = $pos['V']['pos'] . ':' . $pos['V']['size'] . $pos['V']['fmt'];
		$posH		 = $pos['H']['pos'] . ':' . $pos['H']['size'] . $pos['H']['fmt'];
		$posH		 = $pos['H']['pos'] == 'left' ? 'right:auto;' . $posH : 'left:auto;' . $posH;
		$font_size	 = 'font-size:' . $this->themeOption('titlesize') . 'px';
		return $font_size . ';' . $posV . ';' . $posH . ';';
	}

	// Theme settings for the header top section
	protected function headerTopStyle() {
		if ($this->themeOption('image')) {
			$image = WT_DATA_DIR . $this->themeOption('image');
		} else {
			$image = '';
		}
		if ($this->themeOption('header') === '1' && file_exists($image)) {
			$bg		 = file_get_contents($image); // The data dir is a protected directory.
			$type	 = @getimagesize($image);
			return 'background-image:url(data:' . $type['mime'] . ';base64,' . base64_encode($bg) . '); height: ' . $this->themeOption('headerheight') . 'px;';
		} elseif ($this->themeOption('header') === '2') {
			return 'height: ' . $this->themeOption('headerheight') . 'px;';
		} else {
			return 'background-image:url(' . $this->assetUrl() . 'images/header.jpg)';
		}
	}

	/** {@inheritdoc} */
	public function hookAfterInit() {
		// Put a version number in the URL, to prevent browsers from caching old versions.
		$this->theme_dir	 = 'themes/justblack/';
		$this->jquery_ui_url = $this->theme_dir . 'jquery-ui-1.11.4/';
		$this->colorbox_url	 = $this->theme_dir . 'colorbox-1.5.14/';
	}

	/** {@inheritdoc} */
	public function hookFooterExtraJavascript() {
		return
			$this->scriptVars() .
			'<script src="' . WT_JQUERY_COLORBOX_URL . '"></script>' .
			'<script src="' . WT_JQUERY_WHEELZOOM_URL . '"></script>' .
			'<script src="' . $this->jquery_ui_url . 'jquery-ui-effects.min.js"></script>' .
			'<script src="' . $this->theme_dir . 'justblack.js"></script>' .
			'<script src="' . $this->colorbox_url . 'justblack.colorbox.js"></script>' .
			$this->tableMessages() .
			$this->tableClippings();
	}

	/** {@inheritdoc} */
	public function hookHeaderExtraContent() {
		$html = '';
		if ($this->themeOption('css')) {
			$html .= '<link rel="stylesheet" type="text/css" href="' . $this->themeOption('css') . '">';
		}
		if (WT_SCRIPT_NAME == 'individual.php' || Filter::get('mod_action') === 'treeview') {
			$html .= '<link rel="stylesheet" type="text/css" href="' . $this->assetUrl() . 'treeview.css">';
		}
		return $html;
	}

	/** {@inheritdoc} */
	public function individualBox(Individual $individual) {
		if ($this->tree && $this->themeOption('square_thumbs')) {
			$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
			if ($this->tree->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
				$thumbnail = $this->thumbnail($individual);
			} else {
				$thumbnail = '';
			}

			$content = '<span class="namedef name1">' . $individual->getFullName() . '</span>';

			$icons = '';
			if ($individual->canShowName()) {
				$content = '<a href="' . $individual->getHtmlUrl() . '">' . $content . '</a>' .
					'<div class="namedef name1">' . $individual->getAddName() . '</div>';
				$icons	 = '<div class="noprint icons">' .
					'<span class="iconz icon-zoomin" title="' . I18N::translate('Zoom in/out on this box.') . '"></span>' .
					'<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
					'<ul class="' . $personBoxClass . '">' . implode('', $this->individualBoxMenu($individual)) . '</ul>' .
					'</div>' .
					'</div>' .
					'</div>';
			}

			return
				'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' box-style1" style="width: ' . $this->parameter('chart-box-x') . 'px; min-height: ' . $this->parameter('chart-box-y') . 'px">' .
				$icons .
				'<div class="chart_textbox" style="max-height:' . $this->parameter('chart-box-y') . 'px;">' .
				$thumbnail .
				$content .
				'<div class="inout2 details1">' . $this->individualBoxFacts($individual) . '</div>' .
				'</div>' .
				'<div class="inout"></div>' .
				'</div>';
		} else {
			return parent::individualBox($individual);
		}
	}

	/** {@inheritdoc} */
	public function individualBoxLarge(Individual $individual) {
		if ($this->tree && $this->themeOption('square_thumbs')) {
			$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
			if ($this->tree->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
				$thumbnail = $this->thumbnail($individual);
			} else {
				$thumbnail = '';
			}

			return
				'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' box-style2">' .
				'<div class="noprint icons">' .
				'<span class="iconz icon-zoomin" title="' . I18N::translate('Zoom in/out on this box.') . '"></span>' .
				'<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
				'<ul class="' . $personBoxClass . '">' . implode('', $this->individualBoxMenu($individual)) . '</ul>' .
				'</div>' .
				'</div>' .
				'</div>' .
				'<div class="chart_textbox" style="max-height:' . $this->parameter('chart-box-y') . 'px;">' .
				$thumbnail .
				'<a href="' . $individual->getHtmlUrl() . '">' .
				'<span class="namedef name2">' . $individual->getFullName() . '</span>' .
				'</a>' .
				'<div class="namedef name2">' . $individual->getAddName() . '</div>' .
				'<div class="inout2 details2">' . $this->individualBoxFacts($individual) . '</div>' .
				'</div>' .
				'<div class="inout"></div>' .
				'</div>';
		} else {
			return parent::individualBoxLarge($individual);
		}
	}

	public function individualBoxSmall(Individual $individual) {
		if ($this->themeOption('square_thumbs')) {
			$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
			if ($this->tree->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
				$thumbnail = $this->thumbnail($individual);
			} else {
				$thumbnail = '';
			}

			return
				'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' iconz box-style0" style="width: ' . $this->parameter('compact-chart-box-x') . 'px; min-height: ' . $this->parameter('compact-chart-box-y') . 'px">' .
				'<div class="compact_view">' .
				$thumbnail .
				'<a href="' . $individual->getHtmlUrl() . '">' .
				'<span class="namedef name0">' . $individual->getFullName() . '</span>' .
				'</a>' .
				'<div class="inout2 details0">' . $individual->getLifeSpan() . '</div>' .
				'</div>' .
				'<div class="inout"></div>' .
				'</div>';
		} else {
			return parent::individualBoxSmall($individual);
		}
	}

	/** {@inheritdoc} */
	public function menuLogin() {
		if (Auth::check() || Auth::isSearchEngine()) {
			return null;
		} else {
			return new Menu(I18N::translate('Login'), WT_LOGIN_URL . '?url=' . rawurlencode(Functions::getQueryUrl()));
		}
	}

	/** {@inheritdoc} */
	public function logoPoweredBy() {
		return
			parent::logoPoweredBy() .
			'<a class="link" href="http://www.justcarmen.nl" target="_blank">Design: justcarmen.nl</a>';
	}

	protected function menuCompact(Individual $individual, $surname) {
		$menu = new Menu(I18N::translate('View'), '#', 'menu-view');

		$menu->addSubmenu($this->menuChart($individual));
		$menu->addSubmenu($this->menuLists($surname));

		/** $menuReports could return null */
		if ($this->themeOption('compact_menu_reports') && $this->menuReports()) {
			$menu->addSubmenu($this->menuReports());
		}

		$menu->addSubmenu($this->menuCalendar());

		foreach ($menu->getSubmenus() as $submenu) {
			$class		 = explode("-", $submenu->getClass());
			$new_class	 = implode("-", array($class[0], 'view', $class[1]));
			$submenu->setClass($new_class);
		}

		return $menu;
	}

	protected function menuFlags() {
		$menu = $this->menuLanguages();

		$flags = '';
		if ($menu && $menu->getSubmenus()) {
			foreach ($menu->getSubmenus() as $submenu) {
				if ($submenu) {
					$attrs = '';
					foreach ($submenu->getAttrs() as $key => $value) {
						$attrs .= ' ' . $key . '="' . Filter::escapeHtml($value) . '"'; //						
					}
					$flags .= '<li class="' . $submenu->getClass() . '" title="' . $submenu->getLabel() . '">
								<a href="' . $submenu->getLink() . '"' . $attrs . '></a></li>';
				}
			}
			return $flags;
		}
	}

	public function menuLists($surname) {
		$menu = parent::menuLists($surname);
		if ($this->themeOption('media_menu')) {
			$submenus = array_filter($menu->getSubmenus(), function (Menu $menu) {
				return $menu->getClass() !== 'menu-list-obje';
			});
			$menu->setSubmenus($submenus);
		}
		return $menu;
	}

	protected function menuMedia() {
		$MEDIA_DIRECTORY = $this->tree->getPreference('MEDIA_DIRECTORY');

		$mainfolder		 = $this->themeOption('media_link') === $MEDIA_DIRECTORY ? '' : '&amp;folder=' . Filter::escapeUrl($this->themeOption('media_link'));
		$folders		 = $this->themeOption('mediafolders');
		$show_subfolders = $this->themeOption('show_subfolders') ? '&amp;subdirs=on' : '';

		if (count($folders) > 1) {
			$menu = new Menu(/* I18N: Main media menu */ I18N::translate('Media'), '#', 'menu-media');

			foreach ($folders as $key => $folder) {
				if ($key !== $MEDIA_DIRECTORY) {
					$submenu = new Menu(ucfirst($folder), 'medialist.php?' . $this->tree_url . '&amp;action=filter&amp;search=no&amp;folder=' . rawurlencode($key) . '&amp;sortby=title' . $show_subfolders . '&amp;max=20&amp;columns=2&amp;action=submit', 'menu-mediafolder');
					$menu->addSubmenu($submenu);
				}
			}
		} else { // fallback if we don't have any subfolders added to the list
			$menu = new Menu(/* I18N: Main media menu */ I18N::translate('Media'), 'medialist.php?' . $this->tree_url, 'menu-media');
		}
		return $menu;
	}

	protected function menuModule($module_name) {
		$modules = Module::getActiveMenus($this->tree);
		if (array_key_exists($module_name, $modules)) {
			return $modules[$module_name]->getMenu();
		} else {
			return null;
		}
	}

	/** {@inheritdoc} */
	public function parameter($parameter_name) {
		$parameters = array(
			'chart-background-f'			 => 'ffeeb0',
			'chart-background-m'			 => 'ff8c00',
			'chart-background-u'			 => 'ffffde',
			'chart-font-color'				 => '2e2e2e',
			'chart-font-size'				 => 9,
			'chart-spacing-x'				 => 10,
			'chart-spacing-y'				 => 15,
			'distribution-chart-high-values' => 'ff8c00',
			'distribution-chart-low-values'	 => 'ffeeb0',
			'line-width'					 => 1,
			'shadow-blur'					 => 12,
			'shadow-color'					 => '171717',
			'shadow-offset-x'				 => 2,
			'shadow-offset-y'				 => 2,
		);

		if (WT_SCRIPT_NAME === 'pedigree.php' && (Filter::getInteger('orientation') === 2 || Filter::getInteger('orientation') === 3)) {
			$parameters['compact-chart-box-x']	 = 90;
			$parameters['compact-chart-box-y']	 = 120;
		}

		if (array_key_exists($parameter_name, $parameters)) {
			return $parameters[$parameter_name];
		} else {
			return parent::parameter($parameter_name);
		}
	}

	/** {@inheritdoc} */
	public function primaryMenu() {
		global $controller;

		$menus = $this->themeOption('menu');
		if ($this->tree && $menus) {
			$individual	 = $controller->getSignificantIndividual();
			$surname	 = $controller->getSignificantSurname();
			foreach ($menus as $menu) {
				$label		 = $menu['label'];
				$sort		 = $menu['sort'];
				$function	 = $menu['function'];
				if ($sort > 0) {
					if ($function === 'menuCompact') {
						$menubar[] = $this->menuCompact($individual, $surname);
					} elseif ($function === 'menuMedia') {
						$menubar[] = $this->menuMedia();
					} elseif ($function === 'menuChart') {
						$menubar[] = $this->menuChart($individual);
					} elseif ($function === 'menuLists') {
						$menubar[] = $this->menuLists($surname);
					} elseif ($function === 'menuModule') {
						$menubar[] = $this->menuModule($label);
					} else {
						$menubar[] = $this->{$function}();
					}
				}
			}
			return array_filter($menubar);
		} else {
			return parent::primaryMenu();
		}
	}

	// This theme uses variables from php files in the javascript files
	protected function scriptVars() {
		if ($this->tree) {
			$tree_title = $this->tree->getName();
		} else {
			$tree_title = '';
		}

		return '<script>' .
			'var WT_CSS_URL = "' . $this->assetUrl() . '";' .
			'var WT_TREE_TITLE = "' . $tree_title . '";' .
			'var JB_THEME_URL = "' . $this->theme_dir . '";' .
			'var JB_COLORBOX_URL = "' . $this->colorbox_url . '";' .
			'var authID = "' . Auth::id() . '";' .
			'</script>';
	}

	/** (@inheritdoc) */
	public function secondaryMenu() {
		return array_filter(array(
			$this->menuPendingChanges(),
			$this->menuMyPages(),
			$this->menuThemes(),
			!$this->themeOption('flags') ? $this->menuLanguages() : '',
			$this->menuLogin(),
			$this->menuLogout()
		));
	}

	/** {@inheritdoc} */
	public function stylesheets() {
		return array(
			$this->jquery_ui_url . 'jquery-ui.min.css',
			$this->colorbox_url . 'colorbox.css',
			$this->assetUrl() . 'style.css',
		);
	}

	protected function tableClippings() {
		if (Filter::get('mod') == 'clippings') {
			return
				'<script src="' . WT_JQUERY_DATATABLES_JS_URL . '"></script>' .
				'<script>
				var dataTable = jQuery("table#mycart");
				dataTable.find("tr:first").wrap("<thead>"); dataTable.find("tbody").before(jQuery("thead"));
				jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
				jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
				dataTable.dataTable({
					dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
					' . I18N::datatablesI18N() . ',
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
				</script>';
		}
	}

	protected function tableMessages() {
		if (WT_SCRIPT_NAME == 'index.php') {
			return
				'<script src="' . WT_JQUERY_DATATABLES_JS_URL . '"></script>' .
				'<script>
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
					' . I18N::datatablesI18N() . ',
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
				</script>';
		}
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'justblack';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ I18N::translate('JustBlack');
	}

	// This theme comes with an optional module to set a few theme options
	protected function themeOption($setting) {
		if (Module::getModuleByName('justblack_theme_options')) {
			$module = new JustBlackThemeOptionsClass;
			return $module->options($setting);
		}
	}

	protected function thumbnail($individual) {
		$media = $individual->findHighlightedMedia();
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
							return $no_thumbnail = true;
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
						$thumbwidth	 = $thumbheight = '50';


						if ($thumbwidth / $thumbheight > $ratio_orig) {
							$new_height	 = $thumbwidth / $ratio_orig;
							$new_width	 = $thumbwidth;
						} else {
							$new_width	 = $thumbheight * $ratio_orig;
							$new_height	 = $thumbheight;
						}

						$process = imagecreatetruecolor(round($new_width), round($new_height));
						imagecopyresampled($process, $imagesrc, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
						$thumb	 = imagecreatetruecolor($thumbwidth, $thumbheight);
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
						' data-obje-note="' . Filter::escapeHtml($media->getNote()) . '"' .
						' data-title="' . Filter::escapeHtml($media->getFullName()) . '"' .
						'>' . $image . '</a>';
				}
			} else {
				$no_thumbnail = true;
			}
		} else {
			$no_thumbnail = true;
		}

		if ($no_thumbnail == true) {
			if ($this->tree->getPreference('USE_SILHOUETTE')) {
				return '<i class="icon-silhouette-' . $individual->getSex() . '"></i>';
			} else {
				return '';
			}
		}
	}

}

return new JustBlackTheme;

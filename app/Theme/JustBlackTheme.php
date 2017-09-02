<?php
/**
 * JustBlack Theme
 *
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
 * Copyright (C) 2017 JustCarmen
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
namespace JustCarmen\WebtreesThemes\JustBlack\Theme;

use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;

class JustBlackTheme extends JustBaseTheme {

  const THEME_NAME    = 'JustBlack';
  const THEME_DIR     = 'justblack';
  const ASSET_DIR     = 'themes/' . self::THEME_DIR . '/css/';
  const STYLESHEET    = self::ASSET_DIR . 'style.css?v' . self::THEME_VERSION;
  const JAVASCRIPT    = 'themes/' . self::THEME_DIR . '/js/theme.js?v' . self::THEME_VERSION;

  /**
   * In this theme we use a fluid container, an extra divider to separate the content from
   * the header and footer, a background image in the header and a design bar with the search
   * and favorites menu
   *
   * {@inheritdoc} */
  public function bodyHeader() {
    return
        '<body class="wt-global' . $this->getPageGlobalClass() . ' theme-' . self::THEME_DIR . '">' .
        '<header class="wt-header-wrapper">' .
        '<div class="container-fluid wt-header-container">' .
        '<div class="d-flex flex-wrap wt-header-content m-0">' .
        $this->headerContent() .
        '</div>' .
        '</div>' .
        $this->formatFavSearchBar() .
        $this->primaryMenuContainer($this->primaryMenu()) .
        '</header>' .
        $this->formatDivider() .
        $this->fancyImagebar() .
        '<main id="content" class="container' . $this->setFluidClass() . ' wt-main-wrapper mt-3">' .
        '<div class="wt-main-container">' .
        $this->flashMessagesContainer(FlashMessages::getMessages());
  }

  protected function bodyHeaderEnd() {
    return parent::bodyHeaderEnd() . $this->formatDivider();
  }

  protected function formatDivider() {
    return '<div class="jc-divider"></div>';
  }

  protected function formatFavoritesMenu() {
    $menu = parent::menuFavorites();
    if ($menu && count($menu->getSubmenus())) {
      return
          '<div class="header-favorites">' .
          '<ul class="dropdown  btn btn-primary-outline" role="menubar">' . $menu->bootstrap4() . '</ul>' .
          '</div>';
    } else {
      return null;
    }
  }

  protected function formatFavSearchBar() {
    return
        '<div class="jc-favsearch-bar">' .
        '<div class="jc-header-search-container  d-flex flex-nowrap flex-column">' .
        $this->formQuickSearch() .
        '</div>' .
        '</div>';
  }

  /** {@inheritdoc} */
  public function formatTreeTitle() {
    if ($this->tree) {
      return
          '<h1 class="col wt-site-title text-right px-5 py-3 align-self-end">' . $this->formatTreeTitleLink() . '</h1>';
    } else {
      return '';
    }
  }

  /** {@inheritdoc} */
  protected function headerContent() {
    return
        $this->accessibilityLinks() .
        '<div class="d-flex col-12 order-2">' .
        $this->logoHeader() .
        $this->formatTreeTitle() .
        '</div>' .
        '<div class="d-flex flex-column flex-nowrap col-12 order-1">' .
        $this->secondaryMenuContainer($this->secondaryMenu()) .
        '</div>';
  }

  /** {@inheritdoc} */
  public function hookHeaderExtraContent() {
    $html = '';
    if ($this->themeOption('css')) {
      $html .= '<link rel="stylesheet" type="text/css" href="' . $this->themeOption('css') . '">';
    }
    return $html;
  }

  /** {@inheritdoc} */
  public function parameter($parameter_name) {
    $parameters = [
        'chart-background-f'             => 'ffeeb0',
        'chart-background-m'             => 'ff8c00',
        'chart-background-u'             => 'ffffde',
        'chart-font-color'               => '2e2e2e',
        'chart-font-size'                => 9,
        'chart-spacing-x'                => 10,
        'chart-spacing-y'                => 15,
        'distribution-chart-high-values' => 'ff8c00',
        'distribution-chart-low-values'  => 'ffeeb0',
        'line-width'                     => 1,
        'shadow-blur'                    => 12,
        'shadow-color'                   => '171717',
        'shadow-offset-x'                => 2,
        'shadow-offset-y'                => 2,
    ];

    if (array_key_exists($parameter_name, $parameters)) {
      return $parameters[$parameter_name];
    } else {
      return parent::parameter($parameter_name);
    }
  }

  /** (@inheritdoc) */
  public function secondaryMenu() {
    return array_filter([
        $this->menuPendingChanges(),
        $this->menuMyPages(),
        $this->menuThemes(),
        $this->menuLanguages(),
        $this->menuLogin(),
        $this->menuLogout()
    ]);
  }

  /**
   * In this theme we use full width pages on some pages
   */
  protected function setFluidClass() {
    $pages   = ['index', 'individual'];
    $modules = ['tree'];

    if (in_array($this->getPage(), $pages) || (in_array(Filter::get('mod'), $modules))) {
      return '-fluid'; // container-fluid
    }
  }

  /** @inheritdoc} */
  public function stylesheets() {
    return array_merge(
      parent::stylesheets(), [self::STYLESHEET]
    );
  }

  /** {@inheritdoc} */
  public function themeName() {
    return /* I18N: Name of a theme. */ I18N::translate(self::THEME_NAME);
  }

}

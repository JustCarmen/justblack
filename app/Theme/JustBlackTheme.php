<?php
/**
 * JustBlack theme for webtrees (online genealogy)
 * Copyright (C) 2019 JustCarmen (http://www.justcarmen.nl)
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

use Fisharebest\Webtrees\I18N;

class JustBlackTheme extends JustBaseTheme {
	const THEME_NAME = 'JustBlack';
	const THEME_DIR  = 'justblack';
	const ASSET_DIR  = 'themes/' . self::THEME_DIR . '/assets/';
	const STYLESHEET = self::ASSET_DIR . 'css/style.css?v' . self::THEME_VERSION;
	const JAVASCRIPT = self::ASSET_DIR . 'js/theme.js?v' . self::THEME_VERSION;

	public function menuFavorites() {
		$menu = parent::menuFavorites();
		if ($menu && count($menu->getSubmenus())) {
			return $menu->bootstrap4();
		}
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
	public function secondaryMenu():array {
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
	public function setFluidClass() {
		$pages = ['tree-page', 'user-page', 'individual'];

		if (in_array($this->request->get('route'), $pages)) {
			return '-fluid'; // container-fluid
		}
	}

	/** {@inheritdoc} */
	public function themeName():string {
		return /* I18N: Name of a theme. */ I18N::translate(self::THEME_NAME);
	}
}

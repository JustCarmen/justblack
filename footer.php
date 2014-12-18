<?php
// Footer for JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
// Copyright (C) 2014 JustCarmen.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
?>

</main>
<?php if ($view!='simple'): ?>
	<div class="divider"></div>
	<footer id="footer" class="<?php echo $TEXT_DIRECTION; ?> width99 center">
	<?php echo contact_links() ?>
	<p class="logo">
		<a href="<?php echo WT_WEBTREES_URL; ?>" target="_blank" class="icon-webtrees" title="<?php echo WT_WEBTREES, ' ', WT_VERSION; ?>"></a>
		<br><a href="http://www.justcarmen.nl" target="_blank">Design: justcarmen.nl</a>
	</p>
	
	<?php
	if ($WT_TREE && $WT_TREE->getPreference('SHOW_STATS')) {
		echo execution_stats();
	}
	?>
	
	<?php if (exists_pending_change()) { ?>
		<a href="#" onclick="window.open('edit_changes.php', '_blank', chan_window_specs); return false;">
			<p class="error center"><?php echo WT_I18N::translate('There are pending changes for you to moderate.'); ?></p>
		</a>
	<?php } ?>
	</footer>
<?php endif; ?>

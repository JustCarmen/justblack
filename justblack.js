/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * Copyright (C) 2015 JustCarmen
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


//=========================================================================================================
//												FUNCTIONS
//=========================================================================================================

// Modal dialog boxes
function jb_modalDialog(url, title) {
	var $dialog = jQuery('<div id="config-dialog" style="max-height:550px; overflow-y:auto"><div title="' + title + '"><div></div>')
	.load(url).dialog({
		title: title,
		width: 'auto',
		maxWidth: '90%',
		height: 'auto',
		maxHeight: 500,
		fluid: true,
		modal: true,
		resizable: false,
		autoOpen: false,
		open: function() {
			jQuery('.ui-widget-overlay').on('click', function() {
				$dialog.dialog('close');
			});
		}
	});

	// open the dialog box after some time. This is neccessary for the dialogbox to load in center position without page flickering.
	setTimeout(function() {
		$dialog.dialog('open');
	}, 500);
	return false;
}

function jb_helpDialog(topic, module) {
	jQuery.getJSON('help_text.php?help=' + topic + '&mod=' + module, function(json) {
		jb_modalHelp(json.content, json.title);
	});
}

function jb_modalHelp(content, title) {
	var $dialog = jQuery('<div style="max-height:375px; overflow-y:auto"><div></div></div>').html(content).dialog({
		width: 'auto',
		maxWidth: 500,
		height: 'auto',
		maxHeight: 500,
		modal: true,
		fluid: true,
		resizable: false,
		open: function() {
			jQuery('.ui-widget-overlay').on('click', function() {
				$dialog.dialog('close');
			});
		}
	});

	jQuery('.ui-dialog-title').html(title);
	return false;
}

jQuery(document).on("dialogopen", ".ui-dialog", function() {
	fluidDialog();
});

// remove window resize namespace
jQuery(document).on("dialogclose", ".ui-dialog", function() {
	jQuery(window).off("resize.responsive");
});

jQuery(window).resize(function() {
	jQuery(".ui-dialog-content").dialog("option", "position", {
		my: "center",
		at: "center",
		of: window
	});
});

function fluidDialog() {
	var $visible = jQuery(".ui-dialog:visible");
	$visible.each(function() {
		var $this = jQuery(this);
		var dialog = $this.find(".ui-dialog-content");
		var maxWidth = dialog.dialog("option", "maxWidth");
		var width = dialog.dialog("option", "width");
		var fluid = dialog.dialog("option", "fluid");
		// if fluid option == true
		if (maxWidth && width) {
			// fix maxWidth bug
			$this.css("max-width", maxWidth);
			//reposition dialog
			dialog.dialog("option", "position", {
				my: "center", 
				at: "center",
				of: window
			});
		}

		if (fluid) {
			// namespace window resize
			jQuery(window).on("resize.responsive", function() {
				var wWidth = jQuery(window).width();
				// check window width against dialog width
				if (wWidth < maxWidth + 50) {
					// keep dialog from filling entire screen
					$this.css("width", "90%");

				}
				//reposition dialog
				dialog.dialog("option", "position", {
					my: "center", 
					at: "center",
					of: window
				});
			});
		}
	});
}

function qstring(key, url) {
	'use strict';
	var KeysValues, KeyValue, i;
	if (url === null || url === undefined) {
		url = window.location.href;
	}
	KeysValues = url.split(/[\?&]+/);
	for (i = 0; i < KeysValues.length; i++) {
		KeyValue = KeysValues[i].split("=");
		if (KeyValue[0] === key) {
			return KeyValue[1];
		}
	}
}

//=========================================================================================================
//												GENERAL
//=========================================================================================================
jQuery(document).ready(function() {
	'use strict';
	var obj, title;
	/********************************************* TOOLTIPS ***********************************************/
	// Tooltips for all title attributes
	function add_tooltips() {
		jQuery('*[title]').each(function() {
			title = jQuery(this).attr('title');
			jQuery(this).on('click', function() {
				jQuery(this).attr('title', title); // some functions need the title attribute. Make sure it is filled when clicking the item.
			});
		});

		jQuery(document).tooltip({
			items: '*[title]:not(.ui-dialog-titlebar-close)'
		});
	}

	add_tooltips(); // needed when no ajaxcall is made on the particular page.
	jQuery(document).ajaxComplete(function() { // be sure the tooltip is activated after a ajax call is made.
		add_tooltips();
	});

	/******************************************* DROPDOWN MENU *********************************************/
	jQuery('.dropdown > li').hover(function() {
		jQuery(this).find('ul').show();
	}, function() {
		jQuery(this).find('ul').hide();
	});

	// function to use with unsorted dropdownmenus like the fav-menu.
	function sortMenu(dropdownMenu) {
		var menu = jQuery(dropdownMenu + ' .dropdown ul').children('li').get();
		menu.sort(function(a, b) {
			var val1 = jQuery(a).text().toUpperCase();
			var val2 = jQuery(b).text().toUpperCase();
			return (val1 < val2) ? -1 : (val1 > val2) ? 1 : 0;
		});
		jQuery.each(menu, function(index, row) {
			jQuery(dropdownMenu + ' .dropdown ul').append(row);
		});
	}

	/********************************************* MAIN MENU ***********************************************/
	jQuery('.primary-menu').each(function() {
		var dTime, li_height, height, maxHeight, i;

		jQuery(this).find('li').hover(function() {
			//show submenu
			jQuery(this).find('>ul').slideDown('slow');
		}, function() {
			//hide submenu
			jQuery(this).find('>ul').hide();
		});

		dTime = 1200;
		jQuery(this).find('ul').each(function() {
			jQuery(this).children().hover(function() {
				jQuery(this).stop().animate({
					backgroundColor: '#808080'
				}, dTime);
			}, function() {
				jQuery(this).stop().animate({
					backgroundColor: '#272727'
				}, dTime);
			});
		});

		// dynamic height of menubar
		li_height = jQuery(this).find('> li').height();
		height = jQuery(this).find('> li > a').map(function() {
			return jQuery(this).height();
		});
		maxHeight = height[0];
		for (i = 0; i < height.length; i++) {
			maxHeight = Math.max(maxHeight, height[i]);
		}
		jQuery('nav').css('height', li_height + maxHeight);

		// No Gedcom submenu if there is just one gedcom
		if (jQuery('#menu-tree ul li').length === 1) {
			jQuery('#menu-tree ul').remove();
		}

		// open admin in new browsertab
		jQuery(this).find('ul li#menu-admin a').attr('target', 'blank');
	});

	/********************************************* LANGUAGE (FLAGS) MENU ******************************************/
	jQuery('.header-flags').each(function() {
		jQuery(this).find('li').each(function() {
			jQuery(this).tooltip({
				position: {
					my: "center top-40",
					at: "center center"
				}
			});
			jQuery(this).click(function() {
				location.href = jQuery(this).find('a').attr('href');
			});
		});
	});

	/********************************************* FAV-MENU ******************************************/
	var pageId = qstring('pid') || qstring('famid') || qstring('mid') || qstring('nid') || qstring('rid') || qstring('sid');
	var submenu = jQuery('.header-favorites > ul ul');

	obj = submenu.find('li');
	if (authID && pageId !== undefined) {
		obj = submenu.find('li').not(':last');
		submenu.find('li:last a').addClass('add-favorite');
	}

	obj.each(function() {
		var url = jQuery(this).find('a').attr('href');
		var id = qstring('pid', url) || qstring('famid', url) || qstring('mid', url) || qstring('nid', url) || qstring('rid', url) || qstring('sid', url);
		if (id === pageId) {
			jQuery(this).addClass('active');
			jQuery('#menu-favorites > a').replaceWith(jQuery(this).html());
			jQuery('.add-favorite').parent('li').remove();
		}
	});

	obj.click(function() {
		jQuery('#menu-favorites > a').replaceWith(jQuery(this).html());
	});

	sortMenu('.header-favorites');

	// place the add-favorite-link at the bottom of the list after sorting has taken place
	jQuery(".add-favorite").parent().appendTo("#menu-favorites ul")

	/**************************************** MODAL DIALOG BOXES ********************************************/
	// replace default function with our justblack theme function (better dialog boxes)
	function jb_dialogBox() {
		jQuery('[onclick^="modalDialog"], [onclick^="return modalDialog"]').each(function() {
			jQuery(this).attr('onclick', function(index, attr) {
				return attr.replace('modalDialog', 'jb_modalDialog');
			});
		});

		jQuery('[onclick^="helpDialog"]').each(function() {
			jQuery(this).attr('onclick', function(index, attr) {
				return attr.replace('helpDialog', 'jb_helpDialog');
			});
		});
	}

	jb_dialogBox();
	jQuery(document).ajaxComplete(function() {
		jb_dialogBox();
	});

	/********************************************* CUSTOM CONTACT LINK ***********************************************/
	// custom contact link (in custom html block or news block for example). Give the link the class 'contact_link_admin');
	jQuery('a.contact_link_admin').each(function() {
		var onclickItem = jQuery('.contact_links a').attr('onclick');
		jQuery(this).attr('onclick', onclickItem).wrap('<span class="contact_links">');
	});

	/********************************************* LOGIN FORM ***********************************************/
	if (jQuery('#login-page').length > 0) {
		// login page styling
		jQuery('#login-page #login-text b:first').wrap('<div id="login-page-title" class="subheaders ui-state-default">');
		jQuery('#login-page #login-page-title').prependTo('#login-page');
		jQuery('#login-page #login-text br:first').remove();
		jQuery('#login-page #login-text br:first').remove();
		jQuery('#login-page #login-text, #login-page #login-box').wrapAll('<div id="login-page-block">');
	}

	/********************************************* REGISTER FORM ***********************************************/
	if (jQuery('#login-register-page').length > 0) {
		title = jQuery('#login-register-page h2').text();
		jQuery('#login-register-page h2').remove();
		if (title !== "") {
			jQuery('<div id="login-register-page-title" class="subheaders ui-state-default">' + title + '</div>').prependTo('#login-register-page');
		}
		jQuery('#login-register-page .largeError').removeAttr('class').css('font-weight', 'bold');
		jQuery('#login-register-page .error').removeAttr('class');
		jQuery('#login-register-page #register-text, #login-register-page #register-box').wrapAll('<div id="register-page-block">');

		jQuery('#login-register-page #register-form label').each(function() {
			jQuery(this).after(jQuery(this).find('input'));
			jQuery(this).after(jQuery(this).find('select'));
			jQuery(this).after(jQuery(this).find('textarea'));
		});
		jQuery('#login-register-page #register-form textarea').before('<br />').attr('rows', '8');
	}

	/************************************ EDIT USER PAGE **********************************************/
	if (WT_SCRIPT_NAME === 'edituser.php') {
		title = jQuery('#edituser-page h2').text();
		jQuery('#edituser-page h2').remove();
		jQuery('<div id="edituser-page-title" class="subheaders ui-state-default">' + title + '</div>').prependTo('#edituser-page');
		jQuery('#edituser_submit').before('<hr class="clearfloat">');
		jQuery('#edituser-table input:first[type=password]').each(function() {
			jQuery(this).parent().wrapInner('<span class="pw-info">');
			jQuery(this).prependTo(jQuery(this).parents('.value'));
		});

		if (jQuery('#theme-menu').html() === "") {
			jQuery('select[name=form_theme]').parents('.value').prev('.label').remove();
			jQuery('select[name=form_theme]').parents('.value').remove();
		}
	}

	/************************************** HOMEPAGE AND MY PAGE ***********************************************/

	// Icons for gedcom block on homepage and user welcome block on my page (these are bigger then the standard icons)
	var block = jQuery('.gedcom_block_block, .user_welcome_block');
	block.find('.icon-indis').removeClass().addClass('icon-indi-big');
	block.find('.icon-pedigree').removeClass().addClass('icon-pedigree-big');
	block.find('.icon-mypage').removeClass().addClass('icon-mypage-big');
	block.find('a').css('font-size', '11px');

	// gedcom and user favorites block
	jQuery('.block .gedcom_favorites_block .action_header, .block .gedcom_favorites_block .action_headerF, .block .user_favorites_block .action_header, .block .user_favorites_block .action_headerF').each(function() {
		jQuery(this).removeClass('person_box');
	});

	/************************************** INDIVIDUAL PAGE ***********************************************/
	if (WT_SCRIPT_NAME === 'individual.php') {

		// General
		jQuery('<div class="divider">').appendTo('#tabs ul:first');
		jQuery('#tabs li').each(function() {
			jQuery(this).tooltip({
				position: {
					my: "center top+25",
					at: "center center"
				}
			});
		});
	}

	/************************************************ PERSON BOXES *****************************************************/
	// customize the personbox view
	function personbox_default() {
		var obj = jQuery(".person_box_template .inout2");
		modifybox(obj);
	}

	function modifybox(obj) {
		obj.find(".field").contents().filter(function() {
			return (this.nodeType === 3);
		}).remove();
		obj.find(".field span").filter(function() {
			return jQuery(this).text().trim().length === 0;
		}).remove();
		obj.find("div[class^=fact_]").each(function() {
			var div = jQuery(this);
			div.find(".field").each(function() {
				if (jQuery.trim(jQuery(this).text()) === '') {
					div.remove();
				}
			});
		});

	}

	personbox_default();

	jQuery(document).ajaxComplete(function() {
		setTimeout(function() {
			personbox_default();
		}, 500);
		var obj = jQuery(".person_box_zoom");
		modifybox(obj);
	});

	/************************************************ PEDIGREE CHART *****************************************************/
	if (WT_SCRIPT_NAME === 'pedigree.php') {
		jQuery("#content").each(function() {
			jQuery(this).height(jQuery(this).height() + 80);
		});
	}

	/************************************************ HOURGLASS CHART *****************************************************/
	function styleSB() {
		jQuery('.person_box_template.style1').each(function() {
			var width = jQuery(this).width();
			if (width < 250) { // spouses boxes are smaller then the default ones.
				jQuery(this).addClass('spouse_box').removeAttr('style') // css styling
					.closest('table').find('tr:first .person_box_template').css('border-bottom-style', 'dashed');
			}
		});
	}

	if (WT_SCRIPT_NAME === 'hourglass.php' && qstring('show_spouse') === '1') {
		jQuery('a[onclick*=ChangeDis]').on('click', function() {
			styleSB();
		});
		styleSB();
	}

	/****************************** CHILDBOX (ON PEDIGREE CHART AND HOURGLASS CHART)***************************************/
	if (WT_SCRIPT_NAME === 'pedigree.php' || WT_SCRIPT_NAME === 'hourglass.php') {
		jQuery('#hourglass_chart #childbox .name1').each(function() {
			jQuery(this).appendTo(jQuery(this).parents('#childbox'));
		});
		jQuery('#hourglass_chart #childbox table').remove();
		jQuery('#hourglass_chart #childbox').removeAttr('style');

		jQuery('#childbox').each(function() {
			var childbox = jQuery(this);
			childbox.find('br').remove();
			childbox.wrapInner('<ul>');
			childbox.find('a').wrap('<li>');
			childbox.find('ul > span').wrap('<li class="cb_title">');
			childbox.find('span.name1').each(function() {
				var sChar = '<';
				var str = jQuery(this).text();
				if (str.indexOf(sChar) > -1) {
					var newStr = str.replace(sChar, '');
					jQuery(this).text(newStr);
					jQuery(this).parents('li').addClass('cb_child');
				}
			});

			var li_child = jQuery('#hourglass_chart #childbox').parent().prev('table').find('.popup li.cb_child');
			li_child.each(function() {
				var child = jQuery(this).text();
				jQuery('#hourglass_chart #childbox li').each(function() {
					var str = jQuery(this).text();
					if (str === child) {
						jQuery(this).addClass('cb_child');
					}
					if (jQuery(this).hasClass('cb_title')) {
						return false; // stop the loop
					}
				});
			});

			childbox.find('.cb_child').wrapAll('<li><ul>');
			childbox.find(' > ul > li:not(:has(ul), .cb_title)').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
			childbox.find('.cb_child').prepend('<span class="ui-icon ui-icon-person left">');
		});

		if (jQuery('#hourglass_chart #childbox').length > 0) {
			var fTop = jQuery('#footer').offset().top;
			var cTop = jQuery('#hourglass_chart #childbox').offset().top;
			var cHeight = jQuery('#hourglass_chart #childbox').outerHeight();
			var hMargin = cHeight - (fTop - cTop) + 60;

			if (hMargin > 0) {
				jQuery('#hourglass_chart').css('margin-bottom', hMargin);
			}
		}
	}

	/************************************ FANCHART PAGE (POPUPS)***************************************/
	if (WT_SCRIPT_NAME === 'fanchart.php') {
		jQuery('.fan_chart_menu .person_box').each(function() {
			var fanbox = jQuery(this);
			fanbox.find('.name1:not(.children .name1, div.name1), .charts li').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
			fanbox.find('.children li').prepend('<span class="ui-icon ui-icon-person left">');
		});
	}

	/************************************** MEDIALIST PAGE ********************************************/
	if (WT_SCRIPT_NAME === 'medialist.php') {
		// Medialist Menu
		jQuery('.lightbox-menu').parent('td').each(function() {
			jQuery(this).wrapInner('<div class="lb-image_info">');
			jQuery(this).find('.lightbox-menu').prependTo(jQuery(this));
		});

		jQuery('.lightbox-menu .lb-menu li ul').wrap('<div class="popup">');

		jQuery('.lightbox-menu .lb-menu > li > a').each(function() {
			var tooltip, pos;
			tooltip = jQuery(this).text();
			if (jQuery(this).hasClass('lb-image_link')) {
				jQuery(this).parent().find('.popup ul').prepend('<li class="lb-pop-title">' + tooltip);
			} else {
				if (jQuery(this).hasClass('lb-image_edit')) {
					pos = "right-18";
				}
				if (jQuery(this).hasClass('lb-image_view')) {
					pos = "left+15";
				}
				jQuery(this).parent().tooltip({
					position: {
						my: pos + " center-2",
						at: "center center"
					}
				});
				jQuery(this).parent().attr('title', tooltip);
			}
			jQuery(this).text('');
		});

		jQuery('.lb-menu .lb-image_link').parent().hover(function() {
			jQuery(this).find('.popup').fadeIn('slow');
		}, function() {
			jQuery(this).find('.popup').fadeOut('slow');
		});

		// media link list
		jQuery(".lb-image_info").each(function() {
			jQuery(this).find('> a').addClass("media_link").next('br').remove();
			jQuery(this).find('.media_link').wrapAll('<div class="media_link_list">');
		});
	}

	/************************************** MEDIAVIEWER PAGE ******************************************/
	if (WT_SCRIPT_NAME === 'mediaviewer.php') {
		jQuery('#media-tabs').find('.ui-widget-header').removeClass('ui-widget-header');
		jQuery('#media-tabs ul').after('<div class="divider">');
	}

	/************************************** CALENDAR PAGE ********************************************/
	if (WT_SCRIPT_NAME === 'calendar.php') {
		jQuery('.icon-indis, .icon-cfamily').parent().addClass('ui-state-default');
		jQuery('.icon-sex_m_15x15').removeClass().addClass('icon-sex_m_9x9');
		jQuery('.icon-sex_f_15x15').removeClass().addClass('icon-sex_f_9x9');
		jQuery('#calendar-page li').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
	}

	/************************************** CLIPPINGS PAGE ********************************************/
	if (qstring('mod') === 'clippings') {
		jQuery('#content').addClass('clippings-page');
		jQuery('.clippings-page li').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
		jQuery('.clippings-page .topbottombar').addClass('ui-state-default descriptionbox').removeClass('topbottombar');
		jQuery('.clippings-page h2').parent('td').removeClass();
		jQuery('.clippings-page input[type=submit]').parent('td').removeClass().css('text-align', 'right');

		if (jQuery('.clippings-page h3').length > 0) {
			jQuery('.clippings-page').wrapInner('<div class="add-clippings">');
		}
	}

	/************************************** SEARCH PAGE ***********************************************/
	var searchForm = jQuery('#search-page form');
	var searchResult;
	var titleBtn = jQuery('#search-page h2').text();

	if (WT_SCRIPT_NAME === 'search.php') {
		searchResult = jQuery('#search-result-tabs');
		if (searchResult.length > 0) {
			searchForm.hide();
			searchResult.each(function() {
				jQuery(this).find('ul').append('<li id="search-btn" class="ui-state-default ui-corner-top"><a href="#search"><span>' + titleBtn);
				jQuery(this).find('.ui-tabs-nav, .fg-toolbar').removeClass('ui-widget-header');
			});

			jQuery('li#search-btn').on({
				mouseenter: function() {
					jQuery(this).addClass('ui-state-hover');
				},
				mouseleave: function() {
					jQuery(this).removeClass('ui-state-hover');
				},
				click: function() {
					jQuery(this).addClass('ui-state-active');
					searchResult.fadeOut('slow');
					searchForm.fadeIn('slow');
				}
			});
		}
	}

	if (WT_SCRIPT_NAME === 'search_advanced.php') {
		jQuery('#search-page a[onclick^=addFields]').attr('onclick', 'addFields();return false;');
		searchResult = jQuery('#search-page .indi-list');
		if (searchResult.length > 0) {
			searchForm.hide();
			searchResult.find('div[class^=filtersH]').append('<button id="search-btn" class="ui-state-default" type="button">' + titleBtn);

			jQuery('#search-btn').on({
				click: function() {
					searchResult.fadeOut('slow');
					searchForm.fadeIn('slow');
				}
			});
		}
	}

	/************************************** FAQ PAGE ***********************************************/
	if (qstring('mod') === 'faq') {
		jQuery('#content').addClass('faq-page');
		jQuery('.faq_title').addClass('ui-state-default');
		jQuery('hr').remove();
		jQuery('.faq_italic:first').css('padding', '10px 2px');
		jQuery('.faq a, .faq_top a').addClass('scroll');
	}

	/************************************* PLACELIST PAGE *******************************************/
	if (WT_SCRIPT_NAME === 'placelist.php') {
		jQuery('#place-hierarchy').each(function() {
			jQuery(this).find('.list_label').addClass('ui-state-default');
			jQuery(this).find('.icon-place').remove();
			jQuery(this).find('.list_table li a').before('<span class="ui-icon ui-icon-triangle-1-e left">');
			jQuery(this).find('table:first').prependTo('#places-tabs');
			jQuery(this).find('#places-tabs .ui-widget-header').removeClass('ui-widget-header');
			jQuery(this).find('#places-tabs ul.ui-tabs-nav').after('<div class="divider">');
		});
	}

	/************************************** PENDING CHANGES POP UP ***********************************************/
	jQuery('#pending table:first').addClass('top').after('<hr style="margin-top:15px">');
	jQuery('#pending table:last').addClass('bottom').before('<hr style="margin-bottom:15px">');
	jQuery('#pending > table').not('.top, .bottom').addClass('data');
	jQuery('#pending table.data td').addClass('box');
	jQuery('#pending table.data td table td').removeClass('box'); // only this way works
	jQuery('#pending .indent').removeClass();
	jQuery('#pending .box b').wrap('<div class="indi_link">');
	jQuery('#pending br').remove();

	// change the width of the popup screen
	jQuery('[onclick*="edit_changes.php"]').each(function() {
		jQuery(this).attr('onclick', function(index, attr) {
			return attr.replace('chan_window_specs', '\'width=850,height=600,left=100,top=100,resizable=1,scrollbars=1\'');
		});
	});

	/************************************* OTHER *******************************************/
	// Correction. On default pdf opens on the same page. We do not want to force users to use the browser back button.
	jQuery('#reportengine-page form').attr("onsubmit", "this.target='_blank'");

	// styling of the lifespan module
	jQuery('.lifespan_people .icon-sex_m_9x9').parents('#inner div[id^="bar"]').css({
		'background-color': '#545454',
		'border': '#dd6900 1px solid'
	});
	jQuery('.lifespan_people .icon-sex_f_9x9').parents('#inner div[id^="bar"]').css({
		'background-color': '#8E8E8E',
		'border': '#dd6900 1px solid'
	});
	jQuery('.lifespan_people a.showit i.icon-sex_m_9x9, .lifespan_people a.showit i.icon-sex_f_9x9').hide();

	// scroll to anchors
	jQuery(".scroll").click(function(event) {
		var id = jQuery(this).attr("href");
		var offset = 60;
		var target = jQuery(id).offset().top - offset;
		jQuery("html, body").animate({
			scrollTop: target
		}, 1000);
		event.preventDefault();
	});	
});
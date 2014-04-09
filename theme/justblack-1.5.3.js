/*
* JustBlack script for the JustBlack theme
*
* webtrees: Web based Family History software
* Copyright (C) 2014 JustCarmen
*
* Derived from PhpGedView
* Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
*/

//=========================================================================================================
//												FUNCTIONS
//=========================================================================================================

function jb_helpDialog(which, mod) {
	'use strict';
	var url='help_text.php?help='+which+'&mod='+mod;
	var $dialog = jQuery('<div style="max-height:375px; overflow-y:auto"><div><div class="loading-image"></div></div></div>')
		.dialog({
			width: 500,
			height: 'auto',
			maxHeight: 500,
			modal: true,
			position: ['center', 'center'],
			autoOpen: true,
			close: function(event, ui) {
				$dialog.remove();
			},
			open: function(event, ui) {
				jQuery('.ui-widget-overlay').bind('click', function(){
					$dialog.dialog('close');
				});
			}
		}).load(url+' .helpcontent', function() {
			jQuery(this).dialog("option", "position", ['center', 'center'] );
		});

	jQuery('.ui-dialog-title').load(url+' .helpheader');
	return false;
}

function jb_modalDialog(url, title) {
	'use strict';
	jQuery(document).ajaxComplete(function() {
		jQuery('#config-dialog').parent('.ui-dialog').before('<div class="ui-widget-overlay" />');
	});
	// initialize the dialog box
	var $tempdialog = jQuery('<div><div class="loading-image"></div></div>')
		.dialog({
			title: title,
			width: 400,
			height: 'auto',
			autoOpen: true,
			close: function(event, ui) {
				$tempdialog.remove();
			}
		}),
		$dialog = jQuery('<div id="config-dialog" style="max-height:550px; overflow-y:auto"><div title="'+title+'"><div></div>')
			.dialog({
				title: title,
				width: 'auto',
				height: 'auto',
				modal: false,
				autoOpen: false,
				open: function(event, ui) {
					$tempdialog.dialog('close');
					if (jQuery('textarea.html-edit').length > 0) {
						$dialog.dialog( "option", "width", 700 );
						$dialog.dialog( "option", "height", 550 );
					}
					jQuery('.ui-widget-overlay').bind('click', function(){
						$dialog.dialog('close');
						jQuery(this).remove();
					});
				},
				close: function(event, ui) {
					$dialog.remove();
					jQuery('.ui-widget-overlay').remove();
				}
			}).load(url);

	// open the dialog box after some time. This is needed for the dialogbox to get loaded in center position.
	setTimeout(function() {
		$dialog.dialog('open');
	}, 500);

	return false;
}

jQuery(window).resize(function() {
	'use strict';
	jQuery(".ui-dialog-content").dialog("option", "position", ['center', 'center']);
});

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

function jb_expandbox(boxid, bstyle) {
	'use strict';
	var getBox = function () {
		var result = jQuery.Deferred(); 	
		
		expandbox(boxid, bstyle);
		jQuery(".person_box_zoom[data-id='"+boxid+"']").each(function(){
			if (jQuery(this).html().indexOf("LOADING")>0) {
				jQuery(this).hide();
			}
		});	
		
		setTimeout(function () {
			result.resolve();
		}, 500);
		
		return result;
	},	
	modifyBox = function () {	
		jQuery(".person_box_zoom[data-id='"+boxid+"']").each(function(){
			var obj = jQuery(this);			
			obj.find(".field").contents().filter(function(){
				return (this.nodeType === 3);
			}).remove();
			obj.find(".field span").filter(function(){
				return jQuery(this).text().trim().length === 0;
			}).remove();
			obj.find("div[class^=fact_]").each(function(){
				var div = jQuery(this);
				div.find(".field").each(function(){
					if(jQuery.trim(jQuery(this).text()) === '') {
						div.remove();
					}		
				});	
			});
			obj.show();
		});
	};
	
	jQuery(".person_box_zoom[data-id='"+boxid+"']").each(function(){
		if (jQuery(this).html().indexOf("LOADING")>0) {
			jQuery(this).parents(".shadow").css({'box-shadow' : 'none'});
			getBox().done(modifyBox);
		} else {
			getBox();
			if(jQuery(this).is(':visible')) {
				jQuery(this).parents(".shadow").css({'box-shadow' : 'none'});
			} else {
				jQuery(this).parents(".shadow").css({'box-shadow' : '6px 8px #171717'});
			}
		}
	});	
}

//=========================================================================================================
//												GENERAL
//=========================================================================================================
jQuery(document).ready(function(){
	'use strict';
	var obj, title;
	/********************************************* COLORBOX MEDIA GALLERY ***********************************************/
	// prepare all images for colorbox display
	function get_imagetype() {
		var xrefs = [];
		jQuery('a[type^=image].gallery').each(function(){
		  var xref = qstring('mid', jQuery(this).attr('href'));
		  jQuery(this).attr('id', xref);
		  xrefs.push(xref);
		});
		jQuery.ajax({
			url: WT_THEME_JUSTBLACK + 'action.php?action=imagetype',
			type: 'POST',
			async: false,
			data: {
				'csrf'	:   WT_CSRF_TOKEN,
				'xrefs'	: xrefs
			},
			success: function(data) {
				jQuery.each(data, function(index, value) {
					jQuery('a[id=' + index + ']').attr('data-obje-type', value);
				});
			}
		});
	}
	// Function to correct long titles
	function longTitles() {
		var tClass 		= jQuery("#cboxTitle .title");
		var tID		  	= jQuery("#cboxTitle");
		if (tClass.width() > tID.width() - 100) { // 100 because the width of the 4 buttons is 25px each
			tClass.css({"width" : tID.width() - 100, "margin-left" : "75px"});
		}
		if (tClass.height() > 25) { // 26 is 2 lines
			tID.css({"bottom" : 0});
			tClass.css({"height" : "26px"}); // max 2 lines.
		} else {
			tID.css({"bottom" : "6px"}); // set the value to vertically center a 1 line title.
			tClass.css({"height" : "auto"}); // set the value back;
		}
	}

	function resizeImg() {
		jQuery("#cboxLoadedContent").css('overflow-x', 'hidden');
		var outerW = parseInt(jQuery("#cboxLoadedContent").css("width"), 10);
		var innerW = parseInt(jQuery(".cboxPhoto").css("width"), 10);
		if (innerW > outerW) {
			var innerH = parseInt(jQuery(".cboxPhoto").css("height"), 10);
			var ratio = innerH/innerW;
			var outerH = outerW * ratio;
			jQuery(".cboxPhoto").css({"width": outerW + "px", "height": outerH + "px"});
		}
	}

	// add colorbox function to all images on the page when first clicking on an image.
	jQuery("body").one('click', 'a.gallery', function(event) {
		get_imagetype();

		// General (both images and pdf)
		jQuery("a[type^=image].gallery, a[type$=pdf].gallery").colorbox({
			rel:      		"gallery",
			current:		"",
			slideshow:		true,
			slideshowAuto:	false,
			slideshowSpeed: 3000,
			onLoad:			function() {
								jQuery(".cboxNote, .pdf-layer").remove(); // remove previous note or watermarks.
							}
		});

		// Add colorbox to images
		jQuery("a[type^=image].gallery").colorbox({
			photo:			true,
			scalePhotos:	function(){
								if(jQuery(this).data('obje-type') === 'photo') {
									return true;
								}
							},
			maxWidth:		"90%",
			maxHeight:		"90%",
			title:			function(){
								var img_title = jQuery(this).data("title");
								return "<div class=\"title\">" + img_title + "</div>";
							},
			onComplete:		function() {
								if(jQuery(this).data('obje-type') !== 'photo') {
									resizeImg();
								}
								jQuery(".cboxPhoto").wheelzoom();
								jQuery(".cboxPhoto img").on("click", function(e) {e.preventDefault();});
								var note = jQuery(this).data("obje-note");
								if(note !== '') {
										jQuery('#cboxContent').append('<div class="cboxNote"><a href="#">' + note);
										if(jQuery('.cboxPhoto').innerHeight() > jQuery('#cboxContent').innerHeight()) {
											jQuery('.cboxNote').css('width', jQuery('.cboxNote').width() - 27);
										}
										jQuery('.cboxNote a').click(function(e){
											e.preventDefault();
											jQuery(this).parent().hide();										
										});
								}
								longTitles();
							}
		});

		// default settings for all pdf's
		jQuery("a[type$=pdf].gallery").colorbox({
			width:		"75%",
			height:		"90%",
			fixed:		true,
			title:		function(){
							var pdf_title = jQuery(this).data("title");
							pdf_title = '<div class="title">' + pdf_title;
							if(useWatermark === 0) {
								pdf_title += ' &diams; <a href="' + jQuery(this).attr("href") + '" target="_blank">' + fullPdfText + '</a>';
							}
							pdf_title += '</div>';
							return pdf_title;
						},
			onComplete: function() { longTitles(); }
		});

		// use Google Docs Viewer for pdf's if theme option is set.
		if(useGviewer === 1) {
			jQuery("a[type$=pdf].gallery").colorbox({
				scrolling:	false, // the gviewer has a scrollbar.
				html:		function(){
								var mid = qstring('mid', jQuery(this).attr("href"));
								return '<iframe width="100%" height="100%" src="http://docs.google.com/viewer?url=' + WT_SERVER_NAME + WT_SCRIPT_PATH + WT_THEME_JUSTBLACK + 'pdfviewer.php?mid=' + mid + '&embedded=true"></iframe>';
							},
				onComplete: function() {
							longTitles();
							if(useWatermark === 1) {
								var layerHeight = jQuery('#cboxContent iframe').height();
								var layerWidth = jQuery('#cboxContent iframe').width();
								jQuery('#cboxLoadedContent')
									.append('<div class="pdf-menu"></div>' +
											'<div class="pdf-body">' +
												'<div class="pdf-watermark"><span class="text-right">' + WT_TREE_TITLE + '</span></div>' +
											'</div>');
								jQuery('.pdf-menu').css({
									 'width'	: layerWidth + 'px'
								});
								jQuery('.pdf-body').css({
									 'height'	: layerHeight - 37 +'px',
									 'width'	: layerWidth - 17 + 'px'
								});
								jQuery('.pdf-watermark').css({
									 'margin-top'	: ((layerHeight - 37)/2) - 48 +'px'
								});
							}
						}
			});
		}
		// use browsers default pdf viewer
		else {
			jQuery("a[type$=pdf].gallery").colorbox({iframe:	true});
		}

		// Do not open the gallery when clicking on the mainimage on the individual page
		jQuery('a.gallery').each(function(){
			if(jQuery(this).parents("#indi_mainimage").length > 0) {
				jQuery(this).colorbox({rel:"nofollow"});
			}
		});
	});

	/********************************************* TOOLTIPS ***********************************************/
	// Tooltips for all title attributes
	function add_tooltips() {
		jQuery('*[title]').each(function() {
            title = jQuery(this).attr('title');
			jQuery(this).on('click', function(){
				jQuery(this).attr('title', title);	// some functions need the title attribute. Make sure it is filled when clicking the item.
			});
        });

		jQuery(document).tooltip({
			items: '*[title]:not(.ui-dialog-titlebar-close)'
		});
	}

	add_tooltips();	// needed when no ajaxcall is made on the particular page.
	jQuery(document).ajaxComplete(function() { // be sure the tooltip is activated after a ajax call is made.
		add_tooltips();
	});

	/******************************************* DROPDOWN MENU *********************************************/
	jQuery('.dropdown > li').hover(function(){
		jQuery(this).find('ul').show();
	}, function(){
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
	jQuery('#main-menu').each(function(){
		var dTime, li_height, height, maxHeight, i;
		
		jQuery(this).find('li').hover(function(){
			//show submenu
			jQuery(this).find('>ul').slideDown('slow');
		},function () {
			//hide submenu
			jQuery(this).find('>ul').hide();
		});

		dTime = 1200;
		jQuery(this).find('ul').each(function(){
			jQuery(this).find('li').hover(function() {
				jQuery(this).stop().animate({backgroundColor: '#808080'}, dTime);
			}, function(){
				jQuery(this).stop().animate({backgroundColor: '#272727'}, dTime);
			});
		});

		// dynamic height of menubar
		li_height = jQuery(this).find('> li').height();
		height = jQuery(this).find('> li > a').map(function(){
   			return jQuery(this).height();
		});
		maxHeight=height[0];
		for (i = 0; i<height.length; i++) {
		 	maxHeight = Math.max(maxHeight, height[i]);
		}
		jQuery('#topMenu').css('height', li_height + maxHeight);

		// No Gedcom submenu if there is just one gedcom
		if (jQuery('#menu-tree ul li').length === 1) {
			jQuery('#menu-tree ul').remove();
		}

		// open admin in new browsertab
		jQuery(this).find('ul li#menu-admin a').attr('target','blank');
	});

	/********************************************* LANGUAGE (FLAGS) MENU ******************************************/
	jQuery('#optionsmenu #lang-menu').each(function(){
		jQuery(this).find('li').each(function(){
			jQuery(this).tooltip({
				position: {
					my: "center top-40",
					at: "center center"
				}
			});
			jQuery(this).click(function(){
				location.href = jQuery(this).find('a').attr('href');
			});
			jQuery(this).find('a.lang-active').removeClass().parent('li').addClass('lang-active');
		});
	});

	/********************************************* FAV-MENU ******************************************/
	var pageId = qstring('pid') || qstring('famid') || qstring('mid') || qstring('nid') || qstring('rid') || qstring('sid');
	var submenu = jQuery('#fav-menu > ul ul');
	
	obj = submenu.find('li');
	if (WT_USER_ID > 0 && pageId !== undefined) {
		obj = submenu.find('li').not(':last');	
		submenu.find('li:last a').addClass('addFav');
	}

	obj.each(function(){
		var url = jQuery(this).find('a').attr('href');
		var id = qstring('pid', url) || qstring('famid', url) || qstring('mid', url) || qstring('nid', url) || qstring('rid', url) || qstring('sid', url);
		if (id === pageId) {
			jQuery(this).addClass('active');
			jQuery('#menu-favorites > a').replaceWith(jQuery(this).html());
			jQuery('.addFav').parent('li').remove();
		}
	});

	obj.click(function(){
		jQuery('#menu-favorites > a').replaceWith(jQuery(this).html());
	});

	sortMenu('#fav-menu');

	/**************************************** MODAL DIALOG BOXES ********************************************/
	// replace default function with our justblack theme function (better dialog boxes)
	function jb_dialogBox() {
		jQuery('[onclick^="helpDialog"]').each(function(){
			jQuery(this).attr('onclick',function(index,attr){
				return attr.replace('helpDialog', 'jb_helpDialog');
			});
		});

		jQuery('[onclick^="modalDialog"], [onclick^="return modalDialog"]').each(function(){
			jQuery(this).attr('onclick',function(index,attr){
				return attr.replace('modalDialog', 'jb_modalDialog');
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

		jQuery('#login-register-page #register-form label').each(function(){
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
		jQuery('#edituser-table input:first[type=password]').each(function(){
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

	// link change block styling. In the default styling the text does not fit in the block.
	jQuery('#link_change_blocks a').after('<br />');

	// gedcom and user favorites block
	jQuery('.block .gedcom_favorites_block .action_header, .block .gedcom_favorites_block .action_headerF, .block .user_favorites_block .action_header, .block .user_favorites_block .action_headerF').each(function(){
		jQuery(this).removeClass('person_box');
	});

	/************************************** INDIVIDUAL PAGE ***********************************************/
	if (WT_SCRIPT_NAME === 'individual.php') {

		// General
		jQuery('<div class="divider">').appendTo('#tabs ul:first');
		jQuery('#tabs li').each(function(){
			jQuery(this).tooltip({
				position: {
					my: "center top+25",
					at: "center center"
				}
			});
		});

		jQuery('#tabs a[title=lightbox]').on('click', function(){
			var tabindex = jQuery(this).parent().attr('aria-controls');
			if(jQuery('#jb-loading-image').length === 0) {
				jQuery('#' + tabindex).before('<div id="jb-loading-image" class="loading-image"></div>').hide();
				jQuery.ajax({
					complete:function(){
						jQuery('#lightbox_content img.icon').each(function(){
							jQuery(this).attr('src',function(index,attr){
								return attr.replace('modules_v3/lightbox/images', WT_CSS_URL + 'images/buttons');
							});
							jQuery(this).css('padding-left', '5px');
						});
						jQuery('#jb-loading-image').hide();
						jQuery('#' + tabindex).show();
					}
				});
			}
		});

		if (jQuery('#tabs a[title=lightbox]').parent('li').hasClass('ui-state-active')) {
			setTimeout(function() {
				jQuery('#tabs a[title=lightbox]').trigger('click');
			}, 10);
		}
	}

	/********************************************* MESSAGES.PHP*******************************************************/
	// correction. Popup is smaller than the input and textarea field.
	if (WT_SCRIPT_NAME === 'message.php') {
		jQuery('input[name=subject]').attr('size', '45');
		jQuery('textarea[name=body]').attr('cols', '43');
	}
	
	/************************************************ PERSON BOXES *****************************************************/
	//Remove labels with empty fields from the personboxes.
   	jQuery('div[class^=fact_]').each(function(){
		obj = jQuery(this);
		obj.find('span.field').each(function(){
			if(jQuery.trim(jQuery(this).text()) === '') {
				obj.remove();
			}		
		});		
	});
	
	// replace the default function with our own to customize the zoomed personbox view
	jQuery('[onclick^="expandbox"]').each(function(){
		jQuery(this).attr('onclick',function(index,attr){
			return attr.replace('expandbox', 'jb_expandbox');
		});		
	});
	
	/************************************************ HOURGLASS CHART *****************************************************/
	function styleSB(){
		 jQuery.ajax({
			success:function(){
				jQuery('.person_box_template.style1').each(function(){
					var width = jQuery(this).width();
					if(width < 250) { // spouses boxes are smaller then the default ones.
						jQuery(this)
							.addClass('spouse_box')
							.removeAttr('style') // css styling
							.closest('table').find('tr:first .person_box_template').css('border-bottom-style', 'dashed');
					}
				});
			},
			complete:function(data) {
				jQuery('a[onclick*=ChangeDis]').on('click', function(event){	// needed for dynamic added arrow links.
					styleSB();
				});
				return data;
			}
		 });
	}
	if (WT_SCRIPT_NAME === 'hourglass.php' && qstring('show_spouse') === '1') {		
		jQuery('a[onclick*=ChangeDis]').on('click', function(){
			styleSB();
		});
		styleSB();
	}

	/****************************** CHILDBOX (ON PEDIGREE CHART AND HOURGLASS CHART)***************************************/
	if (WT_SCRIPT_NAME === 'pedigree.php' || WT_SCRIPT_NAME === 'hourglass.php') {
		jQuery('#hourglass_chart #childbox .name1').each(function(){
			jQuery(this).appendTo(jQuery(this).parents('#childbox'));
		});
		jQuery('#hourglass_chart #childbox table').remove();
		jQuery('#hourglass_chart #childbox').removeAttr('style');

		jQuery('#childbox').each(function(){
			var childbox = jQuery(this);
			childbox.find('br').remove();
			childbox.wrapInner('<ul>');
			childbox.find('a').wrap('<li>');
			childbox.find('ul > span').wrap('<li class="cb_title">');
			childbox.find('span.name1').each(function(){
				var sChar = '<';
				var str = jQuery(this).text();
				if (str.indexOf(sChar) > -1) {
					var newStr = str.replace(sChar, '');
					jQuery(this).text(newStr);
					jQuery(this).parents('li').addClass('cb_child');
				}
			});

			var li_child = jQuery('#hourglass_chart #childbox').parent().prev('table').find('.popup li.cb_child');
			li_child.each(function(){
				var child = jQuery(this).text();
				jQuery('#hourglass_chart #childbox li').each(function(){
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

		if(jQuery('#hourglass_chart #childbox').length > 0) {
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
		jQuery('table.person_box td').each(function(){
			var content = jQuery(this).html();
			jQuery(this).parents('table').before('<div class="fanchart_box">' + content + '</div>').remove();
		});

		jQuery('.fanchart_box').each(function(){
			var fanbox = jQuery(this);
			fanbox.find('br').remove();
			fanbox.wrapInner('<ul>');
			fanbox.find('a').wrap('<li>');
			fanbox.find('ul > span').wrap('<li class="fb_title">');
			fanbox.find('a.name1').each(function(){
				var sChar = '<';
				var str = jQuery(this).text();
				if (str.indexOf(sChar) > -1) {
					var newStr = str.replace(sChar, '');
					jQuery(this).text(newStr);
					jQuery(this).parents('li').addClass('fb_child');
				}
			});
			fanbox.find('li:first').addClass('fb_indi');
			fanbox.find('.fb_child').prev('li').not('.fb_child').addClass('fb_parent');
			fanbox.find('.fb_child').appendTo(jQuery('.fb_child').prev('.fb_parent'));
			fanbox.find('.fb_parent').each(function(){
				jQuery(this).find('.fb_child').wrapAll('<ul>');
			});
			fanbox.find(' > ul > li:not(.fb_title)').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
			fanbox.find('.fb_child').prepend('<span class="ui-icon ui-icon-person left">');
		});
	}

	/************************************** TREE VIEW ***********************************************/
	// load custom treeview stylesheet. Be sure it is loaded after treeview.css
	function include_css(css_file) {
		var html_doc = document.getElementsByTagName("head")[0];
		var css = document.createElement("link");
		css.setAttribute("rel", "stylesheet");
		css.setAttribute("type", "text/css");
		css.setAttribute("href", css_file);
		html_doc.appendChild(css);
	}
	
	if (WT_SCRIPT_NAME === 'individual.php' || qstring('mod_action') === 'treeview') {
		include_css(WT_CSS_URL + 'treeview.css');
	}
	
	/************************************** FAMILY BOOK ***********************************************/
	if (WT_SCRIPT_NAME === 'familybook.php') {
		jQuery('hr:last').remove(); // remove the last page-break line because it is just above the justblack divider.
	}

	/************************************** MEDIALIST PAGE ********************************************/
	if (WT_SCRIPT_NAME === 'medialist.php') {
		// Medialist Menu
		jQuery('.lightbox-menu').parent('td').each(function(){
			jQuery(this).wrapInner('<div class="lb-image_info">');
			jQuery(this).find('.lightbox-menu').prependTo(jQuery(this));
		});

		jQuery('.lightbox-menu .lb-menu li ul').wrap('<div class="popup">');

		jQuery('.lightbox-menu .lb-menu > li > a').each(function(){
			var tooltip, pos;
			tooltip	= jQuery(this).text();
			if(jQuery(this).hasClass('lb-image_link')) {
				jQuery(this).parent().find('.popup ul').prepend('<li class="lb-pop-title">' + tooltip);
			}
			else {
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

		jQuery('.lb-menu .lb-image_link').parent().hover(function(){
			jQuery(this).find('.popup').fadeIn('slow');
		},
		function(){
			jQuery(this).find('.popup').fadeOut('slow');
		});

		// media link list
		jQuery(".lb-image_info").each(function(){
			jQuery(this).find('> a').addClass("media_link").next('br').remove();
			jQuery(this).find('.media_link').wrapAll('<div class="media_link_list">');
		});
	}

	/************************************** MEDIAVIEWER PAGE ******************************************/
	if (WT_SCRIPT_NAME === 'mediaviewer.php') {
		jQuery('#media-tabs').find('.ui-widget-header').removeClass('ui-widget-header');
		jQuery('#media-tabs ul').after('<div class="divider">');
	}

	/********************************************* SMALL THUMBS *****************************************************/
	// currently small thumbs (on the sourcepage for instance) are having a height of 40px and a width of auto by default.
	// This causes a messy listview.
	// In style.css the default height changed to 45px. Use this function to retrieve a cropped 60/45 (4:3) image.
	// It would be better to do this on the server side, but then we have to mess with the core code.
	jQuery('.media-list td img').each(function(){
		obj = jQuery(this);
		var src = obj.attr('src');
		var img = new Image();
		img.onload = function() {
			var newWidth = 60,
			ratio = newWidth/this.width,
			newHeight = this.height * ratio,
			marginLeft = 0;
			
			if(newHeight < 45) {
				newHeight = 45;
				ratio = newHeight/this.height;
				newWidth = this.width * ratio;
				marginLeft = -(newWidth - 60)/2;
			}
			obj.css({
				'width'  		: newWidth,
				'height' 		: newHeight,
				'margin-left'	: marginLeft
			});
		};
		img.src = src;
		var $div = jQuery('<div>').css({
			'width' 	: '60px',
			'display' 	: 'inline-block',
			'overflow' 	: 'hidden'
		});
		obj.parent().wrap($div);
		obj.parents('td').css('text-align', 'center');
	});

	/************************************** CALENDAR PAGE ********************************************/
	if (WT_SCRIPT_NAME === 'calendar.php') {
		jQuery('.icon-indis, .icon-cfamily').parent().addClass('ui-state-default');
		jQuery('.icon-sex_m_15x15').removeClass().addClass('icon-sex_m_9x9');
		jQuery('.icon-sex_f_15x15').removeClass().addClass('icon-sex_f_9x9');
		jQuery('#calendar-page li').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
	}

	/************************************** CLIPPINGS PAGE ********************************************/
	if(qstring('mod') === 'clippings') {
		jQuery('#content').addClass('clippings-page');
		jQuery('.clippings-page li').prepend('<span class="ui-icon ui-icon-triangle-1-e left">');
		jQuery('.clippings-page .topbottombar').addClass('ui-state-default descriptionbox').removeClass('topbottombar');
		jQuery('.clippings-page h2').parent('td').removeClass();
		jQuery('.clippings-page input[type=submit]').parent('td').removeClass().css('text-align', 'right');
		
		if(jQuery('.clippings-page h3').length > 0) {
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
			searchResult.each(function(){
				jQuery(this).find('ul').append('<li id="search-btn" class="ui-state-default ui-corner-top"><a href="#search"><span>' + titleBtn);
				jQuery(this).find('.ui-tabs-nav, .fg-toolbar').removeClass('ui-widget-header');
			});

			jQuery('li#search-btn').on({
				mouseenter: function(){
					jQuery(this).addClass('ui-state-hover');
				},
				mouseleave: function(){
					jQuery(this).removeClass('ui-state-hover');
				},
				click: function(){
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
		if(searchResult.length > 0) {
			searchForm.hide();
			searchResult.find('div[class^=filtersH]').append('<button id="search-btn" class="ui-state-default" type="button">' + titleBtn);

			jQuery('#search-btn').on({
				click: function(){
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
		jQuery('#place-hierarchy').each(function(){
			jQuery(this).find('.list_label').addClass('ui-state-default');
			jQuery(this).find('.icon-place').remove();
			jQuery(this).find('.list_table li a').before('<span class="ui-icon ui-icon-triangle-1-e left">');
			jQuery(this).find('table:first').prependTo('#places-tabs');
			jQuery(this).find('#places-tabs .ui-widget-header').removeClass('ui-widget-header');
			jQuery(this).find('#places-tabs ul.ui-tabs-nav').after('<div class="divider">');
		});
	}

	/************************************* OTHER *******************************************/
	// Correction. On default pdf opens on the same page. We do not want to force users to use the browser back button.
	jQuery('#reportengine-page form').attr("onsubmit", "this.target='_blank'");

		// styling of the lifespan module
	jQuery('.lifespan_people .icon-sex_m_9x9').parents('#inner div[id^="bar"]').css({'background-color':'#545454', 'border':'#dd6900 1px solid'});
	jQuery('.lifespan_people .icon-sex_f_9x9').parents('#inner div[id^="bar"]').css({'background-color':'#8E8E8E', 'border':'#dd6900 1px solid'});
	jQuery('.lifespan_people a.showit i.icon-sex_m_9x9, .lifespan_people a.showit i.icon-sex_f_9x9').hide();

	// scroll to anchors
	jQuery(".scroll").click(function(event){
		var id = jQuery(this).attr("href");
		var offset = 60;
		var target = jQuery(id).offset().top - offset;
		jQuery("html, body").animate({scrollTop:target}, 1000);
		event.preventDefault();
	});

	// open all external links in new window/tab - Not sure if this function is still neccessary. See WT_Filter::expandUrls
	jQuery("a[href^=http]").each(function(){
      if(this.href.indexOf(location.hostname) === -1) {
         jQuery(this).attr({
            target: "_blank"
         });
      }
   });
});
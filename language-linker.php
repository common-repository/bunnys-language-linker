<?php
/*
Plugin Name: Bunny's Language Linker
Plugin URI: http://climbtothestars.org/archives/2007/12/28/bunnys-language-linker-new-wordpress-plugin/
Description: Helps automate linking between equivalent pages in different languages over separate WordPress installs. You need to edit the plugin file before you can use it.
Version: 0.2
Author: Stephanie Booth
Author URI: http://climbtothestars.org/


  Copyright 2007-2008  Stephanie Booth  (email : stephanie.booth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

INFORMATION:
============

This plugin assumes you have two or more WordPress installations in different languages for the same site. It helps you create links between pages that are different language versions of one another over the different WordPress installations. 

You should have a pattern of parallel sites with urls like http://stephanie-booth.com/en/, http://stephanie-booth.com/de/, http://stephanie-booth.com/fr/. Careful! This only works for Pages, not posts!

If you are using Sandbox or a sandbox-based theme, the plugin will automatically add the language links to the end of the main navigation menu. Otherwise, the links will be added to the end of the post content, in div#bll-lang.

DISCLAIMER:
===========

As far as I could see, this plugin seems to work. It has not, however, been tested systematically. So... as with everything these days, keep an eye open for problems and make regular backups. Let me know if you spot anything fishy. Thanks!

CHANGELOG:
==========

0.1 - Initial release - 28.12.2007
0.2 - Automatic injection of language links in menu bar for Sandbox-based themes - 01.01.2008 

SETTINGS:
=========

Replace "fr" with the language of your other WordPress installation in the array below, with the proper two-letter code. You can add more languages like this: array('en', 'de', 'it'). In the second line here, replace "en" with the language of the WordPress installation this plugin is being installed on.

*/

$bll_other_languages=array('fr');
$bll_this_language="en";


// THE PLUGIN:
// ===========

// GENERAL
// this is because the name of the variable (postmeta key) for the slug changes with the language we're looking at, and if it wasn't in a function I'd be rewriting it everywhere.

function bll_get_slug_key($lang)
{
	$slug_key= $lang . "_slug";
	return($slug_key);
	}


// retrieve the slug of the post in language $lang
function bll_get_the_slug($lang)
{	
	$slug_key= bll_get_slug_key($lang);
	$language_slug=get_post_custom_values($slug_key);
	$slug=$language_slug['0'];
	return($slug);
} 


// TEMPLATE FUNCTIONS


// function which returns the code (link) for one other language
function bll_make_the_slug_link($lang, $before='<li>', $after='</li')
{	
	$the_slug=bll_get_the_slug($lang);
	if(!empty($the_slug))
	{
	$link=$before . '<a href="/' . $lang . '/' . $the_slug . '/" hreflang="' . $lang . '">[' . $lang . ']</a>' . $after;
	return($link);
	}
}

// function which returns the code for all other languages
function bll_make_slug_links($before_language, $after_language, $before='', $after='')
{
	global $bll_other_languages;
	
	foreach($bll_other_languages as $lang)
	{
		$links .= bll_make_the_slug_link($lang, $before_language, $after_language);
	}
	if(!empty($links))
	{
		$links = $before . $links . $after;
		return($links);
	}
}


// for sandbox themes: insertion of slug links (in the menu <ul> of sandbox by default)
function bll_embed_slug_links_well($buffer) {
		ob_start('bll_insert_slug_links');
}

function bll_insert_slug_links($buffer) {		// For Sandbox-based themes: where should the links go? use identifiable string coming just after 
		// links; default places language links in the menu bar for sandbox themes, 
		// identified by </ul></div>    </div><!-- #access -->
		// modify as necessary, check out http://ch2.php.net/reference.pcre.pattern.syntax for syntax help

	    $search='/(<\/ul><\/div>(\s)*<\/div><!-- #access -->)/';      
        $replace = bll_make_slug_links('<li>', '</li>') . "\\1";        return preg_replace($search, $replace, $buffer);
      }

// 3 deprecated functions useful for custom placement of links or non-Sandbox themes
// automatic insertion of slug links after content
function bll_embed_slug_links($content) {
	    $content = $content . bll_make_slug_links('', '', '<div id="bll-lang">', '</div>');
        return $content;
}

// function which prints the slug (if needed manually)
function bll_just_the_slug($lang)
{
	$slug=bll_get_the_slug($lang);
	print($slug);
}

// this prints out the slug links manually, if needed
function bll_the_slug_links($after_language='', $before_language='', $before='<div id="bll-lang">', $after='</div>')
{
	print(bll_make_slug_links($before, $after, $before_language, $after_language));
}


// ADMIN FUNCTIONS

// this function outputs a little box for typing in the slugs of the post/page in a given language (admin pages)

function bll_add_slug_boxes()
{
 	global $bll_other_languages;

 	print('<fieldset id="otherslugs" class="dbx-box">
 	      <h3 class="dbx-handle">');
      echo __('Other Language Slugs');
      print('</h3>
	  <div class="dbx-content">');
	  foreach($bll_other_languages as $lang)
	  {
	  	print(bll_slug_box($lang));
	  	print('<br />');
	  }
	  // hidden field to avoid vanishing meta
 echo '<input type="hidden" name="bunny-bll-key" id="bunny-bll-key" value="' . wp_create_nonce('bunny') . '" />'; 
	  print('
	  </div>
</fieldset>');
}

// this function writes the text input for one language slug

function bll_slug_box($lang)
{
	global $post;
	$slug_key = bll_get_slug_key($lang);
	// retrieving existing slugs
	$existing_slug=get_post_meta($post->ID, $slug_key, true);
	$slug_box = '<label for="' . $slug_key . '">' . $lang . '</label> <input type="text" name="' . $slug_key . '" size="15" value="' . $existing_slug . '" id="' . $slug_key . '" />';
	return($slug_box);
}

// ACTION FUNCTIONS

// general custom field update function
function bll_update_meta($id, $field)
{
	// authorization to avoid vanishing meta
    if ( !current_user_can('edit_post', $id) )
        return $id;
    // origination and intention to avoid vanishing meta
    if ( !wp_verify_nonce($_POST['bunny-bll-key'], 'bunny') )
       return $id;
	$setting = stripslashes($_POST[$field]);
	$meta_exists=update_post_meta($id, $field, $setting);
	if(!$meta_exists)
	{
		add_post_meta($id, $field, $setting);	
	}
}

// update custom slug field for given language
function bll_update_slug($id, $lang)
{
	$slug_key = bll_get_slug_key($lang);	
	bll_update_meta($id, $slug_key);
}

// update all custom slug fields

function bll_update_slugs($id)
{
	global $bll_other_languages;
	foreach($bll_other_languages as $lang)
	{
		bll_update_slug($id, $lang);
	}
}

// check if theme is Sandbox or Sandbox-based

function bll_got_sandbox() {
    $theme_uri = get_stylesheet_uri();
	$theme_data = get_theme_data($theme_uri);
	$theme_name = $theme_data['Name'];
	$theme_template = $theme_data['Template'];
	if($theme_name=="Sandbox" || $theme_template=="sandbox" || $theme_name=="sandbox" || $theme_template=="Sandbox")
	{
		return true;
		}
}


add_action('dbx_page_sidebar', 'bll_add_slug_boxes');


add_action('edit_post', 'bll_update_slugs');
add_action('save_post', 'bll_update_slugs');
add_action('publish_post', 'bll_update_slugs');

if(bll_got_sandbox())
{	add_action('template_redirect', 'bll_embed_slug_links_well');
}else{
	add_action('the_content', 'bll_embed_slug_links');
}
?>

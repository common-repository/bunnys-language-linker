=== Plugin Name ===
Contributors: Steph
Donate link: http://www.amazon.de/exec/obidos/wishlist/3ZN17IJ7B1XW/
Tags: multilingual, language, translation, links, l10n, localization, internationalisation, i18n
Requires at least: 2.0
Tested up to: 2.3.1
Stable tag: trunk

Helps you create links between equivalent (but different language) pages between WordPress installations.

== Description ==

This plugin assumes you have a site in two or more languages, and that you are using a separate WordPress install for each language. It helps you create links between the same pages in different 
language versions.

You should have a pattern of parallel sites with urls like http://stephanie-booth.com/en/, http://stephanie-booth.com/de/, http://stephanie-booth.com/fr/. Maybe the page 
http://stephanie-booth.com/en/about is equivalent to the page http://stephanie-booth.com/fr/a-propos. The plugin adds a DBX box in the edit/create page admin form, named "Other Language Slugs". In 
this case, you'd edit the "about" page, and paste the fr slug ("a-propos") in the appropriate field.

The plugin will then display a small link to the page in the other language at the bottom of your page, or in your menu bar if you're using a Sandbox-based theme.

Careful! This only works for Pages, not posts!

For intelligent user redirection when they land on the home page, you might want to try using the technique described here: http://climbtothestars.org/archives/2007/12/28/browser-language-detection-and-redirection/


== Installation ==

1. Upload the `language-linker` directory into `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Edit the settings at the beginning of the plugin file to indicate what languages you are working with.
1. Start linking!

== Frequently Asked Questions ==

= Is this compatible with other language/translation plugins?  =

No idea, sorry. Let me know if you discover anything exciting.

= Does it work with more than two languages? =

Yes. Unlike Basic Bilingual, Language Linker allows you to create links between as many different linguistic versions of your site as you wish. You just need to specify the languages in the settings 
at the top of the plugin file.

= Aren't there more complete plugins out there if I want to make all my content available in more than one language? =

Yes, there certainly are. My approach is to keep it simple and minimal, so that it works.

= Can I make the links to the other languages appear elsewhere? =

Yes, but you need to get your hands dirty. The function `bll_embed_slug_links` controls if the links go after or before the content. If you want them completely elsewhere, you need to comment out the 
line <code>add_action('the_content', 'bll_embed_slug_links');</code> by adding "//" in front of it (or deleting it, but that's not recommended), and use the template tag `bll_the_slug_links()`. Just 
paste `<?php bll_the_slug_links(); ?>` in your template where you want the links to appear.

= I don't like the [lg] notation, couldn't we have flags? =

[Flags should never be used as a symbol of language](http://www.cs.tut.fi/~jkorpela/flags.html). Languages and countries don't match. If you'd rather replace the square brackets with curly ones or 
other fancy stuff, you can try editing the function `bll_make_the_slug_link()` if you can figure it out.

== Screenshots ==

1. Page editing screen with Language Linker installed.
2. What the link looks like at the end of the post.

== Future Development ==

Here's what I'd like this plugin to do, someday:

- automatically capture URLs like http://stephanie-booth.com/en/a-propos and redirect them to http://stephanie-booth.com/en/about (that's what happens when people edit URLs directly, just replacing 
the language code -- I do it all the time)

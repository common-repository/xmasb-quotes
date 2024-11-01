=== XmasB Quotes ===
Contributors: XmasB
Donate link: http://xmasb.com/xmasbquotes/
Tags: quotes, widget, sidebar, xmasb, plugin, image, random
Requires at least: 2.0.2
Tested up to: 3.3
Stable tag: 1.6.1

Add random quotes with image to your Wordpress blog with this widget.

== Description ==

XmasB Quotes lets you add and show randow quotes to your wordpress blog with ease. It uses the db to store quotes. 
You can specify an image for each quote, deafult image for quotes, or disable images. 
Now with the option to use quotes as links! 

Please rate this plugin if you like it!

For support and questions please visit [the plugin page](http://xmasb.com/xmasbquotes)

XmasB Quotes is available in the following languages:

* English (base language - feel free to make suggestions if you want
* Belarusian - by [Fat Cow](http://www.fatcow.com)
* Dutch - by Rene at [WordPress Webshop](http://wpwebshop.com/premium-wordpress-plugins/)
* French - by Farida at [Traducteurs.com](http://www.traducteurs.com/)
* German - by [Alariel](http://www.alariel.de/blog/)
* Italian - by [gidibao](http://gidibao.net/)
* Norwegian - by [Kristin K. Wangen](http://zhayena.net/) / [Yngve Thoresen](http://xmasb.com)
* Russian - by Flector at [WordPressPlugins.ru](http://www.wordpressplugins.ru/)
* Spanish - by [Carlos](http://www.elquintosuyo.com/)
* Swedish - by [Rabatt](http://rabatt.se/)

== Installation ==

Install XmasB Quotes directly from the WordPress admin:
1. Visit the Plugins/Add New page and search for 'XmasB Quotes'.
1. Click to install.
1. Activate the plugin
1. Add the widget to your sidebar.

Manually:
1. Download and unzip the plugin from 'http://wordpress.org/extend/plugins/xmasb-quotes/'.
1. Upload directory `xmasb_quotes` to the `/wp-content/plugins/` directory
1. Activate the plugin
1. Add the widget to your sidebar.

By deault the plugin looks for an image in the folder named "images" with a name that matches the author, eg. if the author is "Bart", the plugin will look for "bart.gif". If an image is found it is used, else the quotes is shown without an image or default image if specified.

Add, edit and delete quotes under Edit - XmasB Quotes.  
The options for each quote:

Quote - The actual quote.  
Author - The author of the quote. This is optional.  
Image - The image to be used. If left blank, the name of the author will be used to search for an image.  
Visible - Set this to "No" if you want to hide a quote without deleting it.

Set more general options in the XmasB quotes page under Options in your dashboard.  
Options available:

Show Images - Uncheck this to disable images.  
Default image - If specified this image will be used if no image is found for author or specified image for quote. If extension for default image is not specified, the plugin looks for png, jpg, jpeg and gif images, in that order.
Show link to author - Shows a link to XmasB.com and my plugin in the widget. Turn off if you want to.

The title for the widget can be edited directly in the Widget:  
Title - The title to show with the Widget. Default is XmasB Quotes.

There is a shortcut for adding quotes almost anywhere you want: "[XmasBRandomQuote]".

Take a look at the FAQ if you have any questions, or check out the infopage on [XmasB.com.](http://xmasb.com/xmasbquotes). Most questions are solved in the comment section.

== Upgrade Notice ==

Be aware that upgrading the plugin will delete and replace all files. If you have any custom files (such as images or stylesheet) it is adviced to backup these before upgrading. I'm working on a solution to avoid this and apologize for any inconvenience this may have.

== Frequently Asked Questions ==

= Can I use links on my quotes? =
You sure can! Just insert the url you would like the quote to link to when adding/editing your quotes.

= Do you accept donations? =
Absolutely! Just visit [the plugin page](http://xmasb.com/xmasbquotes) and click on the button "Donate". Thanks a lot!

== Screenshots ==

1. The plugin is used on my homepage [XmasB.com.](http://xmasb.com)

== Support ==

For support and questions please visit [the plugin page](http://xmasb.com/xmasbquotes)

== Changelog ==

= 2012-01-03 - 1.6.1 =
Removed unused code that was causing problems for some users with older versions of PHP.

= 2011-08-13 - 1.6 =
Added an option for controlling who can manage quotes.

= 2011-07-19 - 1.5.6 =
Tested and confirmed working on WordPress version 3.2.1. (and development version (3.3-aortic-dissection))
Minor edits in readme to ease installation for new users.

= 2011-07-04 - 1.5.5 =
Swedish translation added thanks to [Rabatt](http://rabatt.se/).

= 2011-03-08 - 1.5.4 =
Bugfix for default image not apperaring.

= 2011-01-31 - 1.5.3 =
Minor update. Fixed an unclosed php tag. Tested and confirmed working with WordPress 3.0.4 and 3.2-bleeding.

= 2010-12-01 - 1.5.2 =
Tested and confirmed working on newest Wordpress version (3.0.2).

= 2010-11-26 - 1.5.1 =
Italian translation updated. Thanks again [gidibao](http://gidibao.net/).
POT file used for translating updated from source. Updated other translation files where I could.

= 2010-11-25 - 1.5.0 =
Image search is now case insensitive. Images with the same name as author (of type png, jpg, jpeg or gif) or a version without spaces and punctuation will be found.
Added spanish translation by [Carlos](http://www.elquintosuyo.com/). Thanks, Carlos!

= 2010-11-24 - 1.4.4 =
Images will now load for author with or without whitespaces. Example: A quote by Bart Simpson will now load image "BartSimpson.jpg" or "Bart Simpson.jpg". It will search without whitespaces first. Author and image must have same case. Will probably make search for image case insensitive soon (most of the code is in place for this now).
Cleaned up some code for easier reading and future versions.

= 2010-11-21 - 1.4.3 =
Dutch translation added thanks to Rene @ [WordPress Webshop](http://wpwebshop.com/premium-wordpress-plugins/).

= 2010-11-15 - 1.4.2 =
Changed head inclusion method of stylesheets and script. Stylesheet is loaded on all pages, while script is only loaded on admin pages.

= 2010-11-04 - 1.4.1 =
Fixed a bug where default image would not show. Improved logic for finding images.
Cleaned up old code.

= 2010-11-03 - 1.4 =
Changed the priority of picture types. The order is now png > jpg > gif. Changed included pictures to png format.

= 2010-10-06 - 1.3.9 =
Images should now work even if Wordpress is installed in another directory than wp-content.

= 2010-08-04 - 1.3.8 =
Fixed bugs with occuring slashes when editing quotes with special characters.

= 2010-08-04 - 1.3.7 =
Included sample CSS and made changes to default CSS. Future changes on the stylesheet (CSS) will only be made to the sample file.

= 2010-08-03 =
You can now optionally use links for the quotes. Because of a change in the database table, you will need to reactivate the plugin for the new changes to take effect.

= 2010-07-25 =
Fixed problem with apostrophes in quotes.

= 2010-06-29 =
Version 1.3.4.
Translation to french thanks to [Farida](http://www.traducteurs.com/) (French)

= 2010-03-03 =
Version 1.3.3.
Fixed some typos. Tested and working on newest Wordpress version (2.9.2).
Preparing for version 1.4.

= 2009-08-24 =
Version 1.3.2.
Translation to Belorussian thanks to [Fat Cow](http://www.fatcow.com/).

= 2009-08-12 =
Version 1.3.1.
Tested on Wordpress 2.8.3. Minor (cosmetic) fix on code.

= 2009-01-13 =
Version 1.3.
Removed link to xmasb.com from the Widget.

= 2008-09-03 =
Version 1.2.8.
German translation should be fixed. Fingers crossed... :)

= 2008-09-02 =
Version 1.2.7.
Added german translation. Thanks to [Alariel](http://www.alariel.de/blog/) (German).

= 2008-08-25 =
Bug introduced in version 1.2.5 caused me to revert the code. Temporary fix. Will add new version with code from Derek soon.

= 2008-08-25 =
Added functions to be able to pass a variable with how many random quotes you'd like to display. Thanks to Derek for the code and idea.

= 2008-07-17 =
Updated italian translation. Thanks to Gianni Diurno (aka gidibao). Link: [gidibao's Cafe](http://gidibao.net/) (Italian).
Added russian translation. Thanks to Flector at [WordPressPlugins.ru](http://www.wordpressplugins.ru/) (Russian).

= 2008-07-11 =
Minor bug fix.

= 2008-05-28 Version 1.2.2 =
New sql query for retreiving random quote faster on large databases. The new query has been tested to be 600% faster on a database containing about 15k quotes.

= 2008-05-22 Version 1.2.1 =
Added support for jpg and png images, in addition to gif.
Norwegian translation updated.

= 2008-05-14 Version 1.2.0 =
Added italian translation. Thanks to Gianni Diurno (aka gidibao) for the translation. Link: [gidibao's Cafe](http://gidibao.net/) (Italian).

More features coming. Future plans include:

* Export/import (to enable easier backups as well as "trading" of quotes) - in one big file or seperate for each author
* Search (for quotes and/or author)
* Better options for sorting

= 2008-05-09 Version 1.1.7 =
Added translation support and norwegian translation. Thanks to Kristin, for the much needed code and the translation.  
Link: [Kristin: blogg](http://kristin.norblogg.net/) (Norwegian).

= 2008-04-05 Version 1.1.6 =
Fixed bug that caused problems for some mysql versions. Default values for table has been removed.
Thanks to Don for the tip.

= 2008-04-03 Version 1.1.5 =
Added support for tags with attributes to be added before and after the code via "Options - XmasB Quotes". Thanks to Stephanie for the tip.

= 2008-04-01 Version 1.1.4 =
Fixed bug introduced with Wordpress 2.5 that caused the prefix to be dropped when creating the quote table.

= 2008-03-14 Version 1.1.3 =
Minor bugfix. Testet with Wordpress version 2.3.3.

= 2008-02-14 Version 1.1.2 =
Text/code to be used before and after author is now fixed.

= 2008-01-16 Version 1.1.1 =
Minor adjustment to the layout for editing quotes.

= 2008-01-14 Version 1.1 =
The plugin now checks for existing quotes when adding. A quote can not be added with the same author more than once. You can add the same quote with different authors.  
Exsisting duplicates for quotes can be seen in the page for editing. You can edit or delete from the list.  
Fixed bug when deleting quote from main form.

= 2008-01-03 Version 1.0 =
Version 1.0 released.  
Fixed several bugs.  
New design for managing quotes and options.  
Tested at many sites with positive feedback.

= 2007-12-28 Version 0.9.2 =
Fixed a minor bug that caused "Visibility" being set to false after edit.

= 2007-11-14 Version 0.9.1 =
Fixed a bug with the Edit page that caused the quotes to be shown wrongly.  
Fixed a spelling error in the .css that caused the not visible quotes to be shown wrongly in the Edit page.

= 2007-11-11 Version 0.9 =
Added support for managing code around image, quote and author.

= 2007-11-02 Version 0.8 =
Redesigned the entire code for easier updates in the future.

= 2007-10-29 Version 0.7.2 =
Separated options from Widget. Option for the plugin can now be found under Wordpress-Options. The title for the Widget is still set on the Widget.

= 2007-10-25 Version 0.7.1 =
Minor bugfix. Added centering in css. Adjustments to readme.txt.

= 2007-10-23 Version 0.7 =
Removed version checking of table. The plugin uses the dbDelta() function to handle any potential future changes to the table.

= Version 0.6.1 =
Fixed bug when creating table and adding quotes for the first time.  
Added content to css file.

= Version 0.6 =
Support for images added. You can now specify an image for each quote, use default image or turn images off.

= Version 0.4 =
Under development.
<?php
/*
Plugin Name: XmasB Quotes
Plugin URI: http://xmasb.com/xmasbquotes
Description: Add quotes with images and links to your WordPress blog
Version: 1.6.1
Author: Yngve Thoresen
Author URI: http://xmasb.com
License: GPL3
*/

/*  Copyright 2011 Yngve Thoresen

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Localization
load_plugin_textdomain('xmasbquotes', PLUGINDIR . '/xmasb-quotes/languages');

# Init Widget
add_action('init', 'widget_xmasb_quotes_init');

# CSS/JS inclusion in HEAD
add_action('template_redirect', 'xmasb_quotes_public_head_inclusion');
add_action('admin_print_scripts', 'xmasb_quotes_admin_head_inclusion');

add_action('activate_' . dirname(plugin_basename(__FILE__)) . '/' . basename(plugin_basename(__FILE__)), 'xmasb_quotes_install');
add_action('admin_menu', 'xmasb_quotes_add_pages');

add_filter('the_content','filter_xmasb_quotes_random_quote');

function xmasb_quotes_lang_init() {
	load_plugin_textdomain('xmasbquotes', PLUGINDIR . '/xmasb-quotes/languages');
}
add_action('init', 'xmasb_quotes_lang_init' );

global $wpdb;
define('XMASB_QUOTES_TABLE', $wpdb->prefix . 'xmasb_quotes');
define('XMASB_QUOTES_PLUGIN_DIR', WP_PLUGIN_DIR .'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
define('XMASB_QUOTES_PLUGIN_URL', WP_PLUGIN_URL .'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
define('XMASB_QUOTES_IMAGE_TYPES', 'png,jpg,gif,jpeg');

function filter_xmasb_quotes_random_quote($content) {
    $codetoreplace = "[XmasBRandomQuote]";
    $content=str_replace($codetoreplace,xmasb_get_random_quote(),$content);
    return $content;
}

function xmasb_quotes_install () {
	$sql = "CREATE TABLE " . XMASB_QUOTES_TABLE . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		author tinytext NOT NULL,
		quote text NOT NULL,
		imgsrc tinytext NOT NULL,
		link tinytext NOT NULL,
		visible boolean NOT NULL default 1,
		UNIQUE KEY id (id)
		);";

	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta($sql);
}

//Function to check if url exists
function xmasb_quotes_url_exists($url) {
	return file_exists($url);
} 

function xmasb_quotes_get_similar_image($author) {
	$imagetypes = explode(',',XMASB_QUOTES_IMAGE_TYPES);

	foreach($imagetypes as $imagetype){
		if (file_exists($author . '/' . $imagetype)) {
			return $author . '/' . $imagetype;
		}
	}
	$dir = dirname($author);
	$fileBasename = basename($author);

	$files = glob($dir . '/*');
	$lcaseFilename = strtolower($dir . '/' . $fileBasename);
	$lcaseFilenameStripped = strtolower($dir . '/' . preg_replace('/[^\w]/','', $fileBasename));
	
	
	foreach($files as $file) {
		foreach($imagetypes as $imagetype){
			if (strtolower($file) == $lcaseFilename . '.' . $imagetype) {
			  return $file;
			}
			if (strtolower($file) == $lcaseFilenameStripped . '.' . $imagetype) {
			  return $file;
			}
		}
	}
	return "";
}

function xmasb_quotes_print_showimage($quoteimg, $quote) {
		echo xmasb_quotes_get_showimage($quoteimg, $quote);
}

function xmasb_quotes_get_showimage($quoteimg, $quote) {
		$siteurl = get_option("siteurl");
		if ($siteurl[strlen($siteurl)-1] != "/") $siteurl .= "/";
		return '<div class="xmasb_quotes_image">
			<img src="'. $quoteimg . '" alt="' . $quote->author . '" />
			</div>';
}

function xmasb_print_random_quote() {
	echo xmasb_get_random_quote();
}

function xmasb_get_random_quote() {
	$return = "";
	global $wpdb;
	$table_name = $wpdb->prefix . "xmasb_quotes";
	$sql = "SELECT * FROM " . XMASB_QUOTES_TABLE . " where visible = 1 ORDER BY RAND() limit 1"; //Ineffective code - Slow with large tables
	//$sql = 'SELECT * FROM ' . XMASB_QUOTES_TABLE . ' T JOIN (SELECT FLOOR(MAX(ID)*RAND()) AS ID FROM ' . XMASB_QUOTES_TABLE . ') AS x ON T.ID >= x.ID and visible = 1 LIMIT 1';
	$quotes = $wpdb->get_results($sql);
	
	if ( !empty($quotes) ) 	{
		$return .= xmasb_quotes_get_quote($quotes[0], true);
	}else {
		$return .=  __('No quotes found', 'xmasbquotes');
	}
	return $return;
}

function xmasb_quotes_print_quote($quote, $showimage = false) {
	echo xmasb_quotes_get_quote($quote, $showimage = false);
}

function xmasb_quotes_get_codeforimage($quote) {
	$codeforimage = "";
	if (empty($quote->imgsrc)) {
		$codeforimage .= xmasb_quotes_get_codeforimage_by_author($quote);
	} else {
		$codeforimage .= xmasb_quotes_get_codeforimage_by_imgsrc($quote);
	}
	return $codeforimage;
}

function xmasb_quotes_get_codeforimage_by_imgsrc($quote) {
	$imagelocation = XMASB_QUOTES_PLUGIN_DIR . "images/";
	$options = get_option('widget_xmasb_quotes');
	$defaultimage = htmlspecialchars($options['defaultimage'], ENT_QUOTES);
	$codeforimage = "";
	$quoteimg = $imagelocation . $quote->imgsrc;
	if (xmasb_quotes_url_exists($quoteimg)) {
		$codeforimage .= xmasb_quotes_get_showimage($quoteimg, $quote);
	} elseif (!empty($defaultimage)) {
		//$codeforimage .= '<!-- XmasB Quotes: Image "' . $quoteimg . '" (by author) not found. -->';
		$quoteimg = $imagelocation . $defaultimage;
		if (xmasb_quotes_url_exists($quoteimg)) {
			$codeforimage .= xmasb_quotes_get_showimage($quoteimg, $quote);
		} else {
			//$codeforimage .= '<!-- XmasB Quotes: Default Image "' . $quoteimg . '" not found. -->';
		}
	} else {
		//$codeforimage .= '<!-- XmasB Quotes: Image "' . $quoteimg . '" specified for quoteid ' . $quote->id . ' not found. -->';
	}
	return $codeforimage;
}

function xmasb_quotes_get_codeforimage_by_author($quote) {
	$imagelocation = XMASB_QUOTES_PLUGIN_DIR . "images/";
	$options = get_option('widget_xmasb_quotes');
	$defaultimage = htmlspecialchars($options['defaultimage'], ENT_QUOTES);
	$codeforimage = "";
	$imagefound = false;
	$author = str_replace(' ', '', $quote->author);
	$quoteimg = $imagelocation . $author;

	$imageUrl = xmasb_quotes_get_similar_image($imagelocation . $quote->author);
	if(!empty($imageUrl)){
		$imageUrl = str_replace(WP_PLUGIN_DIR,WP_PLUGIN_URL,$imageUrl);
		$codeforimage .= xmasb_quotes_get_showimage($imageUrl, $quote);
		$imagefound = true;
	}
	
	if(!$imagefound && !empty($defaultimage)) {
		//$codeforimage .= '<!-- XmasB Quotes: Image "' . $quoteimg . '" (by author) not found. -->';
		$quoteimg = $imagelocation . $defaultimage;

		$rex = "/^.*\.(jpg|jpeg|png|gif)$/i";
		if(preg_match($rex, $quoteimg)) {
			if (xmasb_quotes_url_exists($quoteimg)) {
				$codeforimage .= xmasb_quotes_get_showimage($quoteimg, $quote);
				$imagefound = true;
			} else {
				//$codeforimage .= '<!-- XmasB Quotes: Default Image "' . $quoteimg . '" not found. -->';
			}
		} elseif (xmasb_quotes_url_exists($quoteimg . '.png')) {
			$codeforimage .= xmasb_quotes_get_showimage($quoteimg . '.png', $quote);
		} elseif (xmasb_quotes_url_exists($quoteimg . '.jpg')) {
			$codeforimage .= xmasb_quotes_get_showimage($quoteimg . '.jpg', $quote);
		} elseif (xmasb_quotes_url_exists($quoteimg . '.jpeg')) {
			$codeforimage .= xmasb_quotes_get_showimage($quoteimg . '.jpeg', $quote);
		} elseif (xmasb_quotes_url_exists($quoteimg . '.gif')) {
			$codeforimage .= xmasb_quotes_get_showimage($quoteimg . '.gif', $quote);
		} else {
			//$codeforimage .= '<!-- XmasB Quotes: Default Image "' . $quoteimg . '" not found. -->';
		}
	} else {
		//$codeforimage .= '<!-- XmasB Quotes: Image "' . $quoteimg . '" (by author) not found. -->';
	}
	return $codeforimage;
}

function xmasb_quotes_get_quote($quote, $showimage = false) {
	$quote->quote = stripslashes($quote->quote);
	$return = "";
	if (is_plugin_page()) {
		$return .= '<form action="" method="post">';
	}
	$options = get_option('widget_xmasb_quotes');
	$showimages = htmlspecialchars($options['showimages'], ENT_QUOTES) == 'yes' ? true : false;

	$htmlbeforeimage = $options['htmlbeforeimage'];
	$htmlafterimage = $options['htmlafterimage'];
	$htmlbeforequote = $options['htmlbeforequote'];
	$htmlafterquote = $options['htmlafterquote'];
	$htmlbeforeauthor = $options['htmlbeforeauthor'];
	$htmlafterauthor = $options['htmlafterauthor'];

	$codeforimage = "";
	if ( $showimage && $showimages)	{
		$codeforimage .= xmasb_quotes_get_codeforimage($quote);
	}
	if ( !empty($codeforimage) ) {
		$codeforimage = str_replace(WP_PLUGIN_DIR, WP_PLUGIN_URL, $codeforimage);
		$return .= $htmlbeforeimage . $codeforimage . $htmlafterimage;
	}
	
	if (is_plugin_page()) 	{
		$codeforquote = "";
		if ( $quote->visible ) {
			$codeforquote .= '<div class="xmasb_quotes_quote">' . $quote->quote . '</div>';
		} else {
			$codeforquote .= '<div class="xmasb_quote_not_visible">' . $quote->quote . '</div>';
		}
		if ( !empty($codeforquote) ) {
			$return .= $codeforquote;
		}
	} else {
		if ( empty($quote->link) ) {
			$return .= '<div class="xmasb_quotes_quote">' . $htmlbeforequote . $quote->quote . $htmlafterquote . '</div>';
		} else {
			$return .= '<div class="xmasb_quotes_quote"><a href="' . $quote->link . '">' . $htmlbeforequote . $quote->quote . $htmlafterquote . '</a></div>';		
		}
	}
	
	if ( !empty($quote->author) ) {
		if (is_plugin_page()) {
			$return .= '<div class="xmasb_quotes_author"><strong>' . $quote->author . '</strong></div>';
		} else {
			$return .= '<div class="xmasb_quotes_author"><strong>' . $htmlbeforeauthor . $quote->author . $htmlafterauthor . '</strong></div>';
		}
	}
	if (is_plugin_page()) {
		if ( !empty($quote->imgsrc) ) {
			$return .= '<div class="xmasb_quotes_imgsrc"><strong>' . $quote->imgsrc . '</strong></div>';
		}
		$return .= '<form action="" method="post">
		<input type="hidden" name="mode" value="edit">
		<input type="hidden" value="' . $quote->id . '" name="quoteID"/>
		<input class="button" type="submit" value="' . __('Edit','xmasbquotes') . '" name="Submit"/>
		<input class="button" type="submit" onclick="return delete_confirmation_xmasb_quotes()" value="'.__('Delete','xmasbquotes').'" name="mode"/>
		</form>';
	}
	return $return;
}

function xmasb_quotes_options_page() {
	$options = get_option('widget_xmasb_quotes');
	if ( !is_array($options) ) {
		$options = array('title'=>'XmasB Quotes', 'showimages'=>'yes', 'defaultimage'=>'quote.png', 'htmlbeforequote'=>'<q>', 'htmlafterquote'=>'</q>', 'rolerequired'=>'administrator');
	}
	if ( $_POST['xmasb_quotes-submit'] ) {
		$options['showimages'] = strip_tags(stripslashes($_POST['xmasb_quotes-showimages']));
		$options['defaultimage'] = strip_tags(stripslashes($_POST['xmasb_quotes-defaultimage']));
		$options['htmlbeforeimage'] = stripslashes($_POST['xmasb_quotes-htmlbeforeimage']);
		$options['htmlafterimage'] = stripslashes($_POST['xmasb_quotes-htmlafterimage']);
		$options['htmlbeforequote'] = stripslashes($_POST['xmasb_quotes-htmlbeforequote']);
		$options['htmlafterquote'] = stripslashes($_POST['xmasb_quotes-htmlafterquote']);
		$options['htmlbeforeauthor'] = stripslashes($_POST['xmasb_quotes-htmlbeforeauthor']);
		$options['htmlafterauthor'] = stripslashes($_POST['xmasb_quotes-htmlafterauthor']);
		$options['rolerequired'] = stripslashes($_POST['xmasb_quotes-rolerequired']);
		update_option('widget_xmasb_quotes', $options);
	}

	$showimages = htmlspecialchars($options['showimages'], ENT_QUOTES);
	$defaultimage = htmlspecialchars($options['defaultimage'], ENT_QUOTES);
	$htmlbeforeimage = htmlspecialchars($options['htmlbeforeimage'], ENT_QUOTES);
	$htmlafterimage = htmlspecialchars($options['htmlafterimage'], ENT_QUOTES);
	$htmlbeforequote = htmlspecialchars($options['htmlbeforequote'], ENT_QUOTES);
	$htmlafterquote = htmlspecialchars($options['htmlafterquote'], ENT_QUOTES);
	$htmlbeforeauthor = htmlspecialchars($options['htmlbeforeauthor'], ENT_QUOTES);
	$htmlafterauthor = htmlspecialchars($options['htmlafterauthor'], ENT_QUOTES);
	$rolerequired = htmlspecialchars($options['rolerequired'], ENT_QUOTES);
	
	?>
	<div class="wrap">
		<h2>XmasB Quotes</h2>
		<form name="quoteform" id="quoteform" class="wrap" method="post" action="">
			<?php wp_nonce_field('update-options') ?>
			<table  class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Show images', 'xmasbquotes'); ?></th>
						<td>
							<input type="radio" name="xmasb_quotes-showimages" class="input" value="yes" <?php echo $showimages == 'yes' ? 'checked="yes"' : '' ?> /> <?php _e('Yes','xmasbquotes'); ?>
							<input type="radio" name="xmasb_quotes-showimages" class="input" value="no" <?php echo $showimages != 'yes' ? 'checked="yes"' : '' ?> /> <?php _e('No','xmasbquotes'); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Default image','xmasbquotes'); ?></th>
						<td>
							<input style="width: 200px;" id="xmasb_quotes-defaultimage" name="xmasb_quotes-defaultimage" type="text" value="<?php echo $defaultimage; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('HTML before image','xmasbquotes'); ?></th>
						<td>
							<input style="width: 200px;" id="xmasb_quotes-htmlbeforeimage" name="xmasb_quotes-htmlbeforeimage" type="text" value="<?php echo $htmlbeforeimage; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('HTML after image','xmasbquotes'); ?></th>
						<td>
							<input style="width: 200px;" id="xmasb_quotes-htmlafterimage" name="xmasb_quotes-htmlafterimage" type="text" value="<?php echo $htmlafterimage; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('HTML before quote','xmasbquotes'); ?></th>
						<td>
							<input style="width: 200px;" id="xmasb_quotes-htmlbeforequote" name="xmasb_quotes-htmlbeforequote" type="text" value="<?php echo $htmlbeforequote; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('HTML after quote','xmasbquotes'); ?></th>
						<td>
							<input style="width: 200px;" id="xmasb_quotes-htmlafterquote" name="xmasb_quotes-htmlafterquote" type="text" value="<?php echo $htmlafterquote; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('HTML before author','xmasbquotes'); ?></th>
						<td>
							<input style="width: 200px;" id="xmasb_quotes-htmlbeforeauthor" name="xmasb_quotes-htmlbeforeauthor" type="text" value="<?php echo $htmlbeforeauthor; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('HTML after author','xmasbquotes'); ?></th>
						<td>
							<input style="width: 200px;" id="xmasb_quotes-htmlafterauthor" name="xmasb_quotes-htmlafterauthor" type="text" value="<?php echo $htmlafterauthor; ?>" />
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Role for editig quotes','xmasbquotes'); ?></th>
						<td>
							<select style="width: 200px;" id="xmasb_quotes-rolerequired" name="xmasb_quotes-rolerequired" type="text" />
							<?php
							//global $wp_roles;
							// get a list of values, containing pairs of: $role_name => $display_name
							//$roles = $wp_roles->get_names();
							//print_r($roles);
							$roles = array(
								"create_users" => 'Administrator',
								"moderate_comments" => 'Editor',
								"edit_published_posts" => 'Author',
								"edit_posts" => 'Contributor'
							);
							foreach($roles as $role => $value){
								if($role == $rolerequired){
									?><option value="<?php echo $role ?>" selected><?php echo $value ?></option><?php
								}else{
									?><option value="<?php echo $role ?>"><?php echo $value ?></option><?php
								}
							}
							?>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row" />
						<td>
							<input class="button bold" type="submit" value="<?php _e('Save','xmasbquotes'); ?>" name="Submit" />
						</td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" id="xmasb_quotes-submit" name="xmasb_quotes-submit" value="1" />
		</form>
	</div>
	<?php
}

function xmasb_quotes_management_page() {
	global $wpdb;
	$data = false;

	$mode = !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
	$quoteID = !empty($_REQUEST['quoteID']) ? $_REQUEST['quoteID'] : '';
	
	if (empty($quoteID)) {
		$quoteID = false;
	}
	
	if ( empty($mode) ) {
		$mode = 'new';
	}
	
	if($mode == 'export'){
		$exportResponse = xmasb_quotes_export_quotes(XMASB_QUOTES_PLUGIN_DIR . 'xml/xmasb-quotes-export.xml');
		if(empty($exportResponse)){
			$exportStatus = "Export successful. You can now download the <a href='" . XMASB_QUOTES_PLUGIN_URL . "xml/xmasb-quotes-export.xml'>exported quotes</a>.";
		}else{
			$exportStatus = "Export failed: $exportResponse";
		}
		$mode = 'new';
	}
	
	if ($mode == 'add') {
		$quote = !empty($_REQUEST['quote_quote']) ? $_REQUEST['quote_quote'] : '';
		$author = !empty($_REQUEST['quote_author']) ? $_REQUEST['quote_author'] : '';
		$imgsrc = !empty($_REQUEST['quote_imgsrc']) ? $_REQUEST['quote_imgsrc'] : '';
		$link = !empty($_REQUEST['quote_link']) ? $_REQUEST['quote_link'] : '';
		$visible = !empty($_REQUEST['quote_visible']) ? $_REQUEST['quote_visible'] : '';

/*		
		if ( ini_get('magic_quotes_gpc') )
		{
			$quote = stripslashes($quote);
			$author = stripslashes($author);
			$imgsrc = stripslashes($imgsrc);
			$link = stripslashes($link);
		}	
*/
		if (empty($quote)) {
				?>
					<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php _e('Could not add quote. No quote entered.', 'xmasbquotes')?></strong></p></div>
				<?php
		} else {
			$sql = "select * from " . XMASB_QUOTES_TABLE . "
				where quote = '" . mysql_real_escape_string($quote) . "'
				and author = '" . mysql_real_escape_string($author) . "'";
			$sqlresponse = $wpdb->query($sql);

			if ( $sqlresponse > 0 )  {
				if (empty($author)) {
					?>
						<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php echo sprintf(__('Could not add quote "%1$s". Quote already exists without author.','xmasbquotes'),$quote) ?></strong></p></div>
					<?php
				} else {
					?>
						<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php echo sprintf(__('Could not add quote "%1$s". Quote already exists for author "%2$s".','xmasbquotes'),$quote, $author) ?></strong></p></div>
					<?php
				}
			} else {
				$sql = "insert into " . XMASB_QUOTES_TABLE . " set "
					. "quote='" . mysql_real_escape_string(trim($quote)) . "', "
					. "author='" . mysql_real_escape_string(trim($author)) . "', "
					. "imgsrc='" . mysql_real_escape_string(trim($imgsrc)) . "', "
					. "link='" . mysql_real_escape_string(trim($link)) . "', "
					. "visible='" . mysql_real_escape_string($visible) . "'";
				$sqlresponse = $wpdb->query($sql);
				if ( $sqlresponse == 1 ) {
					$sql = "select id from " . XMASB_QUOTES_TABLE . " where quote='" . mysql_real_escape_string($quote) . "'"
				     . " and author='" . mysql_real_escape_string($author) . "' and visible='" . mysql_real_escape_string($visible) . "' limit 1";
					$quotes = $wpdb->get_results($sql);
					if ( empty($quotes) || empty($quotes[0]->id) ) {
						?>
								<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php _e('Unknown Error Occured','xmasbquotes'); ?></strong></p></div>
						<?php
					} else {
						?>
								<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php _e('Quote has been added with id','xmasbquotes'); ?> <?php echo $quotes[0]->id?>.</strong></p></div>
						<?php
						$quote = false;
					}
				} elseif ( $sqlresponse > 1 ) {
				?>
						<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php _e('All hell is lose','xmasbquotes'); ?>. <?php echo $sqlresponse?> <?php _e('quotes added!','xmasbquotes'); ?></strong></p></div>
				<?php
				}
			}
		}
	} elseif ( $mode == 'list' ) {
		$sql = 'SELECT COUNT(*) AS count FROM ' . XMASB_QUOTES_TABLE;
		$numberofquotes = $wpdb->get_results($sql);
		$sql = 'SELECT * FROM ' . XMASB_QUOTES_TABLE . ' ORDER BY author';
		$quotes = $wpdb->get_results($sql);
		if ( empty($quotes)) {
			_e('No quotes found', 'xmasbquotes');
			$mode = "new";
		} else {
			?>
			<div class="wrap">
			<h2>XmasB Quotes</h2>
			<div class="xmasb_quotes_block"><ul class="xmasb_quotes_list">
			<legend><?php _e('Number of quotes','xmasbquotes')?>: <?php echo $numberofquotes[0]->count ?></legend>
			<form name="quotelist" id="quotelist" method="post" action="">
				<input type="hidden" name="mode" value="new">
				<input class="button" type="submit" value="<?php _e('Add new','xmasbquotes') ?>" name="Submit"/>
			</form>
			<br />
		<?php
			foreach ($quotes as $quote) {
				echo "<li>";
				xmasb_quotes_print_quote($quote);
				echo "</li>";
			}
			?></ul></div><?php
		}
	} elseif ( $mode == 'edit' ) {
		$quoteID = !empty($_REQUEST['quoteID']) ? $_REQUEST['quoteID'] : '';
	} elseif ( $mode == 'edited' ) {
		$quoteID = !empty($_REQUEST['quoteID']) ? $_REQUEST['quoteID'] : '';
		$quote = !empty($_REQUEST['quote_quote']) ? $_REQUEST['quote_quote'] : '';
		$author = !empty($_REQUEST['quote_author']) ? $_REQUEST['quote_author'] : '';
		$imgsrc = !empty($_REQUEST['quote_imgsrc']) ? $_REQUEST['quote_imgsrc'] : '';
		$link = !empty($_REQUEST['quote_link']) ? $_REQUEST['quote_link'] : '';
		$visible = !empty($_REQUEST['quote_visible']) ? $_REQUEST['quote_visible'] : '';
/*
		$quote = stripslashes($quote);
		$author = stripslashes($author);
		$imgsrc = stripslashes($imgsrc);
		$link = stripslashes($link);
*/
		$sql = "update " . XMASB_QUOTES_TABLE . " set quote='" . mysql_real_escape_string($quote) . "', "
			. "author='" . mysql_real_escape_string($author). "', "
			. "imgsrc='" . mysql_real_escape_string($imgsrc). "', "
			. "link='" . mysql_real_escape_string($link). "', "
			. "visible='" . mysql_real_escape_string($visible) . "'"
		  . " where id='" . mysql_real_escape_string($quoteID) . "'";
		$sqlresponse = $wpdb->query($sql);
		if ( $sqlresponse == 1 ) {
		?>
				<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php _e('Quote has been edited','xmasbquotes'); ?>.</strong></p></div>
		<?php
			$quoteID = false;
		} else {
		?>
			<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php _e('Could not edit quote.','xmasbquotes'); ?></strong></p></div>
		<?php
			$mode = 'edit';
		}
	} elseif ( $mode == __('Delete','xmasbquotes') ) {
		$quoteID = !empty($_REQUEST['quoteID']) ? $_REQUEST['quoteID'] : '';
		if ( empty($quoteID) ) {
			?>
			<div class="error"><p><strong><?php _e('Failure:','xmasbquotes'); ?></strong> <?php _e('No quote ID given.','xmasbquotes'); ?></p></div>
			<?php			
		} else {
			$sql = "delete from " . XMASB_QUOTES_TABLE . " where id='" . mysql_escape_string($quoteID) . "'";
			$sqlresponse = $wpdb->query($sql);

			if ( $sqlresponse == 1 ) {
				?>
				<div class="updated"><p><?php _e('Quote','xmasbquotes'); ?> <?php echo $quoteID;?> <?php _e('deleted successfully','xmasbquotes'); ?></p></div>
				<?php
			} else {
				?>
				<div class="error"><p><strong><?php _e('Failure:','xmasbquotes'); ?></strong> <?php _e('Could not delete quote.','xmasbquotes'); ?></p></div>
				<?php
			}
		}
		$quoteID = false;
	} elseif ( $mode == 'new' ) {
	} else {
		echo "<p>". __('Mode:','xmasbquotes') ." " . $mode . "</p>";
	}

	if ( $quoteID !== false ) {
		if ( intval($quoteID) != $quoteID ) {
		?>
		<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php _e('WTH?!?', 'xmasbquotes'); ?></strong></p></div>
		<?php
			return;
		} else {
			$quote = $wpdb->get_results("select * from " . XMASB_QUOTES_TABLE . " where id='" . mysql_real_escape_string($quoteID) . "' limit 1");
			if ( empty($quote) ) {
			?>
			<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong><?php _e('No quote found for id','xmasbquotes'); ?> <?php echo $quoteID?></strong></p></div>
			<?php
				return;
			}
			$quote = $quote[0];
			$quote->quote = stripslashes($quote->quote);
		}
	}
	if ( $mode != "list" ) {
		?>
		<div class="wrap">
			<h2>XmasB Quotes</h2>
			<fieldset class="options" style="border:none;">
			<?php
				if ($mode=="new" || $mode=="add" || $mode=="edited" || $mode==__('Delete','xmasbquotes')){
			?><legend><?php _e('Add New Quote','xmasbquotes') ?></legend><?php
				}elseif ($mode=="edit"){
			?><legend><?php _e('Edit Quote','xmasbquotes') ?></legend><?php
				}else{
			?><legend>WTF?!? <?php echo $mode ?></legend>
			<?php } ?>
			<form name="quoteform" id="quoteform" class="options" method="post" action="">
				<?php wp_nonce_field('update-options') ?>
				<input type="hidden" name="mode" value="<?php echo $mode=="edit" ? "edited" : "add" ?>">
				<input type="hidden" value="<?php echo $quote->id; ?>" name="quoteID"/>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php _e('Quote','xmasbquotes'); ?></th>
							<td>
								<textarea name="quote_quote" class="input" cols=50 rows=7><?php if ( !empty($quote) ) echo htmlspecialchars($quote->quote); ?></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Author','xmasbquotes'); ?> (<?php _e('Optional','xmasbquotes') ?>)</th>
							<td>
								<input class="input" type="text" name="quote_author" value="<?php if ( !empty($quote) ) echo htmlspecialchars($quote->author); ?>" size="52" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Image','xmasbquotes'); ?> (<?php _e('Optional','xmasbquotes') ?>)</th>
							<td>
								<input class="input" type="text" name="quote_imgsrc" value="<?php if ( !empty($quote) ) echo htmlspecialchars($quote->imgsrc); ?>" size="52" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Link','xmasbquotes'); ?> (<?php _e('Optional','xmasbquotes') ?>)</th>
							<td>
								<input class="input" type="text" name="quote_link" value="<?php if ( !empty($quote) ) echo htmlspecialchars($quote->link); ?>" size="52" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Visible','xmasbquotes'); ?></th>
							<td>
								<input type="radio" name="quote_visible" class="input" value="1" <?php if ( empty($quote->quote) || $quote->visible==1 ) echo "checked" ?> /> <?php _e('Yes','xmasbquotes'); ?>
								<input type="radio" name="quote_visible" class="input" value="0" <?php if ( !empty($quote->quote) && $quote->visible==0 ) echo "checked" ?> /> <?php _e('No','xmasbquotes'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" />
							<td>
								<input class="button bold" type="submit" value="<?php $mode=='edit' ? _e('Edit','xmasbquotes') : _e('Add New Quote','xmasbquotes') ?>" name="Submit" />
									<?php 
										if ( $mode == "edit" )
										{
											?><input class="button" type="submit" onclick="return delete_confirmation_xmasb_quotes()" value="<?php _e('Delete','xmasbquotes') ?>" name="mode"/><?php
										}
									?>
								<input class="button bold" type="reset" value="<?php _e('Reset','xmasbquotes') ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</form>
			</fieldset>
			<fieldset class="options" style="border:none;">
			<legend><?php _e('Statistics','xmasbquotes') ?></legend>
			<?php
			$sql = "SELECT * FROM " . XMASB_QUOTES_TABLE . " order by id desc limit 5";
			$quotes = $wpdb->get_results($sql);
			if ( empty($quotes)) {
				_e('No quotes found', 'xmasbquotes');
			} else {
				?><strong><?php _e('Last 5 quotes added','xmasbquotes') ?></strong>
				<div class="xmasb_quotes_block">
				<ul class="xmasb_quotes_list"><?php
				foreach ($quotes as $quote) {
					echo "<li>";
					xmasb_quotes_print_quote($quote);
					echo "</li>";
				}
				?></ul>
				</div><?php
					
					$sql = "SELECT COUNT(*) AS count FROM " . XMASB_QUOTES_TABLE;
					$numberofquotes = $wpdb->get_results($sql);
				?>
				<form name="quotelist" id="quotelist" method="post" action="">
					<input type="hidden" name="mode" value="list">
					<input class="button" type="submit" value="<?php echo sprintf(__('Show all %1$d quotes','xmasbquotes'),$numberofquotes[0]->count) ?>" name="Submit"/>
				</form>
				<?php
				$export_dir_name = XMASB_QUOTES_PLUGIN_DIR . 'xml/';
					if (is_writable($export_dir_name)) { ?>
				<form name="quoteexport" id="export" method="post" action="">
					<input type="hidden" name="mode" value="export">
					<input class="button" type="submit" value="<?php echo 'Export' ?>" name="Submit"/>
				</form>
				<?php
				if(!empty($exportStatus)) echo $exportStatus;
				?>
				<?php } ?>
				<?php
			}
			?>
			</fieldset>
		<?php
		$sql = 'SELECT quote
			FROM ' . XMASB_QUOTES_TABLE . '
			GROUP by quote
			HAVING COUNT(quote)>1';
		$duplicates = $wpdb->get_results($sql);
		if (!empty($duplicates)) {
			?>
			<fieldset class="options">
			<legend><?php _e('Duplicates','xmasbquotes') ?></legend>
			<div class="xmasb_quotes_block">
			<ul class="xmasb_quotes_list">
			<?php
			foreach ($duplicates as $duplicate) {
				echo "<li>" . $duplicate->quote . "</li>";
				$sql = 'SELECT id, author
					FROM ' . XMASB_QUOTES_TABLE . '
					WHERE quote = "' . $duplicate->quote . '"';
				$authors = $wpdb->get_results($sql);				
				?><table><tbody><?php
				foreach ($authors as $author) {
					echo '<form action="" method="post"><tr>
						<td style="width:200px"><strong>' . $author->author . '</strong></td>
						<input type="hidden" name="mode" value="edit">
						<input type="hidden" value="' . $author->id . '" name="quoteID"/>
						<td><input class="button" type="submit" value="' . __('Edit','xmasbquotes') . '" name="Submit"/><input class="button" type="submit" onclick="return delete_confirmation_xmasb_quotes()" value="' . __('Delete','xmasbquotes') . '" name="mode"/></td>
					</form></li>';
				}
				?></tbody></table><?php
			}
			?>
			</ul>
			</div>
			<?php
		}
		?>
		<?php
	} else {
	?>
		<form name="quotelist" id="quotelist" method="post" action="">
			<input type="hidden" name="mode" value="new">
			<input class="button" type="submit" value="<?php _e('Add New Quote','xmasbquotes') ?>" name="Submit"/>
		</form>

	<?php
	}
	?>
		</div>
	<?php
}

# Include CSS/JS in the HEAD of the html page - admin only
function xmasb_quotes_admin_head_inclusion() {
	wp_enqueue_style( 'xmasbquotesStylesheet' );
	wp_enqueue_script( 'xmasbquotesScript' );
}

# Include CSS/JS in the HEAD of the html page - public only
function xmasb_quotes_public_head_inclusion() {
	wp_enqueue_style( 'xmasbquotesStylesheet' );
}

/*
Widget code
*/

function widget_xmasb_quotes_init() {
	load_plugin_textdomain('xmasbquotes', "/wp-content/plugins/xmasbquotes/");
	
	wp_register_script( 'xmasbquotesScript', XMASB_QUOTES_PLUGIN_URL . 'xmasbquotes.js' );
	wp_register_style( 'xmasbquotesStylesheet', XMASB_QUOTES_PLUGIN_URL . 'xmasbquotes.css' );
	
	if ( !function_exists('register_sidebar_widget') ) {
			return;
	}
	
	function widget_xmasb_quotes($args) {
		extract($args);

		$options = get_option('widget_xmasb_quotes');
		$title = $options['title'];

		echo $before_widget . $before_title . $title . $after_title;

		if ( !function_exists('xmasb_print_random_quote') ) {
		?>
				<div class="wp-xmasb_quotes-error"><?php _('Cannot find the XmasB quotes plugin. If you have installed it check if you have activated it. If you have not installed it, you can download it from', 'xmasbquotes'); ?> <a title="XmasB Quotes" href="http://xmasb.com">xmasb.com</a>.</div>
		<?php
		} else {
			xmasb_print_random_quote();
		}
		echo $after_widget;
	}

	function widget_xmasb_quotes_control() 	{
		$options = get_option('widget_xmasb_quotes');
		if ( !is_array($options) ) {
			$options = array('title'=>'XmasB Quotes', 'showimages'=>'yes', 'defaultimage'=>'quote.png');
		}
		if ( $_POST['xmasb_quotes-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['xmasb_quotes-title']));
			update_option('widget_xmasb_quotes', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);

		?>
		<p style="text-align:right;"><label for="xmasb_quotes-title">Title: <input style="width: 200px;" id="xmasb_quotes-title" name="xmasb_quotes-title" type="text" value="<?php echo $title; ?>" /></label></p>
		<input type="hidden" id="xmasb_quotes-submit" name="xmasb_quotes-submit" value="1" />
		<?php
	}

	//Widget sidebar
	register_sidebar_widget('XmasB Quotes', 'widget_xmasb_quotes');
	
	//Admin
	register_widget_control('XmasB Quotes', 'widget_xmasb_quotes_control', 300, 100);
}

# Add 'XmasB Quotes' page to Wordpress' Manage menu
function xmasb_quotes_add_management_page() {
    if (function_exists('add_management_page')) {
		$options = get_option('widget_xmasb_quotes');
		$rolerequired = htmlspecialchars($options['rolerequired'], ENT_QUOTES);
		if (empty($rolerequired)) $rolerequired = 'administrator';
		add_management_page('XmasB Quotes', 'XmasB Quotes', $rolerequired, basename(__FILE__), 'xmasb_quotes_management_page');
    }
}

# Add 'XmasB Quotes' page to Wordpress' Options menu
function xmasb_quotes_add_opptions_page() {
	if (function_exists('add_options_page')) {
		add_options_page('XmasB Quotes', 'XmasB Quotes', 'administrator', basename(__FILE__), 'xmasb_quotes_options_page');
	}
}

function xmasb_quotes_add_pages() {
	xmasb_quotes_add_management_page();
	xmasb_quotes_add_opptions_page();
}

//EXPORT

//TODO Save files before compress with zip function. If zip is used, remember to change extension accordingly.
//TODO Save quotes with images
//TODO Import

/* Saves quotes as xml */
function xmasb_quotes_export_quotes($filename){
	echo xmasb_quotes_write_to_file($filename,xmasb_quotes_export_get_xml());
}

/* creates a compressed zip file */
/*
function xmasb_quotes_create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,$file);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}
*/

/*
function xmasb_quotes_sample_zip_usage(){
	$files_to_zip = array(
		'preload-images/1.jpg',
		'preload-images/2.jpg',
		'preload-images/5.jpg',
		'kwicks/ringo.gif',
		'rod.jpg',
		'reddit.gif'
	);
	//if true, good; if false, zip creation failed
	$result = create_zip($files_to_zip,'my-archive.zip');
}
*/

//Function will be available in a future release
function xmasb_quotes_export_get_xml() {
	global $wpdb;
	$sql = 'SELECT DISTINCT author, quote, imgsrc, link, visible FROM ' . XMASB_QUOTES_TABLE . ' ORDER BY author, quote';
	$quotes = $wpdb->get_results($sql);

	if ( empty($quotes)) {
		$return =  __('No quotes found', 'xmasbquotes');
		echo $return;
	}
	else {
		$document = new DomDocument("1.0");
		$rootNode = $document->createElement('XmasBQuotes');
		$document->appendChild($rootNode);
		
		//Just in case...
		$authorNode = $document->createElement("Author");
		
		$previousAuthor = "";
		foreach ($quotes as $quote)	{
			if(strcmp($previousAuthor,$quote->author)!=0){
				$authorNode = $document->createElement("Author");
			}
			$quoteNode = $document->createElement("Quote");
			$authorNode->appendChild($quoteNode);

			$rootNode->appendChild($authorNode);

			if(!empty($quote->author)) $authorNode->setAttribute("name", $quote->author);

			if(!empty($quote->imgsrc)) $quoteNode->setAttribute("image", $quote->imgsrc);
			if(empty($quote->visible) || $quote->visible == false) $quoteNode->setAttribute("visible", "false");
			if(!empty($quote->link)) $quoteNode->setAttribute("link", $quote->link);

			$quoteNode->appendChild($document->createTextNode($quote->quote));
			$previousAuthor = $quote->author;
		}
		return $document->saveXML();
	}
}

//Function for writing to a file
function xmasb_quotes_write_to_file($filename, $content){
		if (!$handle = fopen($filename, 'w')) {
			 return "Cannot open file ($filename)";
		}
		// Write $content to our opened file.
		if (fwrite($handle, $content) === FALSE) {
			return "Cannot write to file ($filename)";
		}
		//return "Success, wrote ($content) to file ($filename)";
		fclose($handle);
}
?>
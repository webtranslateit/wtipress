<?php
/*
Plugin Name: WTIpress
Plugin URI: https://webtranslateit.com
Description: Makes a site multilingual, and sync your posts to translate with the WebTranslateIt.com service.
Author: Édouard Brière
Author URI: https://webtranslateit.com
Version: 0.0.3
*/

/*
    This file is part of WTIpress.

    WTIpress is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    ICanLocalize Translator is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with ICanLocalize Translator.  If not, see <http://www.gnu.org/licenses/>.
*/
       

define('WTIPRESS_VERSION', '0.0.3');
define('WTIPRESS_DB_VERSION', '0.0.1.22');
define('WTIPRESS_MIN_WORDPRESS_VERSION', '2.9');
define('WTIPRESS_PLUGIN_PATH', dirname(__FILE__));
define('WTIPRESS_PLUGIN_URL', rtrim(get_option('siteurl'),'/') . '/wp-content/' . basename(dirname(dirname(__FILE__))) . '/' . basename(dirname(__FILE__)) );

require WTIPRESS_PLUGIN_PATH . '/lib/setting.class.php';
require WTIPRESS_PLUGIN_PATH . '/lib/language.class.php';
require WTIPRESS_PLUGIN_PATH . '/lib/network.class.php';
require WTIPRESS_PLUGIN_PATH . '/lib/translation.class.php';
require WTIPRESS_PLUGIN_PATH . '/lib/library/sfYamlDumper.php';
require WTIPRESS_PLUGIN_PATH . '/lib/library/sfYamlParser.php';
require WTIPRESS_PLUGIN_PATH . '/lib/wtipress.class.php';
require WTIPRESS_PLUGIN_PATH . '/inc/compatibility.php';
require WTIPRESS_PLUGIN_PATH . '/inc/functions.php';

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

register_activation_hook(__FILE__, array('Setting', 'install'));
add_action('plugins_loaded', array('Setting', 'update_db_ckeck'));

$wtipress = new WtiPress();

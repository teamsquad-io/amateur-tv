<?php
/**
 * Plugin Name: amateur tv
 * Description: Create your own amateur cam affiliate site, thanks to amateur.tv. Online cams feed and live cams viewer ready to use.
 * Requires at least: 6.0
 * Tested up to: 6.3
 * Requires PHP: 7.2
 * Tested PHP: 8.2
 * Version: 1.2.0
 * Author: amateur.cash
 * License: GPL 2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: amateur-tv
 *
 * @package amateur-tv
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

define( 'AMATEURTV_VERSION', '1.2.0' );
define( 'AMATEURTV_URL', plugin_dir_url( __FILE__ ) );
define( 'AMATEURTV_DIR', __DIR__ );

require_once AMATEURTV_DIR . '/includes/admin.php';
require_once AMATEURTV_DIR . '/includes/camlist.php';
require_once AMATEURTV_DIR . '/includes/iframe.php';

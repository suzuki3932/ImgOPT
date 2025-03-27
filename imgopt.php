<?php
/*
Plugin Name: ImgOPT
Description: Optimize Images, more speed for WordPress site.
Author: haz3mn
Version: 1.0
Requires PHP: 7.4
Requires at least: 5.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: imgopt
Domain Path: /languages
*/

include 'includes/menu_page.php';
include 'includes/imgopt-settings.php';
include 'includes/imgopt-frontend.php';

function imgopt_load_translation() {
load_plugin_textdomain('imgopt', false, dirname(plugin_basename(__FILE__)).'/languages');
}
add_action('init', 'imgopt_load_translation');
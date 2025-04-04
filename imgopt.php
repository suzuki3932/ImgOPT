<?php
/*
Plugin Name: ImgOPT
Description: Optimize Images, more speed for WordPress site.
Author: youmu1948
Version: 1.0.1
Requires PHP: 7.4
Requires at least: 5.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: imgopt
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;

include 'includes/menu_page.php';
include 'includes/imgopt-settings.php';
include 'includes/imgopt-frontend.php';

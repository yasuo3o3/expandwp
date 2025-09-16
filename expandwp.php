<?php
/**
 * Plugin Name: ExpandWP
 * Description: 広いディスプレイでGutenbergの左右ペイン枠を安全に拡張する最小実装。キーボードショートカット・ドラッグリサイズ対応。
 * Version: 0.01
 * Author: yasuo3o3
 * Author URI: https://yasuo-o.xyz/
 * License: GPLv2 or later
 * Text Domain: expandwp
 * Requires at least: 5.9
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('OF_EXPANDWP_VERSION')) {
    return;
}

define('OF_EXPANDWP_VERSION', '0.01');
define('OF_EXPANDWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OF_EXPANDWP_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once OF_EXPANDWP_PLUGIN_DIR . 'includes/class-expandwp-controller.php';
require_once OF_EXPANDWP_PLUGIN_DIR . 'includes/class-expandwp-runtime.php';
require_once OF_EXPANDWP_PLUGIN_DIR . 'includes/class-expandwp-admin.php';

class Of_ExpandWP {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'of_init'));
    }

    public function of_init() {
        Of_ExpandWP_Controller::get_instance();
        Of_ExpandWP_Runtime::get_instance();
        Of_ExpandWP_Admin::get_instance();

        add_action('admin_enqueue_scripts', array($this, 'of_admin_enqueue'));
        add_action('enqueue_block_editor_assets', array($this, 'of_block_editor_enqueue'));
        add_action('customize_controls_enqueue_scripts', array($this, 'of_customize_enqueue'));
    }

    public function of_admin_enqueue($hook) {
        if (!Of_ExpandWP_Controller::of_is_target_screen($hook)) {
            return;
        }

        wp_enqueue_script(
            'expandwp-admin',
            OF_EXPANDWP_PLUGIN_URL . 'assets/js/expandwp.js',
            array('jquery'),
            OF_EXPANDWP_VERSION,
            true
        );

        wp_enqueue_style(
            'expandwp-admin',
            OF_EXPANDWP_PLUGIN_URL . 'assets/css/expandwp.css',
            array(),
            OF_EXPANDWP_VERSION
        );

        Of_ExpandWP_Runtime::of_localize_settings();
    }

    public function of_block_editor_enqueue() {
        wp_enqueue_script(
            'expandwp-editor',
            OF_EXPANDWP_PLUGIN_URL . 'assets/js/expandwp.js',
            array('wp-blocks', 'wp-element'),
            OF_EXPANDWP_VERSION,
            true
        );

        wp_enqueue_style(
            'expandwp-editor',
            OF_EXPANDWP_PLUGIN_URL . 'assets/css/expandwp.css',
            array(),
            OF_EXPANDWP_VERSION
        );

        Of_ExpandWP_Runtime::of_localize_settings();
    }

    public function of_customize_enqueue() {
        wp_enqueue_script(
            'expandwp-customize',
            OF_EXPANDWP_PLUGIN_URL . 'assets/js/expandwp.js',
            array('jquery', 'customize-controls'),
            OF_EXPANDWP_VERSION,
            true
        );

        wp_enqueue_style(
            'expandwp-customize',
            OF_EXPANDWP_PLUGIN_URL . 'assets/css/expandwp.css',
            array(),
            OF_EXPANDWP_VERSION
        );

        Of_ExpandWP_Runtime::of_localize_settings();
    }
}

Of_ExpandWP::get_instance();
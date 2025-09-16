<?php

if (!defined('ABSPATH')) {
    exit;
}

class Of_ExpandWP_Runtime {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // ランタイム初期化処理
    }

    public static function of_get_default_settings() {
        return array(
            'minViewport' => 1440,
            'minCanvas' => 1000,
            'gutters' => 48,
            'defaultWidth' => 480,
            'modes' => array(
                'left' => 'manual',
                'right' => 'manual'
            ),
            'selectors' => array(
                'left' => array(
                    '.editor-list-view-sidebar',
                    '.edit-post-editor__list-view-panel'
                ),
                'right' => array(
                    '.interface-complementary-area__fill'
                )
            ),
            'shortcuts' => array(
                'leftToggle' => 'Alt+[',
                'rightToggle' => 'Alt+]',
                'reset' => 'Alt+0'
            )
        );
    }

    public static function of_get_settings() {
        $defaults = self::of_get_default_settings();
        $saved_options = get_option('expandwp_options', array());

        // 保存された設定をデフォルトとマージ
        $settings = wp_parse_args($saved_options, $defaults);

        return $settings;
    }

    public static function of_localize_settings() {
        $settings = self::of_get_settings();
        $editor_type = Of_ExpandWP_Controller::of_get_editor_type();

        $localize_data = array(
            'settings' => $settings,
            'editorType' => $editor_type,
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('expandwp_nonce'),
            'isRTL' => is_rtl(),
            'i18n' => array(
                'leftPanelExpanded' => esc_html__('左パネルを拡張しました', 'expandwp'),
                'rightPanelExpanded' => esc_html__('右パネルを拡張しました', 'expandwp'),
                'panelsReset' => esc_html__('パネル幅をリセットしました', 'expandwp'),
                'canvasProtected' => esc_html__('キャンバス幅保護のため幅を調整しました', 'expandwp')
            )
        );

        wp_localize_script('expandwp-admin', 'expandwpData', $localize_data);
        wp_localize_script('expandwp-editor', 'expandwpData', $localize_data);
        wp_localize_script('expandwp-customize', 'expandwpData', $localize_data);
    }

    public static function of_save_panel_width($panel, $width, $editor_type = '') {
        if (empty($editor_type)) {
            $editor_type = Of_ExpandWP_Controller::of_get_editor_type();
        }

        $storage_key = "expandwp_width_{$editor_type}_{$panel}";
        $width = (int) $width;

        if ($width <= 0) {
            delete_option($storage_key);
        } else {
            update_option($storage_key, $width);
        }

        return true;
    }

    public static function of_get_panel_width($panel, $editor_type = '') {
        if (empty($editor_type)) {
            $editor_type = Of_ExpandWP_Controller::of_get_editor_type();
        }

        $storage_key = "expandwp_width_{$editor_type}_{$panel}";
        $default_width = self::of_get_default_settings()['defaultWidth'];

        return get_option($storage_key, $default_width);
    }

    public static function of_calculate_safe_widths($left_target, $right_target, $viewport_width) {
        $settings = self::of_get_settings();
        $min_canvas = $settings['minCanvas'];
        $gutters = $settings['gutters'];

        $available_width = $viewport_width - $gutters;
        $total_panels = $left_target + $right_target;
        $remaining_canvas = $available_width - $total_panels;

        if ($remaining_canvas >= $min_canvas) {
            return array($left_target, $right_target);
        }

        $excess = $total_panels - ($available_width - $min_canvas);

        // 左右の比率で削減量を配分
        if ($total_panels > 0) {
            $left_ratio = $left_target / $total_panels;
            $right_ratio = $right_target / $total_panels;

            $left_reduction = $excess * $left_ratio;
            $right_reduction = $excess * $right_ratio;

            $safe_left = max(0, $left_target - $left_reduction);
            $safe_right = max(0, $right_target - $right_reduction);

            return array((int) $safe_left, (int) $safe_right);
        }

        return array(0, 0);
    }
}
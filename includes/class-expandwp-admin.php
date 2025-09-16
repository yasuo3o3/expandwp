<?php

if (!defined('ABSPATH')) {
    exit;
}

class Of_ExpandWP_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'of_add_admin_menu'));
        add_action('admin_init', array($this, 'of_admin_init'));
    }

    public function of_add_admin_menu() {
        add_options_page(
            esc_html__('ExpandWP設定', 'expandwp'),
            esc_html__('ExpandWP', 'expandwp'),
            'manage_options',
            'expandwp',
            array($this, 'of_admin_page')
        );
    }

    public function of_admin_init() {
        register_setting('expandwp_options_group', 'expandwp_options', array(
            'sanitize_callback' => array($this, 'of_sanitize_options')
        ));

        add_settings_section(
            'expandwp_general_section',
            esc_html__('基本設定', 'expandwp'),
            array($this, 'of_general_section_callback'),
            'expandwp'
        );

        add_settings_field(
            'minViewport',
            esc_html__('最小ビューポート幅', 'expandwp'),
            array($this, 'of_min_viewport_callback'),
            'expandwp',
            'expandwp_general_section'
        );

        add_settings_field(
            'left_mode',
            esc_html__('左パネルモード', 'expandwp'),
            array($this, 'of_left_mode_callback'),
            'expandwp',
            'expandwp_general_section'
        );

        add_settings_field(
            'right_mode',
            esc_html__('右パネルモード', 'expandwp'),
            array($this, 'of_right_mode_callback'),
            'expandwp',
            'expandwp_general_section'
        );
    }

    public function of_general_section_callback() {
        echo '<p>' . esc_html__('ExpandWPの動作を設定します。', 'expandwp') . '</p>';
    }

    public function of_min_viewport_callback() {
        $options = get_option('expandwp_options', array());
        $defaults = Of_ExpandWP_Runtime::of_get_default_settings();
        $value = isset($options['minViewport']) ? $options['minViewport'] : $defaults['minViewport'];

        printf(
            '<input type="number" id="minViewport" name="expandwp_options[minViewport]" value="%d" min="1200" max="3000" step="10" />',
            (int) $value
        );
        echo '<p class="description">' . esc_html__('自動・常時モードが動作する最小ビューポート幅（px）。この値未満では手動モードのみ動作します。', 'expandwp') . '</p>';
    }

    public function of_left_mode_callback() {
        $options = get_option('expandwp_options', array());
        $defaults = Of_ExpandWP_Runtime::of_get_default_settings();
        $value = isset($options['modes']['left']) ? $options['modes']['left'] : $defaults['modes']['left'];

        $modes = array(
            'manual' => esc_html__('手動（トグル操作時のみ拡張）', 'expandwp'),
            'auto' => esc_html__('自動（対象UIが開いたら拡張）', 'expandwp'),
            'always' => esc_html__('常時（対象画面ロード時に即拡張）', 'expandwp')
        );

        echo '<select id="left_mode" name="expandwp_options[modes][left]">';
        foreach ($modes as $mode => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($mode),
                selected($value, $mode, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    public function of_right_mode_callback() {
        $options = get_option('expandwp_options', array());
        $defaults = Of_ExpandWP_Runtime::of_get_default_settings();
        $value = isset($options['modes']['right']) ? $options['modes']['right'] : $defaults['modes']['right'];

        $modes = array(
            'manual' => esc_html__('手動（トグル操作時のみ拡張）', 'expandwp'),
            'auto' => esc_html__('自動（対象UIが開いたら拡張）', 'expandwp'),
            'always' => esc_html__('常時（対象画面ロード時に即拡張）', 'expandwp')
        );

        echo '<select id="right_mode" name="expandwp_options[modes][right]">';
        foreach ($modes as $mode => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($mode),
                selected($value, $mode, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    public function of_sanitize_options($input) {
        $output = array();

        if (isset($input['minViewport'])) {
            $output['minViewport'] = (int) $input['minViewport'];
            if ($output['minViewport'] < 1200) {
                $output['minViewport'] = 1200;
            }
            if ($output['minViewport'] > 3000) {
                $output['minViewport'] = 3000;
            }
        }

        $valid_modes = array('manual', 'auto', 'always');

        if (isset($input['modes']['left']) && in_array($input['modes']['left'], $valid_modes, true)) {
            $output['modes']['left'] = sanitize_text_field($input['modes']['left']);
        }

        if (isset($input['modes']['right']) && in_array($input['modes']['right'], $valid_modes, true)) {
            $output['modes']['right'] = sanitize_text_field($input['modes']['right']);
        }

        return $output;
    }

    public function of_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('このページにアクセスする権限がありません。', 'expandwp'));
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('ExpandWP設定', 'expandwp'); ?></h1>

            <div class="expandwp-admin-notice" style="background: #fff; border-left: 4px solid #72aee6; margin: 20px 0; padding: 12px;">
                <h3><?php echo esc_html__('キーボードショートカット', 'expandwp'); ?></h3>
                <ul style="margin-left: 20px;">
                    <li><strong>Alt + [</strong> : <?php echo esc_html__('左パネルのトグル', 'expandwp'); ?></li>
                    <li><strong>Alt + ]</strong> : <?php echo esc_html__('右パネルのトグル', 'expandwp'); ?></li>
                    <li><strong>Alt + 0</strong> : <?php echo esc_html__('幅のリセット', 'expandwp'); ?></li>
                </ul>
                <p><em><?php echo esc_html__('※入力中（input/textarea/contenteditable）のときは無効化されます。', 'expandwp'); ?></em></p>
            </div>

            <form method="post" action="options.php">
                <?php
                settings_fields('expandwp_options_group');
                do_settings_sections('expandwp');
                submit_button();
                ?>
            </form>

            <div class="expandwp-admin-info" style="background: #fff; border: 1px solid #ccd0d4; margin-top: 20px; padding: 15px;">
                <h3><?php echo esc_html__('制約事項', 'expandwp'); ?></h3>
                <ul style="margin-left: 20px;">
                    <li><?php echo esc_html__('編集キャンバスの最小幅1000pxを維持します。', 'expandwp'); ?></li>
                    <li><?php echo esc_html__('最小ビューポート幅未満では自動・常時モードは動作しません。', 'expandwp'); ?></li>
                    <li><?php echo esc_html__('左右同時拡張時は自動的に幅を調整してキャンバス幅を保護します。', 'expandwp'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
}
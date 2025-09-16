<?php

if (!defined('ABSPATH')) {
    exit;
}

class Of_ExpandWP_Controller {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // コントローラー初期化処理
    }

    public static function of_is_target_screen($hook = '') {
        global $pagenow;

        // ブロックエディタ（投稿・固定ページ）
        if (in_array($pagenow, array('post.php', 'post-new.php'), true)) {
            return true;
        }

        // サイトエディタ
        if ($pagenow === 'site-editor.php' || $pagenow === 'themes.php') {
            return true;
        }

        // カスタマイザー
        if ($pagenow === 'customize.php') {
            return true;
        }

        // フック名での判定
        if (!empty($hook)) {
            $target_hooks = array(
                'post.php',
                'post-new.php',
                'site-editor.php',
                'themes.php',
                'customize.php',
                'appearance_page_gutenberg-edit-site'
            );

            foreach ($target_hooks as $target_hook) {
                if (strpos($hook, $target_hook) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function of_get_editor_type() {
        global $pagenow;

        if (in_array($pagenow, array('post.php', 'post-new.php'), true)) {
            return 'post';
        }

        if ($pagenow === 'site-editor.php' ||
            $pagenow === 'themes.php' ||
            strpos($_SERVER['REQUEST_URI'], 'site-editor.php') !== false) {
            return 'site';
        }

        if ($pagenow === 'customize.php') {
            return 'customize';
        }

        return 'unknown';
    }

    public static function of_is_block_editor() {
        global $pagenow;

        // Gutenbergプラグインが有効かつブロックエディタ画面
        if (function_exists('is_gutenberg_page') && is_gutenberg_page()) {
            return true;
        }

        // WordPress 5.0+ のブロックエディタ
        if (function_exists('use_block_editor_for_post_type')) {
            if (in_array($pagenow, array('post.php', 'post-new.php'), true)) {
                $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'post';
                if (isset($_GET['post'])) {
                    $post = get_post((int) $_GET['post']);
                    if ($post) {
                        $post_type = $post->post_type;
                    }
                }
                return use_block_editor_for_post_type($post_type);
            }
        }

        // サイトエディタは常にブロックエディタ
        if ($pagenow === 'site-editor.php') {
            return true;
        }

        return false;
    }

    public static function of_should_load_assets() {
        // 管理画面でない場合は読み込まない
        if (!is_admin()) {
            return false;
        }

        // カスタマイザーは例外的に管理画面外でも読み込む
        global $pagenow;
        if ($pagenow === 'customize.php') {
            return true;
        }

        // 対象画面かどうか
        return self::of_is_target_screen();
    }
}
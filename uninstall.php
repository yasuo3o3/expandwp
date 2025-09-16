<?php
/**
 * ExpandWP Uninstall
 *
 * プラグインアンインストール時に実行されるファイル
 * 設定データや一時的なオプションを削除する
 */

// 不正アクセスを防ぐ
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// プラグインが削除されたときのみ実行される
if (!defined('ABSPATH')) {
    exit;
}

/**
 * プラグイン設定オプションを削除
 */
delete_option('expandwp_options');

/**
 * パネル幅の保存オプションを削除
 * エディタタイプ別、パネル別の保存データを削除
 */
$editor_types = array('block', 'classic', 'customize');
$panels = array('left', 'right');

foreach ($editor_types as $editor_type) {
    foreach ($panels as $panel) {
        $storage_key = "expandwp_width_{$editor_type}_{$panel}";
        delete_option($storage_key);
    }
}

/**
 * 追加で作成される可能性のある一時的なオプションを削除
 * プラグインで動的に作成されるオプションパターンをクリーンアップ
 */
global $wpdb;

// expandwp_ で始まるオプションをすべて削除
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        'expandwp_%'
    )
);

/**
 * キャッシュをクリア
 */
wp_cache_flush();
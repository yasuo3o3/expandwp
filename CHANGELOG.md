# Changelog

All notable changes to ExpandWP will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- キーボードショートカットを変更
  - `Alt + ,`: 左パネル（List View）のトグル（旧: `Alt + [`）
  - `Alt + .`: 右パネル（設定パネル）のトグル（旧: `Alt + ]`）
  - `Alt + 0`: 両パネル幅リセット（変更なし）

### Added
- プラグインアンインストール処理（uninstall.php）
  - 設定オプション（expandwp_options）の完全削除
  - パネル幅保存データ（expandwp_width_*）の完全削除
  - プラグイン関連全オプションのクリーンアップ

## [0.01] - 2025-01-17

### Added
- 初回リリース
- Gutenberg左右ペインの枠拡張機能
  - 左パネル（List View）と右パネル（設定パネル）を最大480pxまで拡張
  - デフォルト幅480px、ドラッグで200px-800px範囲で調整可能
- キーボードショートカット機能
  - `Alt + [`: 左パネルトグル
  - `Alt + ]`: 右パネルトグル
  - `Alt + 0`: 両パネル幅リセット
  - 入力中（input/textarea/contenteditable）の自動無効化
- ドラッグリサイズ機能
  - パネル外縁に縦ハンドル追加
  - リアルタイム幅変更
  - localStorage保存・復元（エディタ種別×ペイン単位）
- 3つの動作モード
  - 手動モード（デフォルト）：ショートカット操作時のみ拡張
  - 自動モード：対象パネル表示時に自動拡張
  - 常時モード：画面読み込み時に即拡張
- 安全制約機能
  - 編集キャンバス最小幅1000px保護
  - 左右同時拡張時の自動幅調整
  - 1440px未満ビューポートでの自動・常時モード抑制
- 対象画面サポート
  - ブロックエディタ（投稿・固定ページ編集）
  - サイトエディタ（フルサイト編集）
  - カスタマイザー（追加CSS編集時）
- RTL環境対応
- 管理画面設定ページ（設定→ExpandWP）
  - 左右パネルの動作モード設定
  - 最小ビューポート幅設定（1200-3000px）
- MutationObserver監視による自動モード実装
- WordPress.org Plugin Directory規約準拠
- バニラJavaScript実装（外部ライブラリ依存なし）

### Technical Details
- WordPress 5.9以降対応
- PHP 7.4以降対応
- プレフィックス `of_` 使用（YASUO3O3.md準拠）
- セキュリティ：入力sanitize、出力escape、nonce+権限チェック
- i18n対応：すべて翻訳関数使用、Text Domain `expandwp`
- 静的チェック：php -l構文確認済み

### Files Added
```
expandwp/
├── expandwp.php                        // メインプラグインファイル
├── uninstall.php                      // アンインストール処理
├── readme.txt                         // WordPress.org用README
├── CHANGELOG.md                       // このファイル
├── includes/
│   ├── class-expandwp-controller.php  // 画面種別検出・フック登録
│   ├── class-expandwp-runtime.php     // 設定管理・JavaScript連携
│   └── class-expandwp-admin.php       // 管理画面設定ページ
└── assets/
    ├── css/
    │   └── expandwp.css               // スタイルシート（ハンドル・制御）
    └── js/
        └── expandwp.js                // メイン機能実装
```

## Security

### [0.01] - 2025-01-17
- WordPress セキュリティ標準準拠
- 出力エスケープ（esc_html、esc_attr、esc_url）
- 入力サニタイゼーション
- nonce検証 + 権限チェック
- XSS対策実装

## Notes

- プラグインスラッグ：`expandwp`
- Text Domain：`expandwp`
- 作者：yasuo3o3
- ライセンス：GPLv2 or later
- 最小WordPress：5.9
- 最小PHP：7.4

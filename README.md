# ExpandWP

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.9%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

広いディスプレイ環境でWordPressのGutenbergエディタの左右ペインの幅を安全に拡張する軽量プラグインです。

## 概要

ExpandWPは、編集作業を効率化するために、Gutenbergエディタの左右のパネル（List ViewとSettings Panel）を適切な幅まで拡張できるWordPressプラグインです。キーボードショートカット、ドラッグリサイズ、自動拡張モードを備え、編集キャンバスの安全性を保ちながら作業領域を最適化します。

## 主な機能

### 🎯 ペイン拡張機能
- **左パネル拡張**: List Viewを最大480pxまで拡張
- **右パネル拡張**: 設定パネル・ブロック設定を最大480pxまで拡張
- **ドラッグリサイズ**: 200px-800px範囲での自由な幅調整

### ⌨️ キーボードショートカット
| ショートカット | 機能 |
|---------------|------|
| `Alt + ,` | 左パネル（List View）のトグル |
| `Alt + .` | 右パネル（設定パネル）のトグル |
| `Alt + 0` | 両パネルの幅をリセット |

*入力中（input/textarea/contenteditable）は自動的に無効化されます*

### 🔧 動作モード
- **手動モード**（デフォルト）: ショートカットでのみ拡張
- **自動モード**: 対象パネルが表示されたときに自動拡張
- **常時モード**: 画面読み込み時に即座に拡張

### 🛡️ 安全制約
- **編集キャンバス保護**: 最小幅1000pxを維持
- **ビューポート制限**: 1440px未満では自動・常時モードを抑制
- **衝突回避**: 左右同時拡張時の自動幅調整

### 🌐 対応環境
- **エディタ**: ブロックエディタ、サイトエディタ、カスタマイザー
- **言語**: RTL（右から左）レイアウト対応
- **技術**: バニラJavaScript（外部ライブラリ不要）

## インストール

### WordPress管理画面から
1. 「プラグイン」→「新規追加」
2. "ExpandWP" で検索
3. インストール・有効化

### 手動インストール
1. [リリースページ](../../releases)から最新版をダウンロード
2. `/wp-content/plugins/expandwp/` にアップロード
3. 管理画面で有効化

## 使い方

### 基本操作
1. プラグインを有効化
2. Gutenbergエディタを開く
3. `Alt + ,` または `Alt + .` でパネルを拡張
4. パネル端のハンドルをドラッグして幅を調整

### 設定画面
「設定」→「ExpandWP」で以下を設定できます：

- **左パネルモード**: 手動/自動/常時
- **右パネルモード**: 手動/自動/常時
- **最小ビューポート幅**: 1200-3000px（自動・常時モード発動条件）

### 動作確認

#### 1920px幅での動作例
```bash
# 手動トグルで左右480pxに拡張
Alt + , → 左パネル拡張
Alt + . → 右パネル拡張
Alt + 0 → リセット

# ドラッグで変更→再読込で復元
パネル端のハンドルをドラッグ → localStorage保存 → 次回復元
```

#### 1280px幅での動作例
```bash
# 自動・常時モードは抑制、手動は動作
Alt + , → 動作（手動）
自動モード → 抑制される
```

## 技術仕様

### システム要件
- **WordPress**: 5.9以降
- **PHP**: 7.4以降
- **ブラウザ**: モダンブラウザ（ES6対応）

### アーキテクチャ
```
expandwp/
├── expandwp.php                        # メインプラグインファイル
├── uninstall.php                      # アンインストール処理
├── readme.txt                         # WordPress.org用README
├── README.md                          # このファイル
├── CHANGELOG.md                       # 変更履歴
├── includes/
│   ├── class-expandwp-controller.php  # 画面検出・フック管理
│   ├── class-expandwp-runtime.php     # 設定・JavaScript連携
│   └── class-expandwp-admin.php       # 管理画面
└── assets/
    ├── css/expandwp.css               # スタイルシート
    └── js/expandwp.js                 # メイン機能
```

### セキュリティ
- **出力エスケープ**: `esc_html()`, `esc_attr()`, `esc_url()`
- **入力サニタイゼーション**: `sanitize_text_field()`
- **権限チェック**: `current_user_can()` + nonce検証
- **XSS対策**: 全出力でエスケープ処理

## FAQ

### Q: キーボードショートカットが効かない
**A:** 入力フィールドにフォーカスがある場合は無効化されます。また、ブラウザの拡張機能との競合可能性もあります。

### Q: パネル幅が勝手に縮小される
**A:** 編集キャンバスの最小幅1000px保護のための仕様です。ビューポートを広げるか、片方のパネルを閉じてください。

### Q: 小さい画面でも使える？
**A:** 1440px未満では自動・常時モードは抑制されますが、手動トグルは動作します。

### Q: 他のプラグインとの競合は？
**A:** プレフィックス`of_`で名前空間を分離しており、競合リスクを最小化しています。

### Q: カスタマイザーで動作しない
**A:** 「追加CSS」セクション選択時のみ動作します。他のセクションでは対象外です。

### Q: プラグインを削除したらデータは消える？
**A:** はい。プラグインアンインストール時に設定データとパネル幅データは完全に削除されます。

## 開発・貢献

### 開発環境
```bash
# リポジトリクローン
git clone https://github.com/yasuo3o3/expandwp.git
cd expandwp

# PHP構文チェック
php -l expandwp.php
php -l includes/*.php

# WordPress Coding Standards
phpcs --standard=WordPress .
```

### バグ報告・機能要求
[GitHub Issues](../../issues) でお知らせください。

### プルリクエスト
1. フォーク
2. フィーチャーブランチ作成
3. 変更・テスト
4. プルリクエスト送信

## ライセンス

GNU General Public License v2.0 or later
https://www.gnu.org/licenses/gpl-2.0.html

## 作者

**yasuo3o3**
- ウェブサイト: https://yasuo-o.xyz/
- WordPress.org: [@yasuo3o3](https://profiles.wordpress.org/yasuo3o3/)

## 変更履歴

詳細は [CHANGELOG.md](CHANGELOG.md) をご覧ください。

### [0.01] - 2025-01-17
- 初回リリース
- 基本的なペイン拡張機能
- キーボードショートカット対応
- ドラッグリサイズ機能
- 3つの動作モード実装
- WordPress.org規約準拠

---

**Note**: このプラグインは内部UI要素を変更せず、パネルの枠のみを拡張する安全な実装です。
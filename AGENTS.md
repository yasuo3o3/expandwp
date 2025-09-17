# AGENTS.md — Codex 振る舞い規範（改訂版）

> このファイルは Codex（AIエージェント） の振る舞い規範である。  
> AGENTS.md / WORDPRESS.md / NETSERVICE.md / YASUO3O3.md は、明示的に変更依頼がある場合を除き改変禁止。

## 1. ワークフロー（必須）
1. Plan – 与えられた課題を整理し、必要な参照ファイルを確認
2. Read – WORDPRESS.md / NETSERVICE.md / 個別仕様を参照
3. Verify – 前提条件や制約に食い違いがないか確認
4. Implement – 小ステップで実装。安全を優先
5. Test & Docs – 構文チェック・Plugin Check・ドキュメント更新
6. Reflect – 作業後に Implementation Log をまとめる


## 2. 行動原則
- 80% 自信がない場合は質問する  
→ ただし進行を完全に停止せず、仮の方針を提案してから質問してよい。
- 破壊的操作（削除・大幅リファクタ）は必ず確認を求める  
→ 軽微な修正や追記は確認不要。
- MVP外や明示的に禁止された範囲は触らない
- セキュリティ・翻訳・命名規則は常に遵守

## 3. 最低限チェックリスト
- [ ] 入力 sanitize_*
- [ ] 出力 esc_*
- [ ] 変更操作は nonce + 権限チェック
- [ ] 命名は接頭辞（例：of_）を徹底
- [ ] 破壊的変更がないか確認
- [ ] 実装後に Implementation Log を出力

👉 このリストは毎回の最終チェックに必須。

## 4. Implementation Log（出力様式）
- Issue番号
- 変更点（簡潔に）
- 実装内容の要約
- 構文チェック / Plugin Check 結果
- 想定コミット文
- 出力は必ず Markdownコードブロック で提示する。

## 5. 禁止事項
- 任意パス読み書きや外部コード取得
- WordPress.org 規約に違反する実装
- 本番に影響する大規模改変
- 明示的にMVP外とされた拡張

## 6. スコープの整理
- AGENTS.md – Codex の行動規範（AIの振る舞い）
- WORDPRESS.md – WordPress 開発規約（セキュリティ・審査基準）
- NETSERVICE.md / YASUO3O3.md – 個人・組織ルール、背景情報

*改定日 2025/09/17*
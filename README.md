# 概要
- GitHubのIssueを、CSVファイルから一括作成する
    - 主にredmineからの移行を想定している

# 使用方法
- CSVファイルを作成する
    - #
    - トラッカー
    - 優先度
    - 題名
    - 備考
    - 説明
- `create_issue.php`の`$convMilestoneList`や各種設定の変数を変更する。
    - 必要に応じて`$convTrackerList`も変更する。
- `create_issue.php`を実行する

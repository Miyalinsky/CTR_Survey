# ①課題番号-プロダクト名

11-UMIN-CTR検索システム

## ②課題内容（どんな作品か）

- 臨床試験情報（UMIN-CTR）の情報収集を支援するシステム
- UMINが公開するCTR情報を基に、独自のデータベースを構築
- キーワードと期間を指定して検索すると条件に該当するレコードの一部を表示する
- 検索結果をxlsx形式で保存可能
- 検索期間を入力して[All Export]を押すと必要な情報が全て一括でダウンロード可能
- AM3:00にデータベースが最新情報に自動更新される

## ③DEMO

https://ochiponchi.sakura.ne.jp/CTR_Survey/frontend/

## ④作ったアプリケーション用のIDまたはPasswordがある場合

管理者（データベースの手動更新権限あり）
- ID: admin
- PW: password

一般ユーザー
- ID: user
- PW: password

## ⑤工夫した点・こだわった点

- 臨床試験情報の収集業務を現状手動で行なっているため、自動化したいと思い作成した
- 公開されているCTRのスナップショットCSVはサイズが大きく、検索にも難があるためデータベースやSQLを活用してサーバー再度で保持することで情報へのアクセス性を高めた
- cronを設定し、日次でデータベースを自動更新するようにした
- 管理者権限で手動でもデータベースを更新可能
- All Export機能により、必要な全ての情報を一括でExcelにエクスポートし、業務稼働削減を実現

## ⑥難しかった点・次回トライしたいこと(又は機能)

- 見た目の部分を綺麗に整えていないので、CSSをしっかり作りたい

## ⑦質問・疑問・感想、シェアしたいこと等なんでも

- [質問]
- [感想]
- [参考記事]
  - 1. [URLをここに記入]
  - 2. [URLをここに記入]

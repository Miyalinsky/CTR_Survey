# ①課題番号-プロダクト名

7-UMIN-CTR検索システム

## ②課題内容（どんな作品か）

- 臨床試験情報（UMIN-CTR）の情報収集を支援するシステム
- UMINが公開するCTR情報を基に、独自のデータベースを構築
- キーワードと期間を指定して検索すると条件に該当するレコードの一部を表示する
- 検索結果をxlsx形式で保存可能
- データベースの更新もボタンひとつで可能（結構時間かかる）

## ③DEMO

https://ochiponchi.sakura.ne.jp/CTR_Survey/frontend/

## ④作ったアプリケーション用のIDまたはPasswordがある場合

- ID: なし
- PW: なし

## ⑤工夫した点・こだわった点

- 臨床試験情報の収集業務を現状手動で行なっているため、自動化したいと思い作成した
- 公開されているCTRのスナップショットCSVはサイズが大きく、検索にも難があるためデータベースやSQLを活用してサーバー再度で保持することで情報へのアクセス性を高めた

## ⑥難しかった点・次回トライしたいこと(又は機能)

- 現状データベースの更新は、フロントから手動で更新ボタンを押す仕組みになっているので、サーバー側で定期処理できるようにしたい
- 今回はテストでフロントに返すデータを少なくしているので、実用できるように整えたい

## ⑦質問・疑問・感想、シェアしたいこと等なんでも

- [質問]
- [感想]
- [参考記事]
  - 1. [URLをここに記入]
  - 2. [URLをここに記入]

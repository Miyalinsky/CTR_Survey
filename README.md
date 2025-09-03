# UMIN臨床試験情報検索支援ツール

## 導入方法
### 1. （未インストールであれば）VScodeインストール
以下のURLにアクセスし、使用するOSに合わせてインストーラーをダウンロード  
https://code.visualstudio.com/download

インストーラーを開き、手順を進める

### 2. （未インストールであれば）Gitインストール
以下のURLにアクセスし、使用するOSに合わせてインストーラーをダウンロード  
https://git-scm.com/downloads



### 1. XAMPPインストール
以下のURLにアクセスし、使用するOSに合わせてインストーラーをダウンロード  
https://www.apachefriends.org/jp/index.html

コンポーネントの選択画面では、MySQL, PHP, phpMyAdminの3つにチェックが入っていればOK
<img width="749" height="637" alt="スクリーンショット 2025-09-01 213826" src="https://github.com/user-attachments/assets/49c7519f-e3e9-4f8c-ae95-197650b7c064" />



### データベース作成
CREATE OR REPLACE TABLE users (
    id int(11) NOT NULL AUTO_INCREMENT,
    username varchar(255) NOT NULL UNIQUE,
    password varchar(255) NOT NULL,
    is_admin tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

INSERT INTO users (username, password, is_admin)
VALUES ('admin', '$2y$10$6k6tNGzdWMGWQfwsnE1ebesm883eSjGhGJy8jjwpkKzEMm5jF.9fS', 1);


CREATE OR REPLACE TABLE trials(
    id int(11) NOT NULL AUTO_INCREMENT,
    umin_id varchar(1000) UNIQUE,
    public_title text,
    scientific_title text,
    `condition` text,
    narrative_objectives1 text,
    basic_objectives2 text,
    basic_objectives_others text,
    developmental_phase text,
    primary_outcomes text,
    key_secondary_outcomes text,
    study_type text,
    basic_design text,
    randomization text,
    randomization_unit text,
    blinding text,
    control text,
    age_lower_limit text,
    age_upper_limit text,
    gender text,
    key_inclusion_criteria text,
    key_exclusion_criteria text,
    target_sample_size int(11),
    institute text,
    institute_org text,
    organization text,
    organization_org text,
    irb_organization text,
    institutions text,
    date_of_disclosure date,
    url_japanese varchar(255),
    PRIMARY KEY (id)
);

### 
コマンドプロンプトを起動し、インストールしたXAMPP配下のhtdocsディレクトリに移動
cd C:\xampp\htdocs

git clone https://github.com/Miyalinsky/CTR_Survey.git




「npm : このシステムではスクリプトの実行が無効になっているため、ファイル C:\Program Files\nodejs\npm.ps1 を読み込むことが できません。」というエラーが出る場合
Windows PowerShellを管理者として実行し、以下のコマンドを実行
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser



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

# UMIN臨床試験情報検索支援ツール

## 導入方法
Windowsでの導入方法を記載しているが、他OSでも可  
いくつかインストールが必要なソフトがあるので、開発用PCでの実行を推奨

### 1. （未インストールであれば）Gitインストール
以下の記事を参考にGitをインストールしておく  
https://qiita.com/takeru-hirai/items/4fbe6593d42f9a844b1c


### 2. XAMPPインストール
以下のURLにアクセスし、使用するOSに合わせてインストーラーをダウンロード  
https://www.apachefriends.org/jp/index.html

コンポーネントの選択画面では、MySQL, PHP, phpMyAdminの3つにチェックが入っていればOK
<img width="749" height="637" alt="スクリーンショット 2025-09-01 213826" src="https://github.com/user-attachments/assets/49c7519f-e3e9-4f8c-ae95-197650b7c064" />

その他はそのままNextを押していけばOK

インストールが完了したら、以下のようなウィンドウが開く
<img width="995" height="643" alt="スクリーンショット 2025-09-01 215430" src="https://github.com/user-attachments/assets/4a646898-9ee0-4f97-a988-35b8e523c4f5" />
以降、XAMPPを起動する際は、保存先（デフォルト：C:\xampp）のxampp-control.exeを実行する


### 3.データベース作成
[start]をクリックしてApacheとMySQLを立ち上げておく
<img width="994" height="646" alt="スクリーンショット 2025-09-03 113022" src="https://github.com/user-attachments/assets/630c99c2-edeb-4e38-bb37-8ab4643b0764" />

ブラウザを開き、URLに「localhost」を入力すると以下の画面になる
<img width="2244" height="1427" alt="スクリーンショット 2025-09-01 220025" src="https://github.com/user-attachments/assets/4d6a593e-1780-413f-ab81-b1514eecc480" />

上部の[phpMyAdmin]をクリックすると以下の画面になる
<img width="2252" height="1427" alt="スクリーンショット 2025-09-01 220526" src="https://github.com/user-attachments/assets/0fca7f61-3043-4935-b897-7ad4137ffb9d" />

左側一覧の[新規作成]をクリックし、データベース名「ctr_survey」、照合順序「utf8mb4_general_ci」で作成を押す
<img width="2252" height="1427" alt="スクリーンショット 2025-09-01 220928" src="https://github.com/user-attachments/assets/e085bd80-f3b7-45a0-855a-0c4f15eef618" />

一覧に新規作成された[ctr_survey]をクリックし、上側の[SQL]を押すと、SQLクエリの入力欄が開くので、以下のクエリを実行する
```
CREATE OR REPLACE TABLE users (
    id int(11) NOT NULL AUTO_INCREMENT,
    username varchar(255) NOT NULL UNIQUE,
    password varchar(255) NOT NULL,
    is_admin tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);
```
<img width="2254" height="1428" alt="スクリーンショット 2025-09-03 115952" src="https://github.com/user-attachments/assets/39499870-653f-41d7-aee3-003dfa85a131" />

再度、[ctr_survey]→[SQL]を押し、以下のクエリを実行する
```
INSERT INTO users (username, password, is_admin)
VALUES ('admin', '$2y$10$6k6tNGzdWMGWQfwsnE1ebesm883eSjGhGJy8jjwpkKzEMm5jF.9fS', 1);
```
再度、[ctr_survey]→[SQL]を押し、以下のクエリを実行する
```
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
```

### 4. Node.jsインストール
以下のURLからインストーラーをダウンロード  
https://nodejs.org/ja/download

以下のインストーラーをダウンロードするとよい
<img width="877" height="222" alt="スクリーンショット 2025-09-03 123205" src="https://github.com/user-attachments/assets/5d80ca21-6508-4064-90a9-f3cdb7055f64" />

インストーラーを実行、基本的にそのまま[Next]を押して進めればよい

コマンドプロンプトを開き以下2つのコマンドを実行する
```
node -v
```
```
npm -v
```
それぞれのバージョンが表示されればOK
<img width="915" height="417" alt="スクリーンショット 2025-09-03 153617" src="https://github.com/user-attachments/assets/a5d94e3f-fe8e-4ada-bd06-41d814172cd8" />

「npm : このシステムではスクリプトの実行が無効になっているため、ファイル C:\Program Files\nodejs\npm.ps1 を読み込むことができません。」というエラーが出る場合は、Windows PowerShellを管理者として実行し、以下のコマンドを実行
```
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### 5.ソースコード準備
コマンドプロンプトを起動し、インストールしたXAMPP配下のhtdocsディレクトリに移動
```
cd C:\xampp\htdocs
```
以下のコマンドを実行
```
git clone https://github.com/Miyalinsky/CTR_Survey.git
```
<img width="1160" height="554" alt="スクリーンショット 2025-09-02 103315" src="https://github.com/user-attachments/assets/fdc9a1a3-2d0f-425c-bc0c-e4c97f13d680" />

htdocsにCTR_surveyというフォルダが作成されるので、CTR_survey\frontendディレクトリに移動する
```
cd C:\xampp\htdocs\CTR_survey\frontend
```
以下のコマンドを実行する
```
npm install
```
<img width="855" height="328" alt="スクリーンショット 2025-09-03 155804" src="https://github.com/user-attachments/assets/29a0f9cc-eb01-4638-873d-260e7e25785e" />

## 利用方法
XAMPPを起動し、ApacheとMySQLをStartさせる

コマンドプロンプトを開き、CTR_survey\frontendディレクトリに移動する
```
cd C:\xampp\htdocs\CTR_survey\frontend
```
以下のコマンドを実行する
```
npm start
```

ブラウザが起動し、以下の画面が開く
<img width="862" height="657" alt="スクリーンショット 2025-09-03 105619" src="https://github.com/user-attachments/assets/9a08351f-60cc-4f9a-926d-1776535b1102" />

ユーザー名「admin」、パスワード「password」を入力し、ログインすると以下のような画面になる
<img width="1548" height="509" alt="スクリーンショット 2025-09-03 110729" src="https://github.com/user-attachments/assets/648108a3-039f-446d-b044-852cda92870e" />

[データベースを更新]を押し、臨床試験情報のデータベースが最新の状態に更新する
※環境によって時間がかかる可能性あり

更新が完了すると以下の表示が出る
<img width="666" height="219" alt="スクリーンショット 2025-09-03 110417" src="https://github.com/user-attachments/assets/632749aa-2035-4def-aecb-0f05bd417201" />

日付を入力して検索期間を指定し、[All Export]を押す
<img width="1240" height="556" alt="スクリーンショット 2025-09-03 110451" src="https://github.com/user-attachments/assets/d1a4ee42-22e1-4ebe-bf46-5ceeaf32d329" />

しばらく待つと、Excelファイルが出力される
※検索期間が長い場合、破損したファイルが出力される可能性があるため、その場合は期間を短くして試す
<img width="2255" height="1427" alt="スクリーンショット 2025-09-03 164131" src="https://github.com/user-attachments/assets/177fd326-f950-4de3-b523-edc1eeea2492" />

利用後はコマンドプロンプトで「Ctrl+C」を押し、プログラムを停止する  
その後、XAMPPでApacheとMySQLをStopする


# アプリの概要・作成背景
### アプリの概要

### 作成背景
本の管理を容易にし、買うときの無駄を改善できるアプリが欲しいと思った。自分は本が好きで、本をたくさん持っている。その大体の本はブックカバーが付いており、本の場所や巻数を即座に把握出来ないことや、メモの書き忘れなどで、同じ本を買ってきてしまうことがよくあり、大学の図書館にあるパソコンの検索システムを思い出して、本とその保管場所が一目でわかるアプリを作ろうと思ったので「MyLibrary」を作成しようと思った。
# 使用言語・OS
### 使用言語
- Laravel9 PHP 
- database ElephantSQL
### OS
- windows11
# 機能一覧
- ユーザー登録機能
- ログイン機能
- 検索機能
- お気に入り機能
- 本データ一覧表示機能
- シリーズ・カテゴリー・本棚データによる本データの分類
- ブログ機能
- 本情報登録・編集機能
- 本棚情報編集機能
- ブログ投稿・編集機能
### 注力した・頑張った機能
## ユーザーごとのデータ制限
- 「where」を使い、ユーザーIDによって検索をかけることで自分が登録した本情報を呼び出せるようにした。
- 「AUTHORIZED」を使うことにより、他のユーザーが登録した本情報は見ることが出来ないようにした。
## 保存・編集機能
- シリーズ・カテゴリー名をデータを記入する欄で入力欄にキーワードが無かった場合は選択欄で選んだシリーズ・カテゴリー名が選ばれる。キーワードを記入し、そのキーワードが選択欄にあった場合には選択欄にある同じ名前のデータが選ばれた場合と同じ扱いになるようにした。
## シリーズ・カテゴリーのテーブルデータ
- ユーザーテーブルとシリーズ・カテゴリーテーブルをユーザーIDとそれぞれのIDを中間テーブルとしてリレーションを行い、ユーザーが同じシリーズ・カテゴリーIDを持つカテゴリー名を使えるようにした。
## 本のデータによる一覧表示
- 本を表示するページをシリーズ・カテゴリー・本棚のデータ名を選択することでそのデータ名を持つ本を表示する一覧ページを作成した。
## 並び替え機能
- 本を作成日時、タイトルで昇順降順を掛けられるようにした。
## ブログ機能
- 他ユーザーがどのような本を読んでいるのかを見て、ユーザーが次にどのような本を買うのかを決める手助けができる機能としてブログ機能を作成した。
# URL・テストアカウント
### URL
- [MyLibrary](https://mylibrary-bx6fw2903-yota-yamamotos-projects.vercel.app/)
### テストアカウント
1. e-mail: test@user1.com
   pas: 12345678
2. e-mail: test@user2.com
   pas: 12345678
#### ※ユーザーごとの制限を見てもらうために２つアカウントを用意している。   

# PHP-BELTCONVEYOR-MAIL

imapやpop3で受信したメールをLINEやチャットへかんたんに通知できるライブラリです。  

## 使い方

設定ファイル(JSON形式)に、メールサーバのアカウント情報や送信するチャット・LINEのアカウント情報等を設定してください。  
その後、サーバへこのライブラリをアップロードしcronを設定して下さい。 

### 設定ファイル

example:  

```
{
    "receive": {
        "type": "[メールプロトコル]", //require
        "server": "[メールサーバドメイン]", //require
        "user": "[メールサーバのユーザ名]", //require
        "password": "[メールサーバのパスワード", //require
        "interval": "[実行間隔]",//require
        "filter": [ //チャット等へ送信するメールをフィルタ
            {
                "name": "受信メールの名称（送信用で利用）",//require
                "mailbox": [
                    "[メールボックスの指定]"//require
                ],
                "from": "", //option
                "to": "", //option
                "body": "", //option
                "subject": "", //option
            },
            {
                "name": "受信メールの名称（送信用で利用）",//require
                "mailbox": [
                    "[メールボックスの指定]"//require
                ],
                "from": "", //option
                "to": "", //option
                "body": "", //option
                "subject": "", //option
            }
        ],
        "interval": 30
    },
    "transmit": [
        {
            "name": "送信用の名称（受信メールの名称とセット）",
            "type": "chatwork",
            "room_id": ""
        },
        {
            "name": "送信用の名称（受信メールの名称とセット）",
            "type": "line",
            "room_id": ""
        }
    ]
}
```

### 受信用(receive)

|必須|名称|内容|メモ|
|---|---|---|---|
|○|type|メールプロトコル（imap or pop3）|
|○|server|メールサーバのドメイン|
|○|user|メールサーバのユーザ|
|○|password|メールサーバのパスワード|
|○|interval|実行間隔|
|○|target->name|受信メールのフィルタ名（送信用と対になる）|
|○|target->mailbox|受信メールボックス|
|-|target->from|fromで絞込|
|-|target->to|toで絞込|
|-|target->subject|subjectで絞込|
|-|target->body|bodyで絞込|


# PHP-BELTCONVEYOR-MAIL

imapやpop3で受信したメールをLINEやチャットへかんたんに通知できるライブラリです。  

## 使い方

設定ファイル(config.json)に、メールサーバのアカウント情報や送信するチャット・LINEのアカウント情報等を設定してください。  
その後、サーバへこのライブラリをアップロードしcronを設定して下さい。 

### 設定ファイル

example:  

config.json  
```
{
    "protocol": "[メールプロトコル]", //require
    "server": "[メールサーバドメイン]", //require
    "user": "[メールサーバのユーザ名]", //require
    "password": "[メールサーバのパスワード", //require
    "interval": "[実行間隔]",//require
    "target": [ //チャット等へ送信する対象を指定
        {
            "mailbox": [
                "[メールボックスの指定]"//require
            ],
            "mailfilter": {
                "from": "", //option
                "to": "", //option
                "body": "", //option
                "subject": "", //option
                "bcc": "", //option
                "cc": "" //option
            },
            "transmit":{
                "service": "chatwork",//require
                "type": "message",
                "room_id": ""
            }
        },
        {
            "mailbox": [
                "[メールボックスの指定]"//require
            ],
            "mailfilter": {
                "from": "", //option
                "to": "", //option
                "body": "", //option
                "subject": "", //option
                "bcc": "", //option
                "cc": "" //option
            },
            "transmit":{
                "type": "chatwork",
                "room_id": ""
            }
        }
    ],
    "interval": 30
}
```

### 受信用(receive)

|必須|名称|内容|メモ|
|---|---|---|---|
|○|protocol|メールプロトコル（imap or pop3）|
|○|server|メールサーバのドメイン|
|○|user|メールサーバのユーザ|
|○|password|メールサーバのパスワード|
|○|interval|実行間隔|cronの設定と合わせる|
|○|target->name|受信メールのフィルタ名（送信用と対になる）|
|○|target->mailbox|受信メールボックス|
|-|target->from|fromで絞込|
|-|target->to|toで絞込|
|-|target->subject|subjectで絞込|
|-|target->body|bodyで絞込|
|-|target->cc|ccで絞込|
|-|target->bcc|bccで絞込|
|○|transmit->service|送信するサービス（LINEやチャット等）|
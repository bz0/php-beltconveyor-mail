<?php
    namespace PhpBeltconveyorMail;

    class Validation
    {
       /**
         * 設定ファイルのチェック
         *
         * @param  mixed $config
         * @return mixed
         */
        private function config(array $config): mixed
        {
            if (($config['protocol']==='imap' ||
                 $config['protocol']==='pop3')){
                throw new \Exception('メール受信用のプロトコルの指定が間違っています。「imap」または「pop3」を指定して下さい');
            }

            if(!$config['server']){
                throw new \Exception('メール受信用のサーバが指定されていません');
            }

            if(!$config['user']){
                throw new \Exception('メール受信用のユーザが指定されていません');
            }

            if(!$config['password']){
                throw new \Exception('メール受信用のパスワードが指定されていません');
            }

            if(!$config['interval']){
                throw new \Exception('メール受信用のインターバル時間（分）が指定されていません');
            }

            if($config['target'])===0){
                throw new \Exception('メール受信するターゲットが指定されていません');
            }

            foreach($config['target'] as $target)
            {
                if(!$target['name'])
                {
                    throw new \Exception('受信メールの名称が指定されていません');
                }

                if(!$target['mailbox'])
                {
                    throw new \Exception('受信メールのメールボックスが指定されていません（name:' . $name . '）');
                }

                if(!$target['transmit']['service']){
                    throw new \Exception('送信するサービスが指定されていません');
                }
            }
            
            return true;
        }
    }
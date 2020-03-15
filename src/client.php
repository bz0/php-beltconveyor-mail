<?php
    namespace PhpBeltconveyorMail;
    use PhpBeltconveyorMail\Receiver;
    use PhpBeltconveyorMail\Transmitter;

    class Client
    {
        private $receive;
        private $transmit;

        public function __construct(receiveInterface $receive,
                                    transmitInterface $transmit
        )
        {
            $this->receive = $receive;
            $this->$transmit = $transmit;
        }

        //設定ファイル読み込み
        public function config()
        {
            $json = file_get_contents(__DIR__ . '/../config.json');
            if ($json === false) {
                throw new \Exception('ファイルが存在しません');
            }

            $config = json_decode($json,true);
            if (!$config) {
                throw new \Exception('JSONをPHP配列に変換できませんでした');
            }

            return $config;
        }
        
        /**
         * 受信用設定チェック
         *
         * @param  mixed $config
         * @return void
         */
        private function receiveValidation($config)
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
            }
            
            return true;
        }
        
        /**
         * 送信用設定チェック
         *
         * @param  mixed $config
         * @return void
         */
        private function transmitValidation($config)
        {
            foreach($config as $c)
            {
                if($config['service']==='chatwork' ||
                   $config['service']==='line')
                {
                    throw new \Exception('送信するサービスが指定されていません');
                }
            }
        }

        //メール受信
        private function receive($config)
        {

            foreach($this->receive as $receive)
            {

            }

            switch($configReceive['type']){
                case 'imap':
                    $imap = new Imap();
                    $mailList = $imap->exec();
                    break;
                case 'pop3':
                    $pop3 = new Pop3();
                    $mailList = $pop3->exec();
                    break;
            }

            return $mailList;
        }

        //配信
        public function transmit()
        {
            
        }

        public function exec($config)
        {
            try{
                $this->receiveValidation();
                $date = date("d-M-Y");
                $startUnixtime = strtotime("-{$config['receive']['interval']} minute");

                $mailList = $this->receive($config['receive']);
            }catch(\Exception $e){
                error_log('[PHP-BELTCONVEYOR-MAIL]' . $e->getMessage());
            }
        }
    }

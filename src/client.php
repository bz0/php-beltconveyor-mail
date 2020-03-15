<?php
    namespace PhpBeltconveyorMail;
    use PhpBeltconveyorMail\Receiver;
    use PhpBeltconveyorMail\Transmitter;

    class Client
    {
        private $receive;
        public function __construct(receiveInterface $receive)
        {
            $this->receive = $receive;
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

        private function configCheck($config)
        {
            //受信用設定チェック
            if (($config['receive']['protocol']==='imap' ||
                 $config['receive']['protocol']==='pop3')){
                throw new \Exception('メール受信用のプロトコルの指定が間違っています。「imap」または「pop3」を指定して下さい');
            }

            if(!$config['receive']['server']){
                throw new \Exception('メール受信用のサーバが指定されていません');
            }

            if(!$config['receive']['user']){
                throw new \Exception('メール受信用のユーザが指定されていません');
            }

            if(!$config['receive']['password']){
                throw new \Exception('メール受信用のパスワードが指定されていません');
            }

            if(!$config['receive']['interval']){
                throw new \Exception('メール受信用のインターバル時間（分）が指定されていません');
            }

            if($config['receive']['target'])===0){
                throw new \Exception('メール受信用のターゲットが指定されていません');
            }

            //送信用設定チェック
            
            
            return true;
        }

        private function 

        //メール受信
        public function receive($configReceive)
        {

            
            $date = date("d-M-Y");
            $startUnixtime = strtotime("-{$config['receive']['interval']} minute");

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

        public function exec()
        {

        }
    }

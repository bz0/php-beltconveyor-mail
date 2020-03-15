<?php
    namespace PhpBeltconveyorMail;

    class Client
    {
        private $container;

        public function __construct($container)
        {
            $this->container = $container;
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

        //メール受信
        private function receive(array $config, string $date, string $startUnixtime)
        {
            $this->container['config'] = $config;
            switch($config['protocol']){
                case 'imap':
                    $receive = $this->container['imap'];
                    break;
                case 'pop3':
                    //pop3のクラスが作成できたら追加
                    break;
            }

            $receive->validation();
            $mailList = $receive->exec();

            return $mailList;
        }

        //配信
        public function transmit()
        {
            
        }

        public function exec()
        {
            try{
                $config = $this->config(); //設定ファイルの取得
                //メールフィルタ用の日時の取得
                $startUnixtime = strtotime("-{$config['interval']} minute");
                $sinceDate = date("d-M-Y", $startUnixtime);

                var_dump($startUnixtime);
                var_dump($sinceDate);

                //メール受信
                $mailList = $this->receive($config, $sinceDate, $startUnixtime);
            }catch(\Exception $e){
                echo $e->getMessage();
                error_log('[PHP-BELTCONVEYOR-MAIL]' . $e->getMessage());
            }

            return $mailList;
        }
    }

    require_once __DIR__ . "/../vendor/autoload.php";
    require_once __DIR__ . "/receive/receiveInterface.php";
    require_once __DIR__ . "/receive/imap.php";


    $container = new \Pimple\Container();

    $container['imap'] = function ($c) {
        return new \PhpBeltconveyorMail\Receive\Imap($c['config']);
    };

    $client = new Client($container);
    $client->exec();

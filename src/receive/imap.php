<?php
    namespace PhpBeltconveyorMail\Receive;

    class Imap implements receiveInterface
    {
        private $config;
        private $port = 993;
        private $max  = 1000;
        
        /**
         * @param  mixed $config
         * @return void
         */
        public function __construct(array $config)
        {
            $this->config = $config;
        }

        public function validation(): mixed
        {
            if (($this->config['protocol']==='imap' ||
                 $this->config['protocol']==='pop3')){
                throw new \Exception('メール受信用のプロトコルの指定が間違っています。「imap」または「pop3」を指定して下さい');
            }

            if(!$this->config['server']){
                throw new \Exception('メール受信用のサーバが指定されていません');
            }

            if(!$this->config['user']){
                throw new \Exception('メール受信用のユーザが指定されていません');
            }

            if(!$this->config['password']){
                throw new \Exception('メール受信用のパスワードが指定されていません');
            }

            if(!$this->config['interval']){
                throw new \Exception('メール受信用のインターバル時間（分）が指定されていません');
            }

            if($this->config['target']===0){
                throw new \Exception('メール受信するターゲットが指定されていません');
            }

            foreach($this->config['target'] as $target)
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
         * imap connect
         *
         * @param  mixed $port
         * @return mixed $imap
         */
        private function connect()
        {
            $port = $this->port;
            if(isset($this->config['port']))
            {
                $port = $this->config['port'];
            }

            $imap = eden('mail')->imap(
                $this->config['server'], 
                $this->config['username'], 
                $this->config['password'], 
                $port,
                true
            );

            return $imap;
        }

        private function getMailBox($imap): array
        {
            $mailboxes = [];
            if(is_array($this->config['mailbox']))
            {
                //複数の受信メールボックスが指定されたとき
                foreach($this->config['mailbox'] as $mailbox)
                {
                    $result = $imap->setActiveMailbox($mailbox);
                    if (!$result)
                    {
                        throw new Exception('指定したメールボックス(' . $mailbox . ')が存在しません');
                    }
                    $mailboxes[] = $result;
                }

                return $mailboxes;
            }

            $mailboxes[] = $imap->setActiveMailbox($this->config['mailbox']);
            return $mailboxes;
        }
        
        /**
         * 取得開始時刻以降のメールを抽出
         *
         * @param  mixed $mailList
         * @param  mixed $start
         * @return array
         */
        private function startExtract(array $mailList, int $start): array
        {
            $mailData = [];
            foreach($mailList as $mail)
            {
                if($mail['date']>=$start)
                {
                    $mailData[] = $mail;
                }
            }

            return $mailData;
        }

        private function query($date, array $filters)
        {
            $params[] = 'SINCE "' . $date . '"';
            foreach($filters as $key => $val)
            {
                switch(strtolower($key)){
                    case 'from':
                        $query = 'FROM "' . $val . '"';
                        break;
                    case 'to':
                        $query = 'TO "' . $val . '"';
                        break;
                    case 'subject':
                        $query = 'SUBJECT "' . $val . '"';
                        break;
                    case 'body':
                        $query = 'BODY "' . $val . '"';
                        break;
                    case 'cc':
                        $query = 'CC "' . $val . '"';
                    case 'bcc':
                        $query = 'BCC "' . $val . '"';
                }

                $params[] = $query;
            }
            
            return $params;
        }
                
        /**
         * exec
         *
         * @param  mixed $date
         * @param  mixed $startUnixtime
         * @param  mixed $targets
         * @return array
         */
        public function exec(string $date, int $startUnixtime, array $targets): array
        {
            $imap = $this->connect();

            foreach($targets as $target)
            {
                $mailboxes = $this->getMailBox($imap);
                $querys = $this->query($date, $target['mailfilter']);

                $mailList = [];
                foreach($mailboxes as $mailbox)
                {
                    $result = $mailbox->search($querys, 0, $this->max);
                    $mailList[$mailbox['name']] = array_merge($mailList[$mailbox['name']], $result);
                }
    
                $mailList = $this->startExtract($startUnixtime, $mailList);
            }

            $imap->disconnect();

            return $mailList;
        }
    }
<?php
    namespace PhpBeltconveyorMail\Receiver;

    class Imap
    {
        private $config;
        private $port = 993;
        private $max  = 1000;
        
        /**
         * @param  mixed $config
         * @return void
         */
        public function __construct(array $config): void
        {
            if(isset($config['max']))
            {
                //取得メール数の最大値
                $this->max = $config['max'];
            }

            if($config['server']  ==='' ||
               $config['username']==='' ||
               $config['password']==='' ||
               empty($config['mailbox'])
            )
            {
                return false;
            }

            $this->config = $config;
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

        private getMailBox($imap): array
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

        private function filter(array $mailList, $start)
        {
            $result = [];
            foreach($mailList as $mail)
            {
                if()
                {
                    $result[] = $mail['date'];
                }
            }
        }
        
        /**
         *
         * @return void
         */
        public function exec(string $date, string $startUnixtime)
        {
            if($date==='' || $startUnixtime==='')
            {
                throw new Exception("");
            }

            if(!is_numeric($interval))
            {
                return false; 
            }

            $startTime = date("Y-m-d", strtotime("-1 minute"));

            $imap = $this->connect();
            $mailboxes = $this->getMailBox($imap);

            $mailList = [];
            foreach($mailboxes as $mailbox)
            {
                $result = $mailbox->search(['SINCE "' . $date . '"'], 0, $this->max);
                $mailList = array_merge($mailList, $result);
            }

            $mailList = $this->between($mailList, $start);
            $imap->disconnect();

            return $mailList;
        }
    }
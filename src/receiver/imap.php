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
        
        /**
         * uids
         *
         * @param  mixed $mailList
         * @param  mixed $start
         * @return void
         */
        private function uids(array $mailList, $start)
        {
            $uids = [];
            foreach($mailList as $mail)
            {
                if($mail['date']>=$start)
                {
                    $uids[] = $mail['uid'];
                }
            }

            return $uids;
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
         *
         * @return void
         */
        public function exec(string $date, string $startUnixtime, $targets)
        {
            $imap = $this->connect();

            foreach($targets as $target)
            {
                $mailboxes = $this->getMailBox($imap);

                $mailList = [];
                foreach($mailboxes as $mailbox)
                {
                    $result = $mailbox->search($querys, 0, $this->max);
                    $mailList[$mailbox['name']] = array_merge($mailList[$mailbox['name']], $result);
                }
    
                $mailList = $this->between($mailList, $start);
            }

            $imap->disconnect();

            return $mailList;
        }
    }
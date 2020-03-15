<?php
    namespace namespace PhpBeltconveyorMail\Receiver;
    interface receiveInterface
    {
        private function connect();
        public function exec(string $date, string $startUnixtime);
        private function filter();
    }
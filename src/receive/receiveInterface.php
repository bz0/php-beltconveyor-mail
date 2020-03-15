<?php
    namespace PhpBeltconveyorMail\Receive;

    interface receiveInterface
    {
        public function exec(string $date, int $startUnixtime, array $targets);
    }
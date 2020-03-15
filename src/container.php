<?php
    $container = new \Pimple\Container\Container();

    $c->extend('imap', function($config, $c) {
        return new \PhpBeltconveyorMail\Receive\Imap($config);
    });
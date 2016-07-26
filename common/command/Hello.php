<?php
namespace command;

class Hello
{
    public $a;
    protected $b;
    public static function index($params = [])
    {
        \Jeen::echoln($params);
        \Jeen::echoln(__FILE__.':'.__LINE__);
    }

}
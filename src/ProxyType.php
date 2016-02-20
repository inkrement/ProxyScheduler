<?php

namespace Inkrement\ProxyScheduler;

abstract class ProxyType
{
    const Undefinied = 0;
    const HTTP = 1;
    const HTTPS = 2;
    const SOCKS4 = 3;
    const SOCKS5 = 4;

    public static function intFactory($int)
    {
        assert(is_integer($int), 'ProxyType intFactory expects an integer argument');

        switch ($int) {
          case 1:
            return Self::HTTP;
          case 2:
            return Self::HTTPS;
          case 3:
            return Self::SOCKS;
          default:
            return Self::Undefinied;
      }
    }

    public static function stringFactory($string)
    {
        assert(is_string($string), 'ProxyType stringFactory expects an string');

        switch ($string) {
          case 'HTTP':
          case 'Http':
          case 'http':
            return Self::HTTP;
          case 'HTTPS':
          case 'Https':
          case 'https':
            return Self::HTTPS;
          case 'SOCKS':
          case 'Socks':
          case 'socks':
            return Self::SOCKS;
          default:
            return Self::Undefinied;
      }
    }

    public static function toString($value)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());

        return strtolower($constants[$value]).'://';
    }
}

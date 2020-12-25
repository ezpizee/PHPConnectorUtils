<?php

namespace Ezpizee\ConnectorUtils;

use Ezpizee\MicroservicesClient\Client as MicroserviceClient;
use Unirest\Request;

class Client extends MicroserviceClient
{
  public static function adminUri(string $platform='ezpz', string $version='latest'): string {return '/adminui/'.$version.'/index.'.$platform.'.html';}

  public static function installUri(string $platform='ezpz'): string {return '/install/html/index.'.$platform.'.html';}

  public static function apiSchema(string $env): string {
    switch ($env) {
      case 'local':
        return 'https://';
      case 'dev':
        return 'https://';
      case 'stage':
        return 'https://';
      case 'prod':
      default:
        return 'https://';
    }
  }

  public static function apiHostPfx(string $env): string {
    switch ($env) {
      case 'local':
        return $env.'-api.ezpizee.com';
      case 'dev':
        return $env.'-api.ezpz.solutions';
      case 'stage':
        return $env.'-api.ezpizee.com';
      case 'prod':
      default:
        return 'api.ezpizee.com';
    }
  }

  public static function apiEndpointPfx(string $env): string {return self::apiSchema($env).self::apiHostPfx($env);}

  public static function cdnSchema(string $env): string {
    switch ($env) {
      case 'local':
        return 'https://';
      case 'dev':
        return 'https://';
      case 'stage':
        return 'https://';
      case 'prod':
      default:
        return 'https://';
    }
  }

  public static function cdnHost(string $env): string {
    switch ($env) {
      case 'local':
        return $env.'-cdn.ezpz.solutions';
      case 'dev':
        return $env.'-cdn.ezpz.solutions';
      case 'stage':
        return $env.'-cdn.ezpz.solutions';
      case 'prod':
      default:
        return 'cdn.ezpz.solutions';
    }
  }

  public static function cdnEndpointPfx(string $env): string {return self::cdnSchema($env).self::cdnHost($env);}
}

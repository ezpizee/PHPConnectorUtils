<?php

namespace Ezpizee\ConnectorUtils;

use Ezpizee\MicroservicesClient\Client as MicroserviceClient;
use Ezpizee\MicroservicesClient\Config;
use Unirest\Request;

class Client extends MicroserviceClient
{
  public static function getTokenUri(): string {return '/api/user/token';}

  public static function adminUri(string $platform='ezpz', string $version='latest'): string {return '/adminui/'.$version.'/index.'.$platform.'.html';}

  public static function installUri(string $platform='ezpz'): string {return '/install/html/index.'.$platform.'.html';}

  public static function apiSchema(string $env): string {return 'https://';}

  public static function apiHost(string $env): string {return $env==='dev'?$env.'-api.ezpz.solutions':($env==='prod'?'':$env.'-').'api.ezpizee.com';}

  public static function apiEndpointPfx(string $env): string {return self::apiSchema($env).self::apiHost($env);}

  public static function cdnSchema(string $env): string {return 'https://';}

  public static function cdnHost(string $env): string {return ($env==='prod'?'':$env.'-').'cdn.ezpz.solutions';}

  public static function cdnEndpointPfx(string $env): string {return self::cdnSchema($env).self::cdnHost($env);}
}

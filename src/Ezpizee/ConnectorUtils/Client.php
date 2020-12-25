<?php

namespace Ezpizee\ConnectorUtils;

use Ezpizee\MicroservicesClient\Client as MicroserviceClient;
use Ezpizee\MicroservicesClient\Config;
use Unirest\Request;

class Client extends MicroserviceClient
{
  const HEADER_PARAM_ACCEPT = "Accept";
  const HEADER_PARAM_CTYPE = "Content-Type";
  const HEADER_PARAM_ACCESS_TOKEN = "Authorization";
  const HEADER_PARAM_USER_AGENT = "User-Agent";
  const HEADER_VALUE_JSON = "application/json";
  const HEADER_VALUE_USER_AGENT = "Ezpizee Web/1.0";
  const HEADER_PARAM_APP_NAME = "App-Name";
  const HEADER_PARAM_APP_VERSION = "App-Version";
  const HEADER_VALUE_APP_VERSION = "0.0.1";
  const HEADER_PARAM_APP_PLATFORM = "App-Platform";
  const HEADER_VALUE_APP_PLATFORM = "Unknown";
  const HEADER_PARAM_OS_PLATFORM_VERSION = "OS-Platform-Version";
  const HEADER_VALUE_OS_PLATFORM_VERSION = "Unknown";
  const HEADER_LANGUAGE_TAG = "Language-Tag";

  private $isMultipart = false;
  private $platform = '';
  private $platformVersion = '';
  
  public function __construct(string $schema, string $host, Config $config)
  {
    parent::__construct($schema, $host, $config);
    $this->setDefaultHeaders();
  }

  public function setMultipart(bool $b): void {$this->isMultipart = $b;}

  public function verifyPeer(bool $b): void {Request::verifyPeer($b);}

  public function setPlatform(string $platform): void {$this->platform=$platform;}

  public function setPlatformVersion(string $platformVersion): void {$this->platformVersion=$platformVersion;}

  private function setDefaultHeaders(): void {
    if (!$this->isMultipart && !$this->hasHeader(self::HEADER_PARAM_CTYPE)) {
      $this->addHeader(self::HEADER_PARAM_CTYPE, self::HEADER_VALUE_JSON);
    }
    if (!$this->hasHeader(self::HEADER_PARAM_ACCEPT)) {
      $this->addHeader(self::HEADER_PARAM_ACCEPT, self::HEADER_VALUE_JSON);
    }
    if (!$this->hasHeader(self::HEADER_PARAM_USER_AGENT)) {
      $this->addHeader(self::HEADER_PARAM_USER_AGENT, self::HEADER_VALUE_USER_AGENT);
    }
    if (!$this->hasHeader(self::HEADER_PARAM_APP_VERSION)) {
      $this->addHeader(self::HEADER_PARAM_APP_VERSION, self::HEADER_VALUE_APP_VERSION);
    }
    if (!$this->hasHeader(self::HEADER_PARAM_APP_PLATFORM)) {
      $this->addHeader(self::HEADER_PARAM_APP_PLATFORM, $this->platform?$this->platform:self::HEADER_VALUE_APP_PLATFORM);
    }
    if (!$this->hasHeader(self::HEADER_PARAM_OS_PLATFORM_VERSION)) {
      $this->addHeader(self::HEADER_PARAM_OS_PLATFORM_VERSION, $this->platformVersion?$this->platformVersion:self::HEADER_VALUE_OS_PLATFORM_VERSION);
    }
    if (!$this->hasHeader(self::HEADER_PARAM_APP_NAME)) {
      $this->addHeader(self::HEADER_PARAM_APP_NAME, $this->getConfig('app_name'));
    }
    $this->bearerToken();
  }

  private function bearerToken(): void {
    if (!$this->hasHeader(self::HEADER_PARAM_ACCESS_TOKEN)) {
      if (!isset($_COOKIE['ezpz_token'])) {
        $tokenUri = $this->getConfig('token_uri');
        if ($this->getConfig('env') === 'local') {
          $this->verifyPeer(false);
        }
        $response = Request::post(
          $this->url($tokenUri), $this->getHeaders(),
          null,
          $this->getConfig('client_id'),
          $this->getConfig('client_secret')
        );
        if (
          isset($response->body->data)
          && isset($response->body->data->AuthorizationBearerToken)
          && isset($response->body->data->expire_in)
        ) {
          setcookie('ezpz_token', $response->body->data->AuthorizationBearerToken, time() + ($response->body->data->expire_in - (10 * 60 * 1000)), "/");
          $this->addHeader(self::HEADER_PARAM_ACCESS_TOKEN, 'Bearer ' . $response->body->data->AuthorizationBearerToken);
        }
      }
      else {
        $this->addHeader(self::HEADER_PARAM_ACCESS_TOKEN, 'Bearer ' . $_COOKIE['ezpz_token']);
      }
    }
  }

  public static function getTokenUri(): string {return '/api/user/token';}

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

  public static function apiHost(string $env): string {
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

  public static function apiEndpointPfx(string $env): string {return self::apiSchema($env).self::apiHost($env);}

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

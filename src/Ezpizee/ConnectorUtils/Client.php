<?php

namespace Ezpizee\ConnectorUtils;

use Ezpizee\MicroservicesClient\Client as MicroserviceClient;
use Ezpizee\Utils\EncodingUtil;
use Unirest\Request;

class Client extends MicroserviceClient
{
    const DEFAULT_ACCESS_TOKEN_KEY = "ezpz_access_token";

    public static function install(string $tokenKey, array $data): array
    {
        $env = isset($data['env']) ? $data['env'] : '';
        $url = self::apiSchema($env) . self::apiHost($env) . Endpoints::INSTALL;
        $response = Request::post(
            $url,
            null,
            $data
        );

        if (isset($response->body->data)
            && isset($response->body->data->AuthorizationBearerToken)
            && isset($response->body->data->expire_in)) {
            $expire = time() + ($response->body->data->expire_in - (10 * 60 * 1000));
            setcookie($tokenKey, $response->body->data->AuthorizationBearerToken, $expire, "/");
            setcookie($tokenKey . '_ei', $response->body->data->expire_in, $expire, "/");
            return json_decode($response->raw_body, true);
        } else {
            return json_decode(EncodingUtil::isValidJSON($response->raw_body) ? $response->raw_body : '[]', true);
        }
    }

    public static function getTokenUri(): string
    {
        return Endpoints::GET_TOKEN;
    }

    public static function adminUri(string $platform = 'ezpz', string $version = 'latest'): string
    {
        return '/adminui/' . $version . '/index.' . $platform . '.html';
    }

    public static function installUri(string $platform = 'ezpz'): string
    {
        return '/install/html/index.' . $platform . '.html';
    }

    public static function apiSchema(string $env): string {return 'http'.($env==='local'?'s':'s').'://';}

    public static function apiHost(string $env): string
    {
        return $env === 'dev' ? $env . '-api.ezpz.solutions' : ($env === 'prod' ? '' : $env . '-') . 'api.ezpizee.com';
    }

    public static function apiEndpointPfx(string $env): string
    {
        return self::apiSchema($env) . self::apiHost($env);
    }

    public static function cdnSchema(string $env): string {return 'http'.($env==='local'?'s':'s').'://';}

    public static function cdnHost(string $env): string
    {
        return ($env === 'prod' ? '' : $env . '-') . 'cdn.ezpz.solutions';
    }

    public static function cdnEndpointPfx(string $env): string
    {
        return self::cdnSchema($env) . self::cdnHost($env);
    }
}

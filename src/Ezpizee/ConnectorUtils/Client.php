<?php

namespace Ezpizee\ConnectorUtils;

use Ezpizee\MicroservicesClient\Client as MicroserviceClient;
use Ezpizee\MicroservicesClient\Token;
use Ezpizee\MicroservicesClient\TokenHandlerInterface;
use Ezpizee\Utils\EncodingUtil;
use Ezpizee\Utils\Logger;
use Ezpizee\Utils\ResponseCodes;
use Unirest\Request;
use RuntimeException;

class Client extends MicroserviceClient
{
    const DEFAULT_ACCESS_TOKEN_KEY = "ezpz_access_token";

    public static function register(array $data)
    : array
    {
        if (
            !isset($data['env']) || !isset($data['email']) || !isset($data['password']) || !isset($data['appname']) ||
            empty($data['env']) || empty($data['email']) || empty($data['password']) || empty($data['appname']) ||
            !isset($data['cellphone']) || empty($data['cellphone']) ||
            !isset($data['system_user_client_id']) || empty($data['system_user_client_id']) ||
            !isset($data['system_user_client_secret']) || empty($data['system_user_client_secret'])
        ) {
            throw new RuntimeException('Invalid data provided: '.self::class.'->register',
                ResponseCodes::CODE_ERROR_INVALID_DATA);
        }

        $url = self::apiEndpointPfx($data['env']) . Endpoints::REGISTER;

        Logger::debug("API Call: POST " . $url);

        $response = Request::post($url, [
            self::HEADER_PARAM_APP_NAME => $data['appname'],
            self::HEADER_PARAM_USER_AGENT => self::HEADER_VALUE_USER_AGENT,
            self::HEADER_PARAM_ACCESS_TOKEN => 'Basic '.base64_encode($data['system_user_client_id'].':'.$data['system_user_client_secret'])
        ], [
            'email' => $data['email'],
            'password' => $data['password'],
            'cellphone' => $data['cellphone'],
            'suppress_activate_email' => isset($data['suppress_activate_email']) ? $data['suppress_activate_email'] : 0
        ]);

        if (isset($response->body->data) && (int)$response->code === 200) {
            return json_decode($response->raw_body, true);
        }
        else {
            return json_decode(EncodingUtil::isValidJSON($response->raw_body) ? $response->raw_body : '[]', true);
        }
    }

    public static function login(array $data)
    : array
    {
        if (!isset($data['env']) || !isset($data['username']) || !isset($data['password']) || !isset($data['appname']) ||
            empty($data['env']) || empty($data['username']) || empty($data['password']) || empty($data['appname'])) {
            throw new RuntimeException('Invalid data provided: '.self::class.'->login',
                ResponseCodes::CODE_ERROR_INVALID_DATA);
        }

        $url = self::apiEndpointPfx($data['env']) . Endpoints::LOGIN;

        Logger::debug("API Call: POST " . $url);

        $response = Request::post($url, [
            self::HEADER_PARAM_APP_NAME => $data['appname'],
            self::HEADER_PARAM_USER_AGENT => self::HEADER_VALUE_USER_AGENT,
            self::HEADER_PARAM_ACCESS_TOKEN => 'Basic '.base64_encode($data['username'].':'.$data['password'])
        ]);

        if (isset($response->body->data)
            && isset($response->body->data->token_param_name)
            && isset($response->body->data->{$response->body->data->token_param_name})
            && isset($response->body->data->expire_in)) {
            return json_decode($response->raw_body, true);
        }
        else {
            return json_decode(EncodingUtil::isValidJSON($response->raw_body) ? $response->raw_body : '[]', true);
        }
    }

    public static function logout(array $data)
    : void
    {
        if (!isset($data['env']) || !isset($data['token']) || !isset($data['appname']) ||
            empty($data['env']) || empty($data['token']) || empty($data['appname'])) {
            throw new RuntimeException('Invalid data provided: '.self::class.'->logout',
                ResponseCodes::CODE_ERROR_INVALID_DATA);
        }

        $url = self::apiEndpointPfx($data['env']) . Endpoints::LOGOUT;

        Logger::debug("API Call: POST " . $url);

        Request::post($url, [
            self::HEADER_PARAM_USER_AGENT => self::HEADER_VALUE_USER_AGENT,
            self::HEADER_PARAM_APP_NAME => $data['appname'],
            self::HEADER_PARAM_ACCESS_TOKEN => 'Bearer '.$data['token']
        ]);
    }

    public static function install(string $tokenKey, array $data, $tokenHandler)
    : array
    {
        $env = isset($data['env']) ? $data['env'] : '';
        $url = self::apiEndpointPfx($env) . Endpoints::INSTALL;

        Logger::debug("API Call: POST " . $url);

        $response = Request::post($url, [self::HEADER_PARAM_USER_AGENT => self::HEADER_VALUE_USER_AGENT], $data);

        if (isset($response->body->data)
            && isset($response->body->data->token_param_name)
            && isset($response->body->data->{$response->body->data->token_param_name})
            && isset($response->body->data->expire_in)) {
            $cookieVal = uniqid(self::SESSION_COOKIE_VALUE_PFX);
            $tokenHandler = new $tokenHandler($cookieVal);
            if ($tokenHandler instanceof TokenHandlerInterface) {
                $tokenHandler->setCookie($tokenKey, $cookieVal);
                $tokenHandler->keepToken(new Token(json_decode(json_encode($response->body->data), true)));
            }

            return json_decode($response->raw_body, true);
        }
        else {
            return json_decode(EncodingUtil::isValidJSON($response->raw_body) ? $response->raw_body : '[]', true);
        }
    }

    public static function apiSchema(string $env)
    : string
    {
        return 'http' . ($env === 'local' ? '' : 's') . '://';
    }

    public static function apiHost(string $env)
    : string
    {
        return ($env === 'prod' ? '' : $env . '-') . 'api.ezpizee.com';
    }

    public static function getTokenUri()
    : string
    {
        return Endpoints::GET_TOKEN;
    }

    public static function adminUri(string $platform = 'ezpz', string $version = 'latest')
    : string
    {
        return '/adminui/' . $version . '/index.' . $platform . '.html';
    }

    public static function installUri(string $platform = 'ezpz')
    : string
    {
        return '/install/html/index.' . $platform . '.html';
    }

    public static function apiEndpointPfx(string $env)
    : string
    {
        return self::apiSchema($env) . self::apiHost($env);
    }

    public static function cdnEndpointPfx(string $env)
    : string
    {
        return self::cdnSchema($env) . self::cdnHost($env);
    }

    public static function cdnSchema(string $env)
    : string
    {
        return 'http' . ($env === 'local' ? '' : 's') . '://';
    }

    public static function cdnHost(string $env)
    : string
    {
        return ($env === 'prod' ? '' : $env . '-') . 'cdn.ezpz.solutions';
    }
}

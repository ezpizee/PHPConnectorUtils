<?php

namespace Ezpizee\ConnectorUtils;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

class JWT
{
  const JWT_API_CORE_ISSUER = "ezpz.api.core";

  private function __construct(){}

  public static final function encode(string $audience, array $data): string {
    $signer = new Sha256();
    return (new Builder())
      ->withHeader('alg', $signer->getAlgorithmId())
      ->withHeader('typ', 'JWT')
      ->issuedBy(self::JWT_API_CORE_ISSUER)
      ->permittedFor($audience)
      ->withClaim('data', json_encode($data))
      ->getToken($signer);
  }

  public static function isValidToken(string $audience, string $token): bool {
    if (!empty($token)) {
      $parsedToken = (new Parser())->parse($token);
      $validationData = new ValidationData();
      $validationData->setIssuer(self::JWT_API_CORE_ISSUER);
      $validationData->setAudience($audience);
      $signer = new Sha256();
      return $parsedToken->validate($validationData) && $parsedToken->getHeader('alg') === $signer->getAlgorithmId();
    }
    return false;
  }

  public static final function parseToken(string $token): Token {return (new Parser())->parse($token);}

  public static final function getDataFromToken(string $token): string {
    $parsed = self::parseToken($token);
    return $parsed->hasClaim('data') ? $parsed->getClaim('data') : null;
  }

  public static final function getIssuerFromToken(string $token): string {
    $parsed = self::parseToken($token);
    return $parsed->hasClaim('iss') ? $parsed->getClaim('iss') : null;
  }

  public static final function getAudienceFromToken(string $token): string {
    $parsed = self::parseToken($token);
    return $parsed->hasClaim('aud') ? $parsed->getClaim('aud') : null;
  }
}

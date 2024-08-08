<?php

// src/Service/JWTService.php

namespace App\Services\Token;

use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Symfony\Bundle\SecurityBundle\Security;

class JWTService
{
    private Configuration $config;

    public function __construct(
        $jwtDefaultSecretKey,
        Security $security
    )
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($security->getUser()?->getUserIdentifier() || $jwtDefaultSecretKey)
        );
    }

    public function generateToken($claim): Plain
    {
        $now = new \DateTimeImmutable();
        return $this->config->builder()
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $claim)
            ->getToken($this->config->signer(), $this->config->signingKey());
    }

    public function parseToken(string $token): Token
    {
        return $this->config->parser()->parse($token);
    }

    public function verifyToken(Token $token): bool
    {
        $signedWithConstraint = new SignedWith(
            $this->config->signer(),
            $this->config->signingKey()
        );

        $validAtConstraint = new ValidAt(
            SystemClock::fromUTC()
        );
        return $this->config->validator()->validate($token, $signedWithConstraint, $validAtConstraint);
    }
}

<?php

namespace App\Tests\Infrastructure\Security;

use App\Infrastructure\Security\TokenAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class TokenAuthenticatorTest extends TestCase
{
    private TokenAuthenticator $authenticator;

    protected function setUp(): void
    {
        $this->authenticator = new TokenAuthenticator();
    }

    public function testSupportsWhenAuthorizationHeaderPresent(): void
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer admintoken');

        $this->assertTrue($this->authenticator->supports($request));
    }

    public function testSupportsWhenAuthorizationHeaderMissing(): void
    {
        $request = new Request();

        $this->assertFalse($this->authenticator->supports($request));
    }

    public function testAuthenticationSuccessWithValidToken(): void
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer admintoken');

        $passport = $this->authenticator->authenticate($request);

        $this->assertTrue($passport->hasBadge(\Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge::class));
    }

    public function testAuthenticationFailsWithInvalidToken(): void
    {
        $this->expectException(\Symfony\Component\Security\Core\Exception\AuthenticationException::class);

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer invalidtoken');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticationFailsWithNoToken(): void
    {
        $this->expectException(\Symfony\Component\Security\Core\Exception\AuthenticationException::class);

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer ');

        $this->authenticator->authenticate($request);
    }
}

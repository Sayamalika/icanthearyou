<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class DatabaseUser implements Authenticatable
{
    private ?string $rememberToken = null;

    public function __construct(
        public readonly int $iduser,
        public readonly string $username,
        private string $passwordHash,
        public readonly int $idrole,
    ) {
    }

    public function getAuthIdentifierName(): string
    {
        return 'iduser';
    }

    public function getAuthIdentifier(): int
    {
        return $this->iduser;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): string
    {
        return $this->passwordHash;
    }

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function setRememberToken($value): void
    {
        $this->rememberToken = $value;
    }

    public function getRememberTokenName(): string
    {
        return '';
    }
}

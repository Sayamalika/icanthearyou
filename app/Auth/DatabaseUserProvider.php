<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\ConnectionInterface;
use LogicException;

class DatabaseUserProvider implements UserProvider
{
    public function __construct(
        private ConnectionInterface $connection,
        private Hasher $hasher,
    ) {
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        $user = $this->connection->selectOne(
            'SELECT nomor_pengguna AS iduser, nama AS username, sandi AS password, nomor_peran AS idrole
            FROM v_db_user
            WHERE nomor_pengguna = ?',
            [$identifier]
        );

        return $user === null ? null : $this->createUser($user);
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token): void
    {
        throw new LogicException('Remember-me authentication is not supported by the user table.');
    }

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        if (! isset($credentials['username']) || ! is_string($credentials['username'])) {
            return null;
        }

        $user = $this->connection->selectOne(
            'SELECT nomor_pengguna AS iduser, nama AS username, sandi AS password, nomor_peran AS idrole
            FROM v_db_user
            WHERE nama = ?',
            [$credentials['username']]
        );

        return $user === null ? null : $this->createUser($user);
    }

    public function validateCredentials(
        Authenticatable $user,
        #[\SensitiveParameter] array $credentials
    ): bool {
        return isset($credentials['password'])
            && is_string($credentials['password'])
            && $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(
        Authenticatable $user,
        #[\SensitiveParameter] array $credentials,
        bool $force = false
    ): void {
        if (! isset($credentials['password']) || ! is_string($credentials['password'])) {
            return;
        }

        if (! $force && ! $this->hasher->needsRehash($user->getAuthPassword())) {
            return;
        }

        $this->connection->update(
            'UPDATE user
            SET password = ?
            WHERE iduser = ?',
            [
                $this->hasher->make($credentials['password']),
                $user->getAuthIdentifier(),
            ]
        );
    }

    private function createUser(object $user): DatabaseUser
    {
        return new DatabaseUser(
            (int) $user->iduser,
            $user->username,
            $user->password,
            (int) $user->idrole
        );
    }
}

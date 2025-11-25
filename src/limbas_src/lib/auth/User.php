<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\lib\auth;

use Limbas\lib\db\Database;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class User //implements UserInterface, PasswordAuthenticatedUserInterface
{
    protected static string $tableName = 'LMB_USERDB';

    private array $roles = [];
    
    
    public function __construct(
        private readonly int $id,
        private string       $username,
        private ?string      $email,
        private string       $password,
    )
    {
        
    }

    /**
     * @param int $id
     * @return User|null
     */
    public static function get(int $id): User|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }

    /**
     * @param int $id
     * @return User|null
     */
    public static function getByUsername(string $username): User|null
    {
        $output = self::all(['USERNAME' => $username]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }


    /**
     * @param array $where
     * @return array
     */
    public static function all(array $where = []): array
    {

        $rs = Database::select(self::$tableName, ['ID','USERNAME','PASSWORT','EMAIL'], $where);

        $output = [];

        while (lmbdb_fetch_row($rs)) {

            $output[] = new self(
                lmbdb_result($rs, 'ID') ?? '',
                    lmbdb_result($rs, 'USERNAME') ?? '',
                    lmbdb_result($rs, 'PASSWORT') ?? '',
                    lmbdb_result($rs, 'EMAIL') ?? '',
            );

        }

        return $output;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        //
    }
}

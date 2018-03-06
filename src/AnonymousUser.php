<?php
/* ===========================================================================
 * Copyright 2018 The Opis Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace OpisColibri\UsersSQLImpl;

use DateTime;
use OpisColibri\Permissions\{
    IPermission,
    IRole,
    IRoleRepository
};
use OpisColibri\Users\IUser;
use function Opis\Colibri\Functions\make;

class AnonymousUser implements IUser
{

    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function isAdmin(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function registrationDate(): DateTime
    {
        return new DateTime();
    }

    /**
     * @inheritDoc
     */
    public function setRegistrationDate(DateTime $date): IUser
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function lastLogin(): ?DateTime
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setLastLogin(DateTime $date): IUser
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(bool $value): IUser
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAnonymous(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function roles(): iterable
    {
        return [make(IRoleRepository::class)->getByName('anonymous')];
    }

    /**
     * @inheritDoc
     */
    public function setRoles(iterable $roles): IUser
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function permissions(): iterable
    {
        $permissions = [];

        /** @var IRole $role */
        foreach ($this->roles() as $role) {
            foreach ($role->permissions() as $permission) {
                $permissions[] = $permission;
            }
        }

        return $permissions;
    }

    /**
     * @inheritDoc
     */
    public function hasPermissions(array $permissions)
    {
        /** @var IPermission $user_permission */
        foreach ($this->permissions() as $user_permission) {
            /** @var IPermission $permission */
            foreach ($permissions as $permission) {
                if ($user_permission->name() !== $permission->name()) {
                    return false;
                }
            }
        }

        return true;
    }
}
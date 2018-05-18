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

namespace OpisColibri\UsersSQL;

use DateTime;
use function Opis\Colibri\Functions\{
    make, uuid4
};
use Opis\ORM\{
    Entity, IEntityMapper, IMappableEntity
};
use OpisColibri\Permissions\{
    IRole,
    IPermission,
    IRoleRepository
};
use OpisColibri\Users\{
    IUser, IUserSession
};

class User extends Entity implements IUser, IMappableEntity
{
    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return $this->orm()->getColumn('id');
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->orm()->getColumn('name');
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): IUser
    {
        $this->orm()->setColumn('name', $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function email(): string
    {
        return $this->orm()->getColumn('email');
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email): IUser
    {
        $this->orm()->setColumn('email', $email);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function avatar(): ?string
    {
        return $this->orm()->getColumn('avatar');
    }

    /**
     * @inheritDoc
     */
    public function setAvatar(string $avatar = null): IUser
    {
        $this->orm()->setColumn('avatar', $avatar);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAdmin(): bool
    {
        return $this->id() === make(IUserSession::class)->getAdminId();
    }

    /**
     * @inheritDoc
     */
    public function registrationDate(): DateTime
    {
        return $this->orm()->getColumn('registration_date');
    }

    /**
     * @param DateTime $date
     * @return User
     */
    public function setRegistrationDate(DateTime $date): IUser
    {
        $this->orm()->setColumn('registration_date', $date);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function lastLogin(): ?DateTime
    {
        return $this->orm()->getColumn('last_login');
    }

    /**
     * @param DateTime $date
     * @return User
     */
    public function setLastLogin(DateTime $date): IUser
    {
        $this->orm()->setColumn('last_login', $date);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return $this->orm()->getColumn('is_active');
    }

    /**
     * @param bool $value
     * @return User
     */
    public function setIsActive(bool $value): IUser
    {
        $this->orm()->setColumn('is_active', $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAnonymous(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function roles(): iterable
    {
        return $this->orm()->getColumn('roles');
    }

    /**
     * @inheritDoc
     */
    public function setRoles(iterable $roles): IUser
    {
        $this->orm()->setColumn('roles', $roles);
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
    public function hasPermissions(iterable $permissions)
    {
        $permission_list = [];

        /** @var IPermission $user_permission */
        foreach ($this->permissions() as $user_permission) {
            $permission_list[$user_permission->name()] = true;
        }

        /** @var IPermission $permission */
        foreach ($permissions as $permission) {
            if (!isset($permission_list[$permission->name()])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->orm()->isNew();
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(IEntityMapper $mapper)
    {
        $mapper->cast([
            'is_active' => 'boolean',
            'registration_date' => 'date',
            'last_login' => '?date',
            'roles' => 'json-assoc'
        ]);

        $mapper->setter('roles', function(iterable $roles) {
            $list = [];
            foreach ($roles as $role) {
                if ($role instanceof IRole) {
                    $list[$role->name()] = $role->id();
                }
            }
            if (!isset($list['authenticated'])) {
                $list['authenticated'] = make(IRoleRepository::class)->getByName('authenticated')->id();
            }
            return $list;
        });

        $mapper->getter('roles', function ($roles) {
            $list = [];
            $items = make(IRoleRepository::class)->getMultipleByName(array_keys($roles));
            foreach ($items as $role) {
                if ($roles[$role->name()] === $role->id()) {
                    $list[] = $role;
                }
            }
            return $list;
        });

        $mapper->primaryKeyGenerator(function () {
            return uuid4('');
        });
    }
}
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

use Opis\ORM\Entity;
use OpisColibri\Users\{
    IUser,
    IUserRepository
};
use function Opis\Colibri\Functions\{
    entity, entityManager
};

class UserRepository implements IUserRepository
{
    /** @var string */
    protected $entity;

    public function __construct(string $entity = null)
    {
        if ($entity === null) {
            $entity = User::class;
        }
        $this->entity = $entity;
    }

    /**
     * Create user
     * @return IUser|User
     */
    public function create(): IUser
    {
        /** @var User $user */
        $user = entityManager()->create($this->entity);
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function getById(string $id): ?IUser
    {
        /** @var User|null $user */
        $user = entity($this->entity)->find($id);
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function getByEmail(string $email): ?IUser
    {
        /** @var User|null $user */
        $user = entity($this->entity)->where('email')->is($email)->get();
        return $user;
    }

    /**
     * Save modified user
     *
     * @param IUser|User $user
     * @return bool
     */
    public function save(IUser $user): bool
    {
        if (!$user instanceof Entity) {
            return false;
        }

       return entityManager()->save($user);
    }

    /**
     * Delete user
     *
     * @param IUser|User $user
     * @return bool
     */
    public function delete(IUser $user): bool
    {
        return entityManager()->delete($user);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(string $id): bool
    {
        return (bool) entity($this->entity)
            ->where('id')->is($id)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleById(array $ids): int
    {
        return entity($this->entity)
            ->where('id')->in($ids)
            ->delete();
    }
}
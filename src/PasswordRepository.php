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

namespace Opis\Colibri\Modules\UsersSQL;

use Opis\Colibri\Modules\Users\{
    IUser,
    Security\IPassword,
    Security\IPasswordRepository
};
use function Opis\Colibri\Functions\{
    entity,
    entityManager
};

class PasswordRepository implements IPasswordRepository
{

    /**
     * Create password
     *
     * @return IPassword|Password
     */
    public function create(): IPassword
    {
        /** @var Password $password */
        $password = entityManager()->create(Password::class);
        return $password;
    }

    /**
     * @inheritDoc
     */
    public function getById(string $id): ?IPassword
    {
        return entity(Password::class)->find($id);
    }

    /**
     * @inheritDoc
     */
    public function getByUserId(string $id): ?IPassword
    {
        return $this->getById($id);
    }

    /**
     * Get a password by user
     *
     * @param IUser $user
     * @return null|IPassword
     */
    public function getByUser(IUser $user): ?IPassword
    {
        return $this->getByUserId($user->id());
    }

    /**
     * Save password
     *
     * @param IPassword|Password $password
     * @return bool
     */
    public function save(IPassword $password): bool
    {
        return entityManager()->save($password);
    }

    /**
     * Delete password
     *
     * @param IPassword|Password $password
     * @return bool
     */
    public function delete(IPassword $password): bool
    {
        return entityManager()->delete($password);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(string $id): bool
    {
        return (bool)entity(Password::class)
            ->where('user_id')->is($id)
            ->delete();
    }
}
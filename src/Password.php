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

use Opis\ORM\{
    Entity,
    IEntityMapper,
    Core\DataMapper,
    Core\EntityMapper
};
use OpisColibri\Users\{
    IUser,
    Security\IPassword,
    Security\IPasswordHandler
};
use function Opis\Colibri\Functions\make;

class Password extends Entity implements IPassword, IEntityMapper
{

    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return $this->orm()->getColumn('user_id');
    }

    /**
     * @inheritDoc
     */
    public function user(): IUser
    {
        return $this->orm()->getRelated('user');
    }

    /**
     * @inheritDoc
     */
    public function value(): string
    {
        return $this->orm()->getColumn('value');
    }

    /**
     * @param IUser|User $user
     * @return Password
     */
    public function setUser(IUser $user): IPassword
    {
        $this->orm()->setRelated('user', $user);
        return $this;
    }

    /**
     * @param string $value
     * @return Password
     */
    public function setValue(string $value): IPassword
    {
        $this->orm()->setColumn('value', $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper)
    {
        $mapper->primaryKey('user_id');

        $mapper->primaryKeyGenerator(function (DataMapper $data) {
            return $data->getColumn('user_id');
        });

        $mapper->setter('value', function(string $value){
            return make(IPasswordHandler::class)->hash($value);
        });

        $mapper->relation('user')->belongsTo(User::class);
    }
}
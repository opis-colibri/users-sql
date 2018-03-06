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

use function Opis\Colibri\Functions\entityManager;
use function Opis\Colibri\Functions\make;
use function Opis\Colibri\Functions\session;
use OpisColibri\Users\IUser;
use OpisColibri\Users\IUserCredentials;
use OpisColibri\Users\IUserRepository;
use OpisColibri\Users\IUserSession;

class UserSession implements IUserSession
{
    const USER_KEY = 'authenticated_user';

    /** @var  AnonymousUser|null */
    private $anonymous = null;

    /** @var User|null */
    private $user = null;

    /**
     * @param IUser|User $user
     * @param IUserCredentials $credentials
     * @return bool
     */
    public function authenticate(IUser $user, IUserCredentials $credentials): bool
    {
        if ($credentials->validate($user)) {
            $user->setLastLogin(new \DateTime());
            entityManager()->save($user);
            session()->set(self::USER_KEY, $user->id());
            $this->user = $user;
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function signOut(IUser $user): bool
    {
        if (!$user->isAnonymous()) {
            return session()->destroy();
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function currentUser(): IUser
    {
        $session = session();
        if ($session->has(self::USER_KEY)) {
            if ($this->user !== null) {
                return $this->user;
            }
            $user = make(IUserRepository::class)
                ->getById($session->get(self::USER_KEY));
            if ($user !== null) {
                return $this->user = $user;
            } else {
                $session->delete(self::USER_KEY);
            }
        }
        if ($this->anonymous === null) {
            $this->anonymous = new AnonymousUser();
        }
        return $this->anonymous;
    }
}
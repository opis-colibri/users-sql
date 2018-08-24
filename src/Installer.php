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

use function Opis\Colibri\Functions\schema;
use Opis\Colibri\Installer as AbstractInstaller;
use Opis\Database\Schema\CreateTable;

class Installer extends AbstractInstaller
{
    /**
     * @throws \Exception
     */
    public function install()
    {
        $schema = schema();

        $schema->create('users', function (CreateTable $table) {
            $table->fixed('id', 32)->notNull()->primary();
            $table->string('name')->notNull();
            $table->string('email')->notNull()->unique();
            $table->string('avatar');
            $table->dateTime('registration_date')->notNull();
            $table->dateTime('last_login');
            $table->boolean('is_active')->notNull()->defaultValue(false);
            $table->binary('roles')->notNull();
        });

        $schema->create('passwords', function (CreateTable $table) {
            $table->fixed('user_id', 32)->primary()->notNull();
            $table->string('value')->notNull();

            $table->foreign('user_id')
                ->references('users', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * @throws \Exception
     */
    public function uninstall()
    {
        $schema = schema();
        $schema->drop('passwords');
        $schema->drop('users');
    }
}

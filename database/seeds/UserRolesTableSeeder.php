<?php

use Illuminate\Database\Seeder;

class UserRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('user_roles')->delete();

        \DB::table('user_roles')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'role_id' => 1,
                    'user_id' => 1,
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            1 =>
                array(
                    'id' => 2,
                    'role_id' => 2,
                    'user_id' => 1,
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
        ));


    }
}

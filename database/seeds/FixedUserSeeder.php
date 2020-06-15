<?php

use App\User;
use Illuminate\Database\Seeder;

class FixedUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $user = User::create([
            'name' => 'Mohamad Murad',
            'email' => 'mhdite7@gmail.com',
            'phone' => '+963960602327',
            'username' => 'mhdite7',
            'location' => 'Damascus',
            'password' => bcrypt('12345678'),
        ]);

        $user->assignRole('super_admin');

        $user1 = User::create([
            'name' => 'Mouaz He',
            'email' => 'mouaz@gmail.com',
            'phone' => '+963960602322',
            'username' => 'mouaz',
            'location' => 'Damascus',
            'password' => bcrypt('12345678'),
        ]);



        $user1->assignRole(['employee']);
    }
}

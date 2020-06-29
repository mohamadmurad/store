<?php

use App\Branches;
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

        // super admin
        $user = User::create([
            'name' => 'Mohamad Murad',
            'email' => 'mhdite7@gmail.com',
            'phone' => '+963960602327',
            'username' => 'mhdite7',
            'location' => 'Damascus',
            'password' => bcrypt('12345678'),
        ]);

        $user->assignRole('Super Admin');

        // super employee
        $user1 = User::create([
            'name' => 'Mouaz He',
            'email' => 'mouaz@gmail.com',
            'phone' => '+963960602322',
            'username' => 'mouaz',
            'location' => 'Damascus',
            'password' => bcrypt('12345678'),
        ]);
        $user1->assignRole(['super_employee']);

        $rand_branch = Branches::all()->random(1)->first();
        $rand_branch->user_id = $user1->id;
        $rand_branch->save();

        // employee
        $user2 = User::create([
            'name' => 'Hasan As',
            'email' => 'hasan@gmail.com',
            'phone' => '+963960602321',
            'username' => 'Hasan',
            'location' => 'Damascus',
            'password' => bcrypt('12345678'),
        ]);
        $user2->assignRole(['employee']);

        $rand_branch = Branches::all()->random(1)->first();
        $rand_branch->user_id = $user2->id;
        $rand_branch->save();


        // costumer
        $user3 = User::create([
            'name' => 'meheden kh',
            'email' => 'meheden@gmail.com',
            'phone' => '+963960602320',
            'username' => 'meheden',
            'location' => 'Damascus',
            'password' => bcrypt('12345678'),
        ]);
        $user3->assignRole(['customer']);
    }
}

<?php

use App\Attachment;
use App\AttachmentType;
use App\Attributes;
use App\Branches;
use App\Cards;
use App\Categories;
use App\Companies;
use App\Coupons;
use App\Groups;
use App\Offers;
use App\Orders;
use App\Products;
use App\Rate;
use App\Sales;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        Attachment::truncate();
        AttachmentType::truncate();
        Attributes::truncate();
        Branches::truncate();
        Cards::truncate();
        Categories::truncate();
        Companies::truncate();
        Coupons::truncate();
        Groups::truncate();
        Offers::truncate();
        Orders::truncate();
        Products::truncate();
        Rate::truncate();
        Sales::truncate();
        User::truncate();

        // many to many
        DB::table('attributes_branches')->truncate();
        DB::table('favorite')->truncate();
        DB::table('offers_products')->truncate();
        DB::table('orders_products')->truncate();


        Attachment::flushEventListeners();
        AttachmentType::flushEventListeners();
        Attributes::flushEventListeners();
        Branches::flushEventListeners();
        Cards::flushEventListeners();
        Categories::flushEventListeners();
        Companies::flushEventListeners();
        Coupons::flushEventListeners();
        Groups::flushEventListeners();
        Offers::flushEventListeners();

        Orders::flushEventListeners();

        Products::flushEventListeners();

        Rate::flushEventListeners();

        Sales::flushEventListeners();
        User::flushEventListeners();

        $attach_typeQuantity = 2;
        $branchesQuantity = 25;
        $attributesQuantity = 7;
        $categoryQuantity = 10;
        $CompaniesQuantity = 5;
        $couponsQuantity = 5;
        $offersQuantity = 8;
        $productsQuantity = 100;
        $salesQuantity = 4;
        $userQuantity = 100;
        $groupsQuantity = 4;
        $favoriteQuantity = 50;
        $ratesQuantity=60;
        $ordersQuantity=100;


        $this->call(RolesAndPermissionsSeeder::class);

        $this->call(FixedAttachment::class);

        // users
       /* factory(User::class, $userQuantity)->create()->each(function ($u){
            $u->assignRole(['customer']);
            $rand = rand(1,0);
            $u->card()->save(factory(Cards::class)->make());

            $rand === 1 ? $u->assignRole(['employee']) : $u->assignRole(['super_employee']);
        });*/

        // attribute
        factory(Attributes::class, $attributesQuantity)->create();

        // company
        //factory(Companies::class, $CompaniesQuantity)->create();


        // category
       /* factory(Categories::class, $categoryQuantity)->create()->each(function ($cat){
            $rand = rand(1,0);
            if( Categories::first()->id  && $rand === 1){
                $parent = Categories::all()->random();
                $cat->parent_id = $parent->id;
                $cat->save();
            }
        });*/

        // branches
       /* factory(Branches::class, $branchesQuantity)->create()->each(function ($branch){
            $usersInBranchTable = Branches::all()->pluck('user_id');
            $user = User::all()->whereNotIn('id',$usersInBranchTable)->random(1);
            $branch->user_id = $user;
            $attribute = Attributes::all()->random();
            $rand= rand(1,0);
            if($rand === 1 ){
                $branch->attributes()->attach($attribute);
            }

        });*/

        // groups
        //factory(Groups::class, $groupsQuantity)->create();

        // coupons
        //factory(Coupons::class, $couponsQuantity)->create();

        // products
       /* factory(Products::class, $productsQuantity)->create()->each(function ($product) {
            $rand = rand(1,0);
            if($rand===1 && Products::first()->id){
                $product->parent_id = Products::all()->random()->id;
            }
            $rand = rand(1,0);

            if($rand===1){
                $product->category_id = Categories::all()->random()->id;
            }

            $rand = rand(1,0);

            if($rand===1){
                $product->group_id = Groups::all()->random()->id;
            }
            $branch = branches::all()->random()->id;
            $product->branch_id = $branch;

            $rand = rand(1,4);

            for ($i=0;$i<$rand;$i++){
                $product->attachments()->save(factory(Attachment::class)->make());
            }

            $branch_attribute = Attributes::all()->random()->id;

            DB::insert('insert into attribute_values (value,attributes_id , products_id) values (?, ? , ?)',[\Illuminate\Support\Str::random(5),$branch_attribute,$product->id]);


            // rate
         //   $user = User::all()->random()->id;
         //   $product->rates()->attach();

            $product->save();

        });*/

        // rate
        //factory(Rate::class, $ratesQuantity)->create();

        // sale
        //factory(Sales::class, $salesQuantity)->create();

        // offer
        /*factory(Offers::class, $offersQuantity)->create()->each(function ($offer) {
            $branch_id = rand(1,25);

            $product = products::all()->where('branch_id','=',$branch_id);
            if (count($product) > 2){

                $product = $product->random(3)->pluck('id');
            }else{
                $product= $product->pluck('id');
            }

            $offer->products()->attach($product);
        });*/

        // favorate
       /* for ($i=0;$i<$favoriteQuantity;$i++){
            $product = products::all()->random();
            $user = User::all()->random()->id;
            $product->favorite()->attach($user);
        //    DB::insert('insert into favorite (users_id , products_id) values (?, ?)',[$user,$product]);
        }*/
/*
        factory(Orders::class, $ordersQuantity)->create()->each(function ($order) {
            $rand = rand(1,6);
            $product = products::all()->random($rand)->pluck('id');

            $order->products()->attach($product);
        });*/


        $this->call(FixedUserSeeder::class);
       // $this->call(FixedOrders::class);

    }
}

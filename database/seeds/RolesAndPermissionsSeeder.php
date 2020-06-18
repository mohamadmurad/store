<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // admin
        $admin_role = Role::create(['guard_name' => 'api','name' => 'super_admin']);
        //$admin_role->givePermissionTo(Permission::all());

        // super_employee role
        $super_employee_role = Role::create(['guard_name' => 'api','name' => 'super_employee']);
        //$super_employee_role->givePermissionTo(Permission::all());

        // employee role
        $employee_role = Role::create(['guard_name' => 'api','name' => 'employee']);

        // customer
        $customer_role = Role::create(['guard_name' => 'api','name' => 'customer']);








        // create admin permissions
        Permission::create(['guard_name' => 'api','name' => 'add_user']);
        Permission::create(['guard_name' => 'api','name' => 'edit_user']);
        Permission::create(['guard_name' => 'api','name' => 'delete_user']);
        Permission::create(['guard_name' => 'api','name' => 'read_user']);


        // create "super_employee" permissions on company
        $edit_company_per = Permission::create(['guard_name' => 'api','name' => 'edit_company']);
        $edit_company_per->assignRole($super_employee_role);

        // create "employee" permissions on branch
        $add_product_per = Permission::create(['guard_name' => 'api','name' => 'add_product']);
        $edit_product_per = Permission::create(['guard_name' => 'api','name' => 'edit_product']);
        $delete_product_per = Permission::create(['guard_name' => 'api','name' => 'delete_product']);

        $add_product_per->assignRole($employee_role);
        $edit_product_per->assignRole($employee_role);
        $delete_product_per->assignRole($employee_role);

        $add_product_per->assignRole($super_employee_role);
        $edit_product_per->assignRole($super_employee_role);
        $delete_product_per->assignRole($super_employee_role);


        // sales
        $add_sale_per = Permission::create(['guard_name' => 'api','name' => 'add_sale']);
        $edit_sale_per = Permission::create(['guard_name' => 'api','name' => 'edit_sale']);
        $delete_sale_per = Permission::create(['guard_name' => 'api','name' => 'delete_sale']);

        $add_sale_per->assignRole($employee_role);
        $edit_sale_per->assignRole($employee_role);
        $delete_sale_per->assignRole($employee_role);

        $add_sale_per->assignRole($super_employee_role);
        $edit_sale_per->assignRole($super_employee_role);
        $delete_sale_per->assignRole($super_employee_role);


        // offers
        $add_offer_per = Permission::create(['guard_name' => 'api','name' => 'add_offer']);
        $edit_offer_per = Permission::create(['guard_name' => 'api','name' => 'edit_offer']);
        $delete_offer_per = Permission::create(['guard_name' => 'api','name' => 'delete_offer']);


        $add_offer_per->assignRole($employee_role);
        $edit_offer_per->assignRole($employee_role);
        $delete_offer_per->assignRole($employee_role);

        $add_offer_per->assignRole($super_employee_role);
        $edit_offer_per->assignRole($super_employee_role);
        $delete_offer_per->assignRole($super_employee_role);


        // global permissions for all user
//        Permission::create(['guard_name' => 'api','name' => 'read_product']);
//        Permission::create(['guard_name' => 'api','name' => 'read_sale']);
//        Permission::create(['guard_name' => 'api','name' => 'read_offer']);

        $read_account_per = Permission::create(['guard_name' => 'api','name' => 'read_account']);
        $edit_account_per = Permission::create(['guard_name' => 'api','name' => 'edit_account']);






    }
}

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
        $admin_role = Role::create(['guard_name' => 'api','name' => 'Super Admin']);
        //$admin_role->givePermissionTo(Permission::all());

        // super_employee role
        $super_employee_role = Role::create(['guard_name' => 'api','name' => 'super_employee']);
        //$super_employee_role->givePermissionTo(Permission::all());

        // employee role
        $employee_role = Role::create(['guard_name' => 'api','name' => 'employee']);

        // customer
        $customer_role = Role::create(['guard_name' => 'api','name' => 'customer']);





        // attachment type
        $add_attachType_per = Permission::create(['guard_name' => 'api','name' => 'add_attachType']);
        $edit_attachType_per = Permission::create(['guard_name' => 'api','name' => 'edit_attachType']);
        $delete_attachType_per = Permission::create(['guard_name' => 'api','name' => 'delete_attachType']);

        // create admin permissions
        $add_user_per = Permission::create(['guard_name' => 'api','name' => 'add_user']);
        $edit_user_per = Permission::create(['guard_name' => 'api','name' => 'edit_user']);
        $delete_user_per = Permission::create(['guard_name' => 'api','name' => 'delete_user']);
        $read_user_per = Permission::create(['guard_name' => 'api','name' => 'read_user']);


        // search employee
        $employee_search_per = Permission::create(['guard_name' => 'api','name' => 'employee_search']);

        //show roles
        $show_roles_per = Permission::create(['guard_name' => 'api','name' => 'show_roles']);


        // category
        $add_category_per = Permission::create(['guard_name' => 'api','name' => 'add_category']);
        $edit_category_per = Permission::create(['guard_name' => 'api','name' => 'edit_category']);
        $delete_category_per = Permission::create(['guard_name' => 'api','name' => 'delete_category']);

        // attributes
        $add_attribute_per = Permission::create(['guard_name' => 'api','name' => 'add_attribute']);
        $edit_attribute_per = Permission::create(['guard_name' => 'api','name' => 'edit_attribute']);
        $delete_attribute_per = Permission::create(['guard_name' => 'api','name' => 'delete_attribute']);



        // search category
        $category_search_per = Permission::create(['guard_name' => 'api','name' => 'category_search']);
        $category_search_per->assignRole($super_employee_role);
        $category_search_per->assignRole($employee_role);


        // create permissions on company
        $add_company_per = Permission::create(['guard_name' => 'api','name' => 'add_company']);
        $edit_company_per = Permission::create(['guard_name' => 'api','name' => 'edit_company']);
        $delete_company_per = Permission::create(['guard_name' => 'api','name' => 'delete_company']);

        $edit_company_per->assignRole($super_employee_role);


        $add_branch_per = Permission::create(['guard_name' => 'api','name' => 'add_branch']);
        $edit_branch_per = Permission::create(['guard_name' => 'api','name' => 'edit_branch']);
        $delete_branch_per = Permission::create(['guard_name' => 'api','name' => 'delete_branch']);

        $edit_branch_per->assignRole($super_employee_role);

        // create product permissions on branch
        $add_product_per = Permission::create(['guard_name' => 'api','name' => 'add_product']);
        $edit_product_per = Permission::create(['guard_name' => 'api','name' => 'edit_product']);
        $delete_product_per = Permission::create(['guard_name' => 'api','name' => 'delete_product']);
        $show_product_with__without_sale_per = Permission::create(['guard_name' => 'api','name' => 'show_product_with_without_sale']);
        $show_all_product_info_per = Permission::create(['guard_name' => 'api','name' => 'show_all_product_info']);

        $add_product_per->assignRole($employee_role);
        $edit_product_per->assignRole($employee_role);
        $delete_product_per->assignRole($employee_role);
        $show_product_with__without_sale_per->assignRole($employee_role);
        $show_all_product_info_per->assignRole($employee_role);

        $add_product_per->assignRole($super_employee_role);
        $edit_product_per->assignRole($super_employee_role);
        $delete_product_per->assignRole($super_employee_role);
        $show_product_with__without_sale_per->assignRole($super_employee_role);
        $show_all_product_info_per->assignRole($super_employee_role);


        // create sale permissions on branch
        $add_sale_per = Permission::create(['guard_name' => 'api','name' => 'add_sale']);
        $delete_sale_per = Permission::create(['guard_name' => 'api','name' => 'delete_sale']);

        $add_sale_per->assignRole($employee_role);
        $delete_sale_per->assignRole($employee_role);

        $add_sale_per->assignRole($super_employee_role);
        $delete_sale_per->assignRole($super_employee_role);


        // offers
//        $add_offer_per = Permission::create(['guard_name' => 'api','name' => 'add_offer']);
//        $edit_offer_per = Permission::create(['guard_name' => 'api','name' => 'edit_offer']);
//        $delete_offer_per = Permission::create(['guard_name' => 'api','name' => 'delete_offer']);
//
//
//        $add_offer_per->assignRole($employee_role);
//        $edit_offer_per->assignRole($employee_role);
//        $delete_offer_per->assignRole($employee_role);
//
//        $add_offer_per->assignRole($super_employee_role);
//        $edit_offer_per->assignRole($super_employee_role);
//        $delete_offer_per->assignRole($super_employee_role);


        // global permissions for all user
//        Permission::create(['guard_name' => 'api','name' => 'read_product']);
//        Permission::create(['guard_name' => 'api','name' => 'read_sale']);
//        Permission::create(['guard_name' => 'api','name' => 'read_offer']);

        //$read_account_per = Permission::create(['guard_name' => 'api','name' => 'read_account']);
        //$edit_account_per = Permission::create(['guard_name' => 'api','name' => 'edit_account']);






    }
}

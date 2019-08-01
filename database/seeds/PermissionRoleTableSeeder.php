<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {   //set admin permissions
        $admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));
        
        //set user permissions
        $user_permissions = $admin_permissions->filter(function ($permission) {
            
            $user_permission_list = [
                'curriculum_show', 
               
            ];
            
            return in_array($permission->title, $user_permission_list);
        });
        Role::findOrFail(6)->permissions()->sync($user_permissions);
    }
}

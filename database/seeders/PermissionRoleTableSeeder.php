<?php

namespace Database\Seeders;

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {   //set admin permissions
        $admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));

        //set creator permissions
        $creator_permissions = $admin_permissions->filter(function ($permission) {
            $creator_permission_list = [
                'categorie_access',
                'categorie_create',
                'categorie_show',
                'categorie_edit',
                'categorie_delete',
                'certificate_access',
                'certificate_create',
                'certificate_show',
                'certificate_edit',
                'certificate_delete',
                'contactdetail_access',
                'contactdetail_create',
                'contactdetail_show',
                'contactdetail_edit',
                'contactdetail_delete',
                'content_access',
                'content_create',
                'content_show',
                'content_edit',
                'content_delete',
                'course_create',
                'course_show',
                'course_edit',
                'course_delete',
                'curriculum_access',
                'curriculum_create',
                'curriculum_show',
                'curriculum_edit',
                'curriculum_delete',
                'curriculum_print',
                'grade_access',
                'grade_create',
                'grade_show',
                'grade_edit',
                'grade_delete',
                'logbook_access',
                'logbook_create',
                'logbook_show',
                'logbook_edit',
                'logbook_delete',
                'logbook_entry_access',
                'logbook_entry_create',
                'logbook_entry_show',
                'logbook_entry_edit',
                'logbook_entry_delete',
                'kanban_access',
                'kanban_create',
                'kanban_show',
                'kanban_edit',
                'kanban_delete',
                'navigator_access',
                'navigator_create',
                'navigator_show',
                'navigator_edit',
                'navigator_delete',
                'objective_create',
                'objective_edit',
                'objective_delete',
                'organization_type_access',
                'organization_type_create',
                'organization_type_show',
                'organization_type_edit',
                'organization_type_delete',
                'period_access',
                'period_create',
                'period_show',
                'period_edit',
                'period_delete',
                'task_access',
                'task_create',
                'task_show',
                'task_edit',
                'task_delete',
                'subject_access',
                'subject_create',
                'subject_show',
                'subject_edit',
                'subject_delete',
                'glossar_access',
                'glossar_create',
                'glossar_show',
                'glossar_edit',
                'glossar_delete',
                'message_access',
                'message_create',
                'message_show',
                'message_edit',
                'message_delete',
                'objectivetype_access',
                'objectivetype_create',
                'objectivetype_show',
                'objectivetype_edit',
                'objectivetype_delete',
                'curriculum_create_global',
                'App\Curriculum_content_create',
                'App\Curriculum_content_edit',
                'App\Curriculum_content_delete',
                'App\LogbookEntry_content_create',
                'App\LogbookEntry_content_edit',
                'App\LogbookEntry_content_delete',
                'App\Task_content_create',
                'App\Task_content_edit',
                'App\Task_content_delete',
                'App\EnablingObjective_content_create',
                'App\EnablingObjective_content_edit',
                'App\EnablingObjective_content_delete',
                'App\TerminalObjective_content_create',
                'App\TerminalObjective_content_edit',
                'App\TerminalObjective_content_delete',
                'App\Glossar_content_create',
                'App\Glossar_content_edit',
                'App\Glossar_content_delete',
                'lms_access',
                'lms_create',
                'lms_show',
                'lms_edit',
                'lms_delete',
            ];

            return in_array($permission->title, $creator_permission_list);
        });
        Role::findOrFail(2)->permissions()->sync($creator_permissions);

        //set Indexer permissions
        $indexer_permissions = $admin_permissions->filter(function ($permission) {
            $indexer_permission_list = [
                'curriculum_show',
                'curriculum_print',
                'navigator_show',
                'logbook_access',
                'logbook_show',
                'logbook_entry_access',
                'logbook_entry_show',
                'task_access',
                'task_show',
                'lms_access',
                'lms_create',
                'lms_show',
                'lms_edit',
                'lms_delete',
            ];

            return in_array($permission->title, $indexer_permission_list);
        });
        Role::findOrFail(3)->permissions()->sync($indexer_permissions);

        //set schooladmin permissions
        $schooladmin_permissions = $admin_permissions->filter(function ($permission) {
            $schooladmin_permission_list = [
                'achievement_access',
                'achievement_create',
                'achievement_create_self_assessment',
                'categorie_access',
                'categorie_show',
                'certificate_access',
                'certificate_show',
                'contactdetail_access',
                'contactdetail_create',
                'contactdetail_show',
                'contactdetail_edit',
                'contactdetail_delete',
                'course_create',
                'course_show',
                'course_edit',
                'course_delete',
                'curriculum_show',
                'curriculum_print',
                'group_access',
                'group_create',
                'group_show',
                'group_edit',
                'group_delete',
                'group_enrolment',
                'kanban_access',
                'kanban_create',
                'kanban_show',
                'kanban_edit',
                'kanban_delete',
                'logbook_access',
                'logbook_create',
                'logbook_show',
                'logbook_edit',
                'logbook_delete',
                'logbook_entry_access',
                'logbook_entry_create',
                'logbook_entry_show',
                'logbook_entry_edit',
                'logbook_entry_delete',
                'navigator_show',
                'organization_show',
                'organization_enrolment',
                'organization_edit_address',
                'user_access',
                'user_create',
                'user_show',
                'user_edit',
                'user_delete',
                'user_reset_password',
                'task_access',
                'task_create',
                'task_show',
                'task_edit',
                'task_delete',
                'message_access',
                'message_create',
                'message_show',
                'message_edit',
                'message_delete',
                'note_access',
                'note_create',
                'note_show',
                'note_edit',
                'note_delete',
                'curriculum_create_for_organization',
                'App\Curriculum_content_create',
                'App\Curriculum_content_edit',
                'App\Curriculum_content_delete',
                'App\LogbookEntry_content_create',
                'App\LogbookEntry_content_edit',
                'App\LogbookEntry_content_delete',
                'App\Task_content_create',
                'App\Task_content_edit',
                'App\Task_content_delete',
                'App\EnablingObjective_content_create',
                'App\EnablingObjective_content_edit',
                'App\EnablingObjective_content_delete',
                'App\TerminalObjective_content_create',
                'App\TerminalObjective_content_edit',
                'App\TerminalObjective_content_delete',
                'App\Glossar_content_create',
                'App\Glossar_content_edit',
                'App\Glossar_content_delete',
            ];

            return in_array($permission->title, $schooladmin_permission_list);
        });
        Role::findOrFail(4)->permissions()->sync($schooladmin_permissions);

        //set teacher permissions
        $teacher_permissions = $admin_permissions->filter(function ($permission) {
            $teacher_permission_list = [
                'achievement_access',
                'achievement_create',
                'achievement_create_self_assessment',
                'categorie_access',
                'categorie_show',
                'certificate_access',
                'certificate_show',
                'contactdetail_access',
                'contactdetail_create',
                'contactdetail_show',
                'contactdetail_edit',
                'contactdetail_delete',
                'content_access',
                'content_create',
                'content_show',
                'content_edit',
                'content_delete',
                'course_create',
                'course_show',
                'course_edit',
                'course_delete',
                'curriculum_show',
                'curriculum_print',
                'group_access',
                'group_show',
                'kanban_access',
                'kanban_create',
                'kanban_show',
                'kanban_edit',
                'kanban_delete',
                'logbook_access',
                'logbook_create',
                'logbook_show',
                'logbook_edit',
                'logbook_delete',
                'logbook_entry_access',
                'logbook_entry_create',
                'logbook_entry_show',
                'logbook_entry_edit',
                'logbook_entry_delete',
                'navigator_show',
                'organization_show',
                'user_access',
                'user_show',
                'user_edit',
                'task_access',
                'task_create',
                'task_show',
                'task_edit',
                'task_delete',
                'message_access',
                'message_create',
                'message_show',
                'message_edit',
                'message_delete',
                'note_access',
                'note_create',
                'note_show',
                'note_edit',
                'note_delete',
                'curriculum_create_for_group',
                'App\Curriculum_content_create',
                'App\Curriculum_content_edit',
                'App\Curriculum_content_delete',
                'App\LogbookEntry_content_create',
                'App\LogbookEntry_content_edit',
                'App\LogbookEntry_content_delete',
                'App\Task_content_create',
                'App\Task_content_edit',
                'App\Task_content_delete',
                'App\EnablingObjective_content_create',
                'App\EnablingObjective_content_edit',
                'App\EnablingObjective_content_delete',
                'App\TerminalObjective_content_create',
                'App\TerminalObjective_content_edit',
                'App\TerminalObjective_content_delete',
                'App\Glossar_content_create',
                'App\Glossar_content_edit',
                'App\Glossar_content_delete',
            ];

            return in_array($permission->title, $teacher_permission_list);
        });
        Role::findOrFail(5)->permissions()->sync($teacher_permissions);

        //set students permissions
        $user_permissions = $admin_permissions->filter(function ($permission) {
            $user_permission_list = [
                'achievement_create',
                'achievement_create_self_assessment',
                'curriculum_show',
                'content_access',
                'content_create',
                'content_show',
                'logbook_access',
                'logbook_create',
                'logbook_show',
                'logbook_edit',
                'logbook_entry_access',
                'logbook_entry_show',
                'navigator_access',
                'navigator_show',
                'task_access',
                'task_show',
                'message_access',
                'message_create',
                'message_show',
                'message_edit',
                'curriculum_create_for_user',
                'App\Curriculum_content_create',
                'App\Curriculum_content_edit',
                'App\Curriculum_content_delete',
                'App\LogbookEntry_content_create',
                'App\LogbookEntry_content_edit',
                'App\LogbookEntry_content_delete',
                'App\Task_content_create',
                'App\Task_content_edit',
                'App\Task_content_delete',
                'App\EnablingObjective_content_create',
                'App\EnablingObjective_content_edit',
                'App\EnablingObjective_content_delete',
                'App\TerminalObjective_content_create',
                'App\TerminalObjective_content_edit',
                'App\TerminalObjective_content_delete',
                'App\Glossar_content_create',
                'App\Glossar_content_edit',
                'App\Glossar_content_delete',
            ];

            return in_array($permission->title, $user_permission_list);
        });
        Role::findOrFail(6)->permissions()->sync($user_permissions);

        //set parents permissions
        $parent_permissions = $admin_permissions->filter(function ($permission) {
            $parent_permission_list = [
                'curriculum_show',
                'logbook_access',
                'logbook_show',
                'logbook_entry_access',
                'logbook_entry_show',
                'navigator_show',
                'task_access',
                'task_show',
                'message_access',
                'message_create',
                'message_show',
                'message_edit',
            ];

            return in_array($permission->title, $parent_permission_list);
        });
        Role::findOrFail(7)->permissions()->sync($parent_permissions);

        //set guest permissions
        $guest_permissions = $admin_permissions->filter(function ($permission) {
            $guest_permission_list = [
                'curriculum_show',
                'navigator_show',
            ];

            return in_array($permission->title, $guest_permission_list);
        });

        Role::findOrFail(8)->permissions()->sync($guest_permissions);

        //set token permission
        $token_permission = $admin_permissions->filter(function ($permission) {
            $token_permission_list = [
                'curriculum_show',
                'kanban_show',
                'kanban_access',
                'kanban_create',
            ];

            return in_array($permission->title, $token_permission_list);
        });

        Role::findOrFail(9)->permissions()->sync($token_permission);
    }
}

<?php

namespace App\Imports;

use App\Group;
use App\OrganizationRoleUser;
use App\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class UsersImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $new_user = new User([
            'common_name' => $row['common_name'] ?? '',
            'username'  => $row['username'],
            'firstname' => $row['firstname'],
            'lastname'  => $row['lastname'],
            'email'     => $row['email'],
            'password'  => Hash::make($row['password']),
            'current_organization_id' => $row['organization_id'],
        ]);
        $new_user->save();

        // enrol to org
        OrganizationRoleUser::firstOrCreate(
            [
                'user_id'         => $new_user->id,
                'organization_id' => $row['organization_id'],
            ],
            [
                'role_id'         => isset($row['role_id']) ? $row['role_id'] : 6, //student
            ]
        );

        // enrol to group
        if (isset($row['group_id'])) {
            $new_user = $this->enrolToGroup($row['group_id'], $new_user->id, isset($row['role_id']) ? $row['role_id'] : 6);
        }

        return $new_user;
    }

    protected function enrolToGroup($group_id, $user, $role_id)
    {
        $group = Group::findOrFail($group_id);

        //if user isn't enrolled to groups organization, enrol with given role |fallback student role
        OrganizationRoleUser::firstOrCreate(
            [
                'user_id'           => $user->id,
                'organization_id'   => $group->first()->organization_id,
            ],
            [
                'role_id'           => $role_id,
            ]
        );

        return $user->groups()->syncWithoutDetaching($group_id);
    }
}

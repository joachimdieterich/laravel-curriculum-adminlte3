<?php

namespace App\Helpers;

use App\Exceptions\TooManyResultsException;
use App\Organization;
use App\Role;
use App\User;
use Laravolt\Avatar\Facade as Avatar;

class SubscribeHelper
{
    const int MAX_RESULT = 10000;

    /**
     * @return array
     *
     * @throws TooManyResultsException
     */
    public static function usersForSubscription(): array
    {
        $input = request()->validate([
            'search' => 'sometimes|string|max:255',
            'no_students' => 'sometimes|bool',
        ]);

        $isAdmin = is_admin();

        $organizations         = $isAdmin
            ? Organization::get(['organizations.id', 'organizations.title as groupLabel'])
            : auth()->user()->organizations()->get(['organizations.id', 'organizations.title as groupLabel']);

        $organizationsWithUser = $organizations
            ->makeHidden(['pivot'])
            ->keyBy('id')
            ->toArray();
        foreach ($organizationsWithUser as &$item) {
            $item['groupOptions'] = [];
        }

        $users = $isAdmin
            ? User::noSharing()
            : User::whereAttachedTo(auth()->user()->organizations);

        // only get users with teacher-role or higher
        if (!$isAdmin && request()->has('no_students')) {                                 // skip this step for admins
            $users = $users->whereNotIn('organization_role_users.role_id', [6, 7, 8, 9]); // student-, parent-, guest-, token-role
        }

        $searchTerm = $input['search'];
        if ($searchTerm) {
            $users->where(  // count all entries FIRST with filter to get pagination working
                function ($query) use ($searchTerm) {
                    foreach (['username', 'firstname', 'lastname'] as $f) {
                        $query->orWhere($f, 'LIKE', '%' . $searchTerm . '%');
                    }
                });
        }

        if($users->count() > self::MAX_RESULT) {
            throw new TooManyResultsException();
        }

        $roleNames = Role::get([
            'id',
            'title',
        ])->mapWithKeys(function (Role $role) {
            return [$role->id => $role->title];
        });

        $users->orderBy('users.lastname')->with('organizations')->lazy()->each(
            function ($user) use (&$organizationsWithUser, $roleNames) {
                foreach ($user->organizations as $organization) {
                    if (array_key_exists($organization->id, $organizationsWithUser)) {
                        $roleId = min($user->organizations->pluck('pivot.role_id')->toArray());

                        $organizationsWithUser[$organization->id]['groupOptions'][] = [
                            'label' => $user->firstname . ' ' . $user->lastname,
                            'value' => [
                                'user' => [
                                    'id' => $user->id,
                                    'firstname' => $user->firstname,
                                    'lastname' => $user->lastname,
                                ],
                                'roleId' => $roleId,
                                'roleName' => trans('global.roles.' . $roleNames[$roleId]),
                                'icon' => Avatar::create($user->fullName())->toBase64(),
                                'organizations' => $user->organizations->pluck('title')->toArray(),
                            ],
                        ];

                        // Assign user only to one organization -> no duplicates in list
                        break;
                    }
                }
            }
        );

        // filter empty organizations and remove index for better json encode
        $result = [];
        foreach ($organizationsWithUser as $organization) {
            if (!empty($organization['groupOptions'])) {
                $result[] = $organization;
            }
        }

        return $result;
    }
}
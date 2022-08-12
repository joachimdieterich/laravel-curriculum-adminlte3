<?php

namespace Tests\Browser;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserTest extends DuskTestCase
{
    /**
     * Add user
     *
     * @return void
     */
    public function testAddUser()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $new_user = User::factory()->raw();
            $browser->loginAs(User::find(1))
                    ->visit(new Pages\UserPage)
                    ->waitForText($user->username)
                    ->assertSee('Add User')
                    ->click('#add-user')
                    ->type('username', $new_user['username'])
                    ->type('firstname', $new_user['firstname'])
                    ->type('lastname', $new_user['lastname'])
                    ->type('email', $new_user['email'])
                    ->type('password', $new_user['password'])
                    ->click('#user-save')
                    ->waitForText($new_user['username'])
                    //->screenshot('see-user-details')
;
        });
    }

    /**
     * Edit user
     *
     * @return void
     */
    public function testShowUser()
    {
        $this->browse(function (Browser $admin) {
            $user = User::find(1)->get()->first();
            $admin->loginAs(User::find(1))
                ->visit(new Pages\UserPage)
                ->waitForText($user->username)
                ->click('#show-user-'.$user->id)
                ->waitForText($user->username)
                ->assertSee($user->firstname)
                ->assertSee($user->lastname)
                ->assertSee($user->email)
                //->screenshot('see-user-details')
;
        });
    }

    /**
     * Edit user
     *
     * @return void
     */
    public function testEditUser()
    {
        $this->browse(function (Browser $admin) {
            $new_user = User::factory()->raw();
            $user = User::find(1)->get()->first();
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\UserPage)
                    ->waitForText($user->email)
                    ->click('#edit-user-'.$user->id)
                    ->waitForText(trans('global.edit').' '.trans('global.user.title_singular'))
                    ->type('username', $user->username.' changed')
                    ->click('#user-save')
                    ->waitForText($user->username.' changed')
                    //->screenshot('see-user')
;
        });
    }

    /**
     * Delete user
     *
     * @return void
     */
    public function testDeleteUser()
    {
        $this->browse(function (Browser $admin) {
            $new_user = User::factory()->raw();
            $user = User::create($new_user);

            $admin->loginAs(User::find(1))
                    ->visit(new Pages\UserPage)
                    ->waitForText($user->username)
                    ->click('#delete-user-'.$user->id)
                    ->waitForText(trans('global.user.title_singular').' '.trans('global.list'))
                    ->assertDontSee($user->username)
                   // ->screenshot('see-user')
;
        });
    }
}

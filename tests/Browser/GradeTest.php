<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Chrome;
use App\Grade;
use App\User;

class GradeTest extends DuskTestCase
{
    
    /**
     * Add grade
     *
     * @return void
     */
    public function testAddGrade()
    {   
        $this->browse(function (Browser $browser) {
            $grade = Grade::first();
            $new_grade = factory('App\Grade')->raw();
            $browser->loginAs(User::find(1))
                    ->visit(new Pages\GradePage)
                    ->waitForText($grade->title)
                    ->assertSee('Add Grade')
                    ->click('#add-grade')
                    ->type('title', $new_grade['title'])
                    ->type('external_begin', $new_grade['external_begin'])
                    ->type('external_end', $new_grade['external_end'])
                    ->select2('#organization_type_id', 'Grundschule - Primarstufe')
                    ->click('#grade-save')
                    ->waitForText($new_grade['title'])
                    //->screenshot('see-grade-details')
                    ;
        });     
    }
    
    /**
     * Edit grade
     *
     * @return void
     */
    public function testShowGrade()
    {   
        $this->browse(function (Browser $admin) {
            
        $grade = Grade::find(1)->get()->first();
        $admin->loginAs(User::find(1))
                ->visit(new Pages\GradePage)
                ->waitForText($grade->title)
                ->click('#show-grade-'.$grade->id)
                ->screenshot('see-gradeal-details')
                ->waitForText($grade->title)
                ->assertSee($grade->external_begin)
                ->assertSee($grade->external_end)
                ->screenshot('see-grade-details')
                ;
        });     
    }
    /**
     * Edit grade
     *
     * @return void
     */
    public function testEditGrade()
    {   
        $this->browse(function (Browser $admin) {
            $new_grade = factory('App\Grade')->raw();
            $grade = Grade::find(1)->get()->first();
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\GradePage)
                    ->waitForText($grade->title)
                    ->click('#edit-grade-'.$grade->id)
                    ->waitForText( trans('global.edit').' '. trans('global.grade.title_singular') )
                    ->type('title', $grade->title.' changed')
                    ->click('#grade-save')
                    ->waitForText($grade->title.' changed')
                    //->screenshot('see-grade')
                    ;
        });     
    }
    
    /**
     * Delete grade
     *
     * @return void
     */
//    public function testDeleteGrade()
//    {   
//        $this->browse(function (Browser $admin) {
//            $new_grade = factory('App\Grade')->raw();
//            $grade = Grade::create($new_grade);
//            
//            $admin->loginAs(User::find(1))
//                    ->visit(new Pages\GradePage)
//                    ->waitForText($grade->title)
//                    ->click('#delete-grade-'.$grade->id)
//                    ->waitForText( trans('global.grade.title_singular').' '. trans('global.list') )
//                    ->assertDontSee($grade->title)
//                   // ->screenshot('see-grade')
//                    ;
//        });     
//    }
   
}

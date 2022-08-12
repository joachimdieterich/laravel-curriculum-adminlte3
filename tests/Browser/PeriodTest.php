<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PeriodTest extends DuskTestCase
{
    /**
     * Add period
     *
     * @return void
     */
    public function testAddPeriod()
    {
        $this->browse(function (Browser $browser) {
            $period = Period::first();
            $new_period = Period::factory()->raw();
            $browser->loginAs(User::find(1))
                    ->visit(new Pages\PeriodPage)
                    ->waitForText($period->title)
                    ->assertSee('Add Period')
                    ->click('#add-period')
                    ->type('title', $new_period['title'])
                    ->type('begin', $new_period['begin'])
                    ->type('end', $new_period['end'])
                    ->select2('#organization_id', 'curriculumonline')
                    ->click('#period-save')
                    ->waitForText($new_period['title'])
                    //->screenshot('see-periodal-details')
;
        });
    }

    /**
     * Edit period
     *
     * @return void
     */
    public function testShowPeriod()
    {
        $this->browse(function (Browser $admin) {
            $period = Period::find(1)->get()->first();
//       dd($period);
            $admin->loginAs(User::find(1))
                ->visit(new Pages\PeriodPage)
                ->waitForText($period->title)
                ->click('#show-period-'.$period->id)
                //->screenshot('see-periodal-details')
                ->waitForText($period->title)
                ->assertSee($period->begin)
                ->assertSee($period->end)
                //->screenshot('see-periodal-details')
;
        });
    }

    /**
     * Edit period
     *
     * @return void
     */
    public function testEditPeriod()
    {
        $this->browse(function (Browser $admin) {
            $new_period = Period::factory()->raw();
            $period = Period::create($new_period);
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\PeriodPage)
                    ->waitForText($period->title)
                    ->click('#edit-period-'.$period->id)
                    ->waitForText(trans('global.edit').' '.trans('global.period.title_singular'))
                    ->type('title', $period->title.' changed')
                    ->click('#period-save')
                    ->waitForText($period->title.' changed')
                    //->screenshot('see-period')
;
        });
    }

    /**
     * Delete period
     *
     * @return void
     */
    public function testDeletePeriod()
    {
        $this->browse(function (Browser $admin) {
            $new_period = Period::factory()->raw();
            $period = Period::create($new_period);
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\PeriodPage)
                    ->waitForText($period->title)
                    ->click('#delete-period-'.$period->id)
                    ->waitForText('curriculumonline') //proof that datatable is loaded, curriculumonline is the seeded demo period
                    ->assertDontSee($period->title)
                   // ->screenshot('see-period')
;
        });
    }
}

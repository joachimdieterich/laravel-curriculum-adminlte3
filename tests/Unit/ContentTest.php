<?php

namespace Tests\Unit;

use App\Content;
use App\ContentSubscription;
use App\Curriculum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInRole('admin');
    }

    /** @test */
    public function it_has_a_owner()
    {
        $content = Content::factory()->create();

        $this->assertInstanceOf('App\User', $content->owner);
    }

    /** @test */
    public function a_content_can_have_a_subscriptions()
    {
        ///$this->withoutExceptionHandling();

        $content = Content::factory()->create();

        $curriculum = new Curriculum;

        $subscription = ContentSubscription::create($attributes = [
            'content_id' => $content->id,

            'subscribable_type' => get_class($curriculum),
            'subscribable_id' => 1,
            'sharing_level_id' => 1,

            'visibility' => true,
            'owner_id' => 1,
        ]);

        $c = Content::findOrFail($content->id);

        $this->assertInstanceOf('App\ContentSubscription', $c->subscriptions()->first());
    }
}

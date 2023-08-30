<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            //for full properties  see https://fullcalendar.io/docs/event-object
            $table->id();
            $table->string('groupId')->nullable();

            $table->boolean('allDay')->default(false);
            $table->timestamp('start');
            $table->timestamp('end')->nullable();
            $table->string('startStr');
            $table->string('endStr');

            $table->json('daysOfWeek')->nullable();
            $table->timestamp('endRecur')->nullable();

            $table->string('title');
            $table->string('url')->nullable();
            $table->boolean('interactive')->nullable()->default(true);

            $table->json('classNames')->nullable();
            $table->boolean('editable')->nullable();

            $table->string('display')->default('auto'); // The rendering type of this event. Can be 'auto', 'block', 'list-item', 'background', 'inverse-background', or 'none'.

            $table->string('backgroundColor')->nullable();
            $table->string('borderColor')->nullable();
            $table->string('textColor')->nullable();
            $table->json('extendedProps')->nullable();


            $table->boolean('overlap')->default(true);

            $table->string('source')->nullable()->default(null); //e.g. users/22


            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_events');
    }
};

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
        Schema::table('videoconferences', function (Blueprint $table) {
            $table->text('welcomeMessage')->nullable();
            $table->string('dialNumber')->nullable();
            $table->unsignedInteger('maxParticipants')->default(0);
            $table->string('logoutUrl')->nullable();
            $table->boolean('record')->default(false);
            $table->unsignedInteger('duration')->default(0);
            $table->boolean('isBreakout')->default(false);
            $table->text('moderatorOnlyMessage')->nullable();
            $table->boolean('autoStartRecording')->default(false);
            $table->boolean('allowStartStopRecording')->default(true);
            $table->text('bannerText')->nullable();
            $table->char('bannerColor', 9)->default('#FFFFFF');
            $table->text('logo')->nullable();
            $table->text('copyright')->nullable();
            $table->boolean('muteOnStart')->default(false);
            $table->boolean('allowModsToUnmuteUsers')->default(false);
            $table->boolean('lockSettingsDisableCam')->default(false);
            $table->boolean('lockSettingsDisableMic')->default(false);
            $table->boolean('lockSettingsDisablePrivateChat')->default(false);
            $table->boolean('lockSettingsDisablePublicChat')->default(false);
            $table->boolean('lockSettingsDisableNote')->default(false);
            $table->boolean('lockSettingsLockedLayout')->default(false);
            $table->boolean('lockSettingsLockOnJoin')->default(false);
            $table->boolean('lockSettingsLockOnJoinConfigurable')->default(false);
            $table->string('guestPolicy')->default('ALWAYS_ACCEPT'); //ALWAYS_ACCEPT, ALWAYS_DENY, and ASK_MODERATOR.
            $table->boolean('meetingKeepEvents')->default(false);
            $table->boolean('endWhenNoModerator')->default(false);
            $table->unsignedInteger('endWhenNoModeratorDelayInMinutes')->default(1);
            $table->string('meetingLayout')->default('SMART_LAYOUT'); //CUSTOM_LAYOUT, SMART_LAYOUT, PRESENTATION_FOCUS, VIDEO_FOCUS. (added 2.4)
            $table->unsignedInteger('learningDashboardCleanupDelayInMinutes')->default(2);
            $table->boolean('allowModsToEjectCameras')->default(false);
            $table->boolean('allowRequestsWithoutSession')->default(false);
            $table->unsignedInteger('userCameraCap')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videoconferences', function (Blueprint $table) {
            $table->dropColumn('welcomeMessage');
            $table->dropColumn('dialNumber');
            $table->dropColumn('maxParticipants');
            $table->dropColumn('logoutUrl');
            $table->dropColumn('record');
            $table->dropColumn('duration');
            $table->dropColumn('isBreakout');
            $table->dropColumn('moderatorOnlyMessage');
            $table->dropColumn('autoStartRecording');
            $table->dropColumn('allowStartStopRecording');
            $table->dropColumn('bannerText');
            $table->dropColumn('bannerColor');
            $table->dropColumn('copyright');
            $table->dropColumn('muteOnStart');
            $table->dropColumn('allowModsToUnmuteUsers');
            $table->dropColumn('lockSettingsDisableCam');
            $table->dropColumn('lockSettingsDisableMic');
            $table->dropColumn('lockSettingsDisablePrivateChat');
            $table->dropColumn('lockSettingsDisablePublicChat');
            $table->dropColumn('lockSettingsDisableNote');
            $table->dropColumn('lockSettingsLockedLayout');
            $table->dropColumn('lockSettingsLockOnJoin');
            $table->dropColumn('lockSettingsLockOnJoinConfigurable');
            $table->dropColumn('guestPolicy');
            $table->dropColumn('meetingKeepEvents');
            $table->dropColumn('endWhenNoModerator');
            $table->dropColumn('endWhenNoModeratorDelayInMinutes');
            $table->dropColumn('meetingLayout');
            $table->dropColumn('learningDashboardCleanupDelayInMinutes');
            $table->dropColumn('allowModsToEjectCameras');
            $table->dropColumn('allowRequestsWithoutSession');
            $table->dropColumn('userCameraCap');
        });
    }
};

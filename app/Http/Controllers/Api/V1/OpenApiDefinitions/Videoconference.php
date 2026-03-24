<?php

namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
 * @OA\Get(
 *      path="/v1/videoconferences/links",
 *      operationId="getVideoconferenceLinks",
 *      tags={"videoconference"},
 *      description="Get moderator/attendee links of videoconference",
 *      security={
 *           {"bearerAuth": {}},
 *      },
 *      @OA\Parameter(
 *          name="common_name",
 *          in="query",
 *          description="Common name, that tries to get the links",
 *          required=true,
 *          @OA\Schema(type="string", format="uuid")
 *      ),
 *      @OA\Parameter(
 *          name="meetingID",
 *          in="query",
 *          description="Got at creation of videoconference",
 *          required=true,
 *          @OA\Schema(type="string", format="uuid")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful",
 *          @OA\JsonContent(
 *              @OA\Property(property="moderatorLink", type="string", format="uri", example="https://curriculum.schulcampus-rlp.de/videoconferences/1/startWithPw?moderatorPW=%242y%2410%24zMWK9t3qprEApDSHcM3vl.yDWc8PAQDjB0C3IZ1.KgtRqC4QK3HBq"),
 *              @OA\Property(property="attendeeLink", type="string", format="uri", example="https://curriculum.schulcampus-rlp.de/videoconferences/1/startWithPw?attendeePw=%242y%2410%24snhUwqSRNLBaDVfW4Ivdfu1EH9kFQTjBAyv1cpFnZwmg8IQovFJx2")
 *          )
 *       ),
 *      @OA\Response(response=403, description="Missing rights"),
 *      @OA\Response(response=422, description="Missing/Invalid information"),
 *      @OA\Response(response=500, description="Internal Sever Error"),
 *  )
 *
 * @OA\Post(
 *      path="/v1/videoconferences",
 *      operationId="createVideoconference",
 *      tags={"videoconference"},
 *      summary="Create new videoconference",
 *      description="Returns the new videoconference object",
 *      security={
 *           {"bearerAuth": {}},
 *      },
 *      @OA\Parameter(
 *          name="common_name",
 *          in="query",
 *          description="Common name, that tries to create a videoconference",
 *          required=true,
 *          @OA\Schema(type="string", format="uuid")
 *      ),
 *      @OA\RequestBody(
 *          description="New videoconference",
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  type="object",
 *                  required={"meetingName"},
 *                  @OA\Property( property="id", type="integer", nullable=true),
 *                  @OA\Property( property="sharing_token", type="integer", nullable=true),
 *                  @OA\Property( property="meetingID", type="string", format="uuid"),
 *                  @OA\Property( property="meetingName", type="string"),
 *                  @OA\Property( property="attendeePW", type="string"),
 *                  @OA\Property( property="moderatorPW", type="string"),
 *                  @OA\Property( property="endCallbackUrl", type="string", format="uri"),
 *                  @OA\Property( property="welcomeMessage", type="string", nullable=true),
 *                  @OA\Property( property="dialNumber", type="string"),
 *                  @OA\Property( property="maxParticipants", type="string"),
 *                  @OA\Property( property="logoutUrl", type="string", nullable=true),
 *                  @OA\Property( property="record", type="boolean"),
 *                  @OA\Property( property="duration", type="integer"),
 *                  @OA\Property( property="isBreakout", type="boolean"),
 *                  @OA\Property( property="moderatorOnlyMessage", type="string", nullable=true),
 *                  @OA\Property( property="autoStartRecording", type="boolean"),
 *                  @OA\Property( property="allowStartStopRecording", type="boolean"),
 *                  @OA\Property( property="bannerText", type="string", nullable=true),
 *                  @OA\Property( property="bannerColor", type="string"),
 *                  @OA\Property( property="logo", type="string", format="uri", nullable=true),
 *                  @OA\Property( property="copyright", type="string", nullable=true),
 *                  @OA\Property( property="muteOnStart", type="boolean"),
 *                  @OA\Property( property="allowModsToUnmuteUsers", type="boolean"),
 *                  @OA\Property( property="lockSettingsDisableCam", type="boolean"),
 *                  @OA\Property( property="lockSettingsDisableMic", type="boolean"),
 *                  @OA\Property( property="lockSettingsDisablePrivateChat", type="boolean"),
 *                  @OA\Property( property="lockSettingsDisablePublicChat", type="boolean"),
 *                  @OA\Property( property="lockSettingsDisableNote", type="boolean"),
 *                  @OA\Property( property="lockSettingsLockedLayout", type="boolean"),
 *                  @OA\Property( property="lockSettingsLockOnJoin", type="boolean"),
 *                  @OA\Property( property="lockSettingsLockOnJoinConfigurable", type="boolean"),
 *                  @OA\Property( property="guestPolicy", enum={"ALWAYS_ACCEPT", "ALWAYS_DENY", "ASK_MODERATOR"}),
 *                  @OA\Property( property="meetingLayout", enum={"CUSTOM_LAYOUT", "SMART_LAYOUT", "PRESENTATION_FOCUS", "VIDEO_FOCUS"}),
 *                  @OA\Property( property="meetingKeepEvents", type="boolean"),
 *                  @OA\Property( property="endWhenNoModerator", type="boolean"),
 *                  @OA\Property( property="endWhenNoModeratorDelayInMinutes", type="integer"),
 *                  @OA\Property( property="learningDashboardCleanupDelayInMinutes", type="integer"),
 *                  @OA\Property( property="allowModsToEjectCameras", type="boolean"),
 *                  @OA\Property( property="allowRequestsWithoutSession", type="boolean"),
 *                  @OA\Property( property="allJoinAsModerator", type="boolean"),
 *                  @OA\Property( property="userCameraCap", type="integer"),
 *                  @OA\Property( property="subscribable_type", type="string", nullable=true),
 *                  @OA\Property( property="subscribable_id", type="integer", nullable=true),
 *                  @OA\Property( property="medium_id", type="integer", nullable=true),
 *                  @OA\Property( property="webcamsOnlyForModerator", type="boolean"),
 *                  @OA\Property( property="anyoneCanStart", type="boolean"),
 *                  @OA\Property( property="server", type="string"),
 *                  @OA\Property( property="owner_id", type="integer", nullable=true),
 *            )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="successful created",
 *          @OA\JsonContent(ref="#/components/schemas/Videoconference"),
 *       ),
 *      @OA\Response(response=403, description="Missing rights"),
 *      @OA\Response(response=422, description="Missing/Invalid information"),
 *      @OA\Response(response=500, description="Internal Sever Error"),
 *  )
 */
class Videoconference {}
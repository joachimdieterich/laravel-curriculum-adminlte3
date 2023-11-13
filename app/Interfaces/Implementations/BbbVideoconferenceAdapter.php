<?php

namespace App\Interfaces\Implementations;

use App\Interfaces\VideoconferenceInterface;

class BbbVideoconferenceAdapter implements VideoconferenceInterface
{


    public function index(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->all();
    }

    /**
     * create meeting
     */
    public function create(array $input)
    {
        abort_unless(\Gate::allows('videoconference_create'), 403);

        return \Bigbluebutton::server( $input['server'] )->create([
            'meetingID'     => $input['meetingID'],
            'meetingName'   => $input['meetingName'],
            'attendeePW'    => $input['attendeePW'],
            'moderatorPW'   => $input['moderatorPW'],
            'presentation'  => $input['presentation'] ?? [],
            /*[ //must be array
                ['link' => 'https://www.example.com/doc.pdf', 'fileName' => 'doc.pdf'], //first will be default and current slide in meeting
                ['link' => 'https://www.example.com/php_tutorial.pptx', 'fileName' => 'php_tutorial.pptx'],
            ],*/
            'endCallbackUrl'  => $input['endCallbackUrl'] ?? env('APP_URL') ,
            'logoutUrl' => $input['logoutUrl'] ?? env('APP_URL'),
        ]);
    }

    public function initCreateMeeting(array $input)
    {
        $createMeeting = \Bigbluebutton::server( $input['server'] )->initCreateMeeting([
            'meetingID'     => $input['meetingID'],
            'meetingName'   => $input['meetingName'],
            'attendeePW'    => $input['attendeePW'],
            'moderatorPW'   => $input['moderatorPW'],
        ]);

        $createMeeting->setDuration(100); //overwrite default configuration

        return \Bigbluebutton::server( $input['server'] )->create($createMeeting);
    }

    public function start(array $input){

        $url = \Bigbluebutton::server( $input['server'] )->start($input);

       /* $url = \Bigbluebutton::server( $input['server'] )->start([
            'meetingID' => $input['meetingID'],
            'meetingName' => $input['meetingName'],
            'moderatorPW' => $input['moderatorPW'],
            'attendeePW' => $input['attendeePW'],
            'userName' => $input['userName'],
            'presentation'  => $input['presentation'] ?? [],
            'bannerColor'  => '#E9F476',
            'welcomeMessage' => 'Hello BBB',

            //'redirect' => false // only want to create and meeting and get join url then use this parameter
        ]);*/

        return redirect()->to($url);
    }

    public function join(array $input)
    {
        return redirect()->to(
            \JoisarJignesh\Bigbluebutton\Facades\Bigbluebutton::server( $input['server'] )->join([
                'meetingID' => $input['meetingID'],
                'userName' =>  $input['userName'],
                'password' =>  $input['password'],
            ])
        );
    }

    /**
     * Close Videoconference

     */
    public function close(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->close([
            'meetingID' => $input['meetingID'],
            'moderatorPW' => $input['moderatorPW'],
        ]);

    }

    /**
     * returns information about the meeting.
     * @params array (meetingID,moderatorPW)
     *
     */
    public function getMeetingInfo(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->getMeetingInfo($input['meetingID']);
    }

    /**
     * This call enables you to simply check on whether or not a meeting is running by looking it up with your meeting ID.
     *
     */
    public function isMeetingRunning(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->isMeetingRunning($input['meetingID']);
    }

    public function getRecordings(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->getRecordings([
            'meetingID' => $input['meetingID'],
            //'meetingID' => ['tamku','xyz'], //pass as array if get multiple recordings
            //'recordID' => 'a3f1s',
            //'recordID' => ['xyz.1','pqr.1'] //pass as array note :If a recordID is specified, the meetingID is ignored.
            // 'state' => 'any' // It can be a set of states separate by commas
        ]);
    }

    public function publishRecordings(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->getRecordings([
            'recordID' => $input['recordID'],
            //'recordID' => ['xyz.1','pqr.1'] //pass as array if publish multiple recordings
            'state' => $input['state'] ?? true //default is true
        ]);
    }

    public function deleteRecordings(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->deleteRecordings([
            'recordID' => $input['recordID'],
            //'recordID' => ['xyz.1','pqr.1'] //pass as array if publish multiple recordings
        ]);
    }

    public function hooksCreate(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->hooksCreate([
            'callbackURL' => $input['endCallbackUrl'], //required //check: callbackURL or endCallbackUrl?
            'meetingID' => $input['meetingID'], //optional  if not set then hooks set for all meeting id
            'getRaw' => $input['getRaw'] ?? true //optional
        ]);
    }

    public function hooksDestroy(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->hooksDestroy([
            'hooksID' => $input['hooksID'],

        ]);
    }

    public function isConnect(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->isConnect();
    }

    public function getApiVersion(array $input)
    {
        return \Bigbluebutton::server( $input['server'] )->getApiVersion();
    }

}

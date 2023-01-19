<?php

namespace App\Interfaces\Implementations;

use App\Interfaces\VideoconferenceInterface;

class BbbVideoconferenceAdapter implements VideoconferenceInterface
{

    public function index()
    {
        return \Bigbluebutton::all();
    }

    /**
     * create meeting
     */
    public function create(array $input)
    {
        abort_unless(\Gate::allows('videoconference_create'), 403);

        return \Bigbluebutton::create([
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
        $createMeeting = \Bigbluebutton::initCreateMeeting([
            'meetingID'     => $input['meetingID'],
            'meetingName'   => $input['meetingName'],
            'attendeePW'    => $input['attendeePW'],
            'moderatorPW'   => $input['moderatorPW'],
        ]);

        $createMeeting->setDuration(100); //overwrite default configuration

        return \Bigbluebutton::create($createMeeting);
    }

    public function start(array $input){

        $url = \Bigbluebutton::start([
            'meetingID' => $input['meetingID'],
            'meetingName' => $input['meetingName'],
            'moderatorPW' => $input['moderatorPW'],
            'attendeePW' => $input['attendeePW'],
            'userName' => $input['userName'],
            //'redirect' => false // only want to create and meeting and get join url then use this parameter
        ]);

        return redirect()->to($url);
    }

    public function join(array $input)
    {

        return redirect()->to(
            \JoisarJignesh\Bigbluebutton\Facades\Bigbluebutton::join([
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
        return \Bigbluebutton::close([
            'meetingID' => $input['meetingID'],
            'moderatorPW' => $input['moderatorPW'],
        ]);

    }

    /**
     * This call enables you to simply check on whether or not a videoconference is running by looking it up with your meeting ID.
     *
     */
    public function getMeetingInfo(array $array)
    {
        return \Bigbluebutton::getMeetingInfo($array);
    }

    /**
     * This call enables you to simply check on whether or not a meeting is running by looking it up with your meeting ID.
     *
     */
    public function isMeetingRunning(array $input)
    {
        return \Bigbluebutton::isMeetingRunning($input);
    }

    public function getRecordings(array $input)
    {
        return \Bigbluebutton::getRecordings([
            'meetingID' => $input['meetingID'],
            //'meetingID' => ['tamku','xyz'], //pass as array if get multiple recordings
            //'recordID' => 'a3f1s',
            //'recordID' => ['xyz.1','pqr.1'] //pass as array note :If a recordID is specified, the meetingID is ignored.
            // 'state' => 'any' // It can be a set of states separate by commas
        ]);
    }

    public function publishRecordings(array $input)
    {
        return \Bigbluebutton::getRecordings([
            'recordID' => $input['recordID'],
            //'recordID' => ['xyz.1','pqr.1'] //pass as array if publish multiple recordings
            'state' => $input['state'] ?? true //default is true
        ]);
    }

    public function deleteRecordings(array $input)
    {
        return \Bigbluebutton::deleteRecordings([
            'recordID' => $input['recordID'],
            //'recordID' => ['xyz.1','pqr.1'] //pass as array if publish multiple recordings
        ]);
    }

    public function hooksCreate(array $input)
    {
        return \Bigbluebutton::hooksCreate([
            'callbackURL' => $input['callbackURL'], //required
            'meetingID' => $input['meetingID'], //optional  if not set then hooks set for all meeting id
            'getRaw' => $input['getRaw'] ?? true //optional
        ]);
    }

    public function hooksDestroy(array $input)
    {
        return \Bigbluebutton::hooksDestroy([
            'hooksID' => $input['hooksID'],

        ]);
    }

    public function isConnect()
    {
        return \Bigbluebutton::isConnect();
    }

    public function getApiVersion()
    {
        return \Bigbluebutton::getApiVersion();
    }

}

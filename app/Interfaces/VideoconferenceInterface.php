<?php

namespace App\Interfaces;


use Illuminate\Http\Request;

interface VideoconferenceInterface
{
    public function index();

    public function create(array $array);
    public function initCreateMeeting(array $array);

    public function start(array $array);
    public function join(array $array);
    public function close(array $array);

    public function getMeetingInfo(array $array);
    public function isMeetingRunning(array $array);

    public function getRecordings(array $array);
    public function publishRecordings(array $array);
    public function deleteRecordings(array $array);

    public function hooksCreate(array $array);
    public function hooksDestroy(array $array);
    public function isConnect();

    public function getApiVersion();
}

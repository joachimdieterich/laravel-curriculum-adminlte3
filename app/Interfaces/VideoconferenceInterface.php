<?php

namespace App\Interfaces;


use Illuminate\Http\Request;

interface VideoconferenceInterface
{
    public function index(array $input);

    public function create(array $input);
    public function initCreateMeeting(array $input);

    public function start(array $input);
    public function join(array $input);
    public function close(array $input);

    public function getMeetingInfo(array $input);
    public function isMeetingRunning(array $input);

    public function getRecordings(array $input);
    public function publishRecordings(array $input);
    public function deleteRecordings(array $input);

    public function hooksCreate(array $input);
    public function hooksDestroy(array $input);
    public function isConnect(array $input);

    public function getApiVersion(array $input);
}

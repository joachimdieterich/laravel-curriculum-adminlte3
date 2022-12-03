<?php

namespace App\Domains\Exams\Jobs\IleaPlusJobs;

use App\User;

class getStudentsDataJob
{
    public static function toArray($examId): array
    {
        $students = User::whereHas('exams', function ($query) use ($examId) {
            return $query->where('id', '=', $examId);
        })->get();

        $data = [];
        foreach ($students as $student) {
            $login_data = json_decode($student->exams->where('id', '=', $examId)->first()->pivot->login_data);

            $data[] = [
                'id' => $login_data->ilea_id,
                'tanCode' => $login_data->tan,
                'name' => $student->firstname,
                'vorname' => $student->lastname,
            ];
        }

        return $data;
    }

    public static function toJson(array $students)
    {
        $data = [];
        foreach ($students as $student) {
            $data[$student['id']]['login_data'] = json_encode(['tan' => $student['tan'], 'ilea_id' => $student['ilea_id']]);
        }
        return $data;
    }
}

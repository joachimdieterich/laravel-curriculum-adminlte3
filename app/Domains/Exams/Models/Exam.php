<?php

namespace App\Domains\Exams\Models;

use App\Group;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Exam extends Model
{
    use LaravelVueDatatableTrait;

    protected $table = 'exams';

    protected $fillable =
        [
            'id',
            'tool',
            'exam_id',
            'test_name',
            'subject',
            'status',
            'school_key',
            'tutorial_key',
            'group_id',
            'created_by',
            'updated_by'
        ];

    protected $casts =
        [
            'id' => 'integer',
            'tool' => 'string',
            'test_booklet' => 'string',
            'subject' => 'string',
            'school_key' => 'string',
            'tutorial_key' => 'string',
            'status' => 'integer',
            'group_id' => 'integer',
            'created_by' => 'integer',
            'created_at' => 'date:Y-m-d',
            'updated_at' => 'date:Y-m-d'
        ];

    protected $dataTableColumns = [
        'id' => [
            'searchable' => false,
        ],
        'tool' => [
            'searchable' => true,
        ],
        'exam_id' => [
            'searchable' => false,
        ],
        'test_name' => [
            'searchable' => true,
        ],
        'subject' => [
            'searchable' => false,
        ],
        'status' => [
            'searchable' => false,
        ],
        'created_at' => [
            'searchable' => false,
        ]
    ];

    public function getRouteKeyName()
    {
        return 'exam_id';
    }

    protected $dataTableRelationships = [
        "belongsTo" => [
            "options" => [
                "model" => Group::class,
                "foreign_key" => "group_id",
                "columns" => [
                    "group_id"
                ]
            ]
        ],
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class,
            'exam_user',
            'exam_id',
            'user_id')
            ->withPivot(['login_data', 'exam_started', 'exam_completed_at']);
    }

    public function created_by()
    {
        return $this->hasOne(User::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimeString($value)->format('d.m.Y');
    }

}

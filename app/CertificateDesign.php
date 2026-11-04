<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** User-owned graphical designs are separate from legacy HTML certificates. */
class CertificateDesign extends Model
{
    protected $fillable = ['title', 'curriculum_id', 'organization_id', 'owner_id', 'document', 'revision'];

    protected $casts = ['document' => 'object', 'revision' => 'integer'];
}

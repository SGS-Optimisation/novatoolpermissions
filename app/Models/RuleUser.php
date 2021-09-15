<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RuleUser extends Pivot
{
    public $timestamps = true;

    protected $casts = [
        'metadata' => 'array',
    ];
}

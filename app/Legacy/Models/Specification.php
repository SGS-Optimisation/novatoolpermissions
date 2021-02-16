<?php

namespace App\Legacy\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Specification extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mongodb';
}

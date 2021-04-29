<?php

namespace App\Legacy\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class Specification
 * @package App\Legacy\Models
 * @mixin \Eloquent
 */
class Specification extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mongodb';
}

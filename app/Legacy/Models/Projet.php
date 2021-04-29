<?php

namespace App\Legacy\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class Projet
 * @package App\Legacy\Models
 * @mixin \Eloquent
 */
class Projet extends Model
{

    /**
     * @var string
     */
    protected $connection = 'mongodb';

}

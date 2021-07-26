<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;


/**
 * App\Models\RuleTerm
 *
 * @property int $id
 * @property int $taxonomy_id
 */
class RuleTerm extends Model implements Recordable
{
use \Altek\Accountant\Recordable, \Altek\Eventually\Eventually;
    protected $table = 'rule_term';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $incrementing = true;
    public $timestamps = false;

    protected $recordableEvents = [
        'created',
        'updated',
        'restored',
        'deleted',
        'sync',
        'forceDeleted',
        'existingPivotUpdated',
        'attached',
        'detached',
    ];




}

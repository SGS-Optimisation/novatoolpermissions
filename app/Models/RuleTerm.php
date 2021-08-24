<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;


/**
 * App\Models\RuleTerm
 *
 * @property int $id
 * @property int $taxonomy_id
 * @property int $rule_id
 * @property int $term_id
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTerm whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTerm whereTermId($value)
 * @mixin \Eloquent
 */
class RuleTerm extends Pivot // implements Recordable
{
    //use \Altek\Accountant\Recordable, \Altek\Eventually\Eventually;

    protected $table = 'rule_term';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $incrementing = true;
    public $timestamps = false;

    /*protected $recordableEvents = [
        'created',
        'updated',
        'restored',
        'deleted',
        'synced',
        'existingPivotUpdated',
        'attached',
        'detached',
        'forceDeleted',
    ];*/


}

<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * App\Models\Term
 *
 * @property int $id
 * @property int $taxonomy_id
 * @property string $name
 * @property array $config
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientAccount[] $client_accounts
 * @property-read int|null $client_accounts_count
 * @property-read \App\Models\Taxonomy $taxonomy
 * @method static \Illuminate\Database\Eloquent\Builder|Term newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Term newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Term query()
 * @method static \Illuminate\Database\Eloquent\Builder|Term whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Term whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Term whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Term whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Term whereTaxonomyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Term whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rule[] $rules
 * @property-read int|null $rules_count
 * @method static \Illuminate\Database\Eloquent\Builder|Term whereDeletedAt($value)
 * @method static \Database\Factories\TermFactory factory(...$parameters)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Altek\Accountant\Models\Ledger[] $ledgers
 * @property-read int|null $ledgers_count
 * @method static \Illuminate\Database\Query\Builder|Term onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|Term withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Term withoutTrashed()
 */
class Term extends Model implements Recordable
{
    use HasFactory, SoftDeletes, \Altek\Accountant\Recordable, \Altek\Eventually\Eventually;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'taxonomy_id' => 'integer',
        'config' => 'array',
    ];

    protected $recordableEvents = [
        'created',
        'updated',
        'restored',
        'synced',
        'deleted',
        'forceDeleted',
        'existingPivotUpdated',
        'attached',
        'detached',
    ];
    protected $with = ['taxonomy'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonomy()
    {
        return $this->belongsTo(\App\Models\Taxonomy::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function client_accounts()
    {
        return $this->belongsToMany(\App\Models\ClientAccount::class);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rules()
    {
        return $this->belongsToMany(\App\Models\Rule::class)->using(RuleTerm::class);
    }

    public function getNameAttribute()
    {
        return trim($this->attributes['name']);
    }
}

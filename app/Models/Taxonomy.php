<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Taxonomy
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property string $config
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientAccount[] $clientAccounts
 * @property-read int|null $client_accounts_count
 * @property-read Taxonomy|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|Taxonomy[] $taxonomies
 * @property-read int|null $taxonomies_count
 * @property-read Taxonomy $taxonomy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $terms
 * @property-read int|null $terms_count
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy newQuery()
 * @method static \Illuminate\Database\Query\Builder|Taxonomy onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy query()
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taxonomy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Taxonomy withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Taxonomy withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientAccount[] $client_accounts
 * @property-read \App\Models\FieldMapping|null $mapping
 */
class Taxonomy extends Model
{
    use HasFactory, SoftDeletes;

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
        'parent_id' => 'integer',
        'config' => 'array',
    ];

    protected $with = ['parent'];

    protected $appends = ['requiresMapping'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(\App\Models\Taxonomy::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonomies()
    {
        return $this->hasMany(\App\Models\Taxonomy::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function terms()
    {
        return $this->hasMany(\App\Models\Term::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function client_accounts()
    {
        return $this->belongsToMany(\App\Models\ClientAccount::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mapping()
    {
        return $this->hasOne(FieldMapping::class);
    }

    public function mappings()
    {
        return $this->hasMany(FieldMapping::class)->orderBy('sort_order');
    }

    public function getRequiresMappingAttribute()
    {
        return $this->parent && $this->parent->name == 'Account Structure';
    }
}

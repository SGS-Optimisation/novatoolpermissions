<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Altek\Accountant\Contracts\Recordable;



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
 * @property-read mixed $requires_mapping
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FieldMapping[] $mappings
 * @property-read int|null $mappings_count
 * @method static \Database\Factories\TaxonomyFactory factory(...$parameters)
 * @method static Builder|Taxonomy children()
 * @property-read \Illuminate\Database\Eloquent\Collection|\Altek\Accountant\Models\Ledger[] $ledgers
 * @property-read int|null $ledgers_count
 */
class Taxonomy extends Model implements Recordable
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
        'parent_id' => 'integer',
        'config' => 'array',
    ];
    protected $recordableEvents = [
        'created',
        'updated',
        'restored',
        'deleted',
        'forceDeleted',
        'existingPivotUpdated',
        'attached',
        'detached',
    ];
    protected $with = ['parent'];

    protected $appends = ['requiresMapping'];

    public function scopeChildren(Builder $query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeAccountStructure(Builder $query)
    {
        return $query->children()->whereHas('parent', function($subquery) {
            return $subquery->where('name', 'Account Structure');
        });
    }


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

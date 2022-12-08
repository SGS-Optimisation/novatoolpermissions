<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\ClientAccount
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $alias
 * @property string $jobteam
 * @property string $image
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taxonomy[] $taxonomies
 * @property-read int|null $taxonomies_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $terms
 * @property-read int|null $terms_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereLegacyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $legacy_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rule[] $rules
 * @property-read int|null $rules_count
 * @property-read \App\Models\Team|null $team
 * @method static \Database\Factories\ClientAccountFactory factory(...$parameters)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taxonomy[] $child_taxonomies
 * @property-read int|null $child_taxonomies_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taxonomy[] $root_taxonomies
 * @property-read int|null $root_taxonomies_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taxonomy[] $account_structure_child_taxonomies
 * @property-read int|null $account_structure_child_taxonomies_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taxonomy[] $job_categorizations_child_taxonomies
 * @property-read int|null $job_categorizations_child_taxonomies_count
 */
class ClientAccount extends Model
{
    use HasFactory, \Staudenmeir\EloquentHasManyDeep\HasRelationships;

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
    ];

    protected $with = ['team', 'teams'];

    protected $appends = ['is_pm_rules_enabled'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxonomies()
    {
        return $this->belongsToMany(\App\Models\Taxonomy::class)
            ->withPivot('use_for_pm_search', 'metadata');;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function child_taxonomies()
    {
        return $this->taxonomies()->whereNotNull('parent_id')
            ->withPivot('use_for_pm_search', 'metadata');;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function account_structure_child_taxonomies()
    {
        return $this->taxonomies()->whereNotNull('parent_id')
            ->whereHas('parent', function (Builder $query) {
                return $query->where('name', 'Account Structure');
            })
            ->withPivot('use_for_pm_search', 'metadata');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pm_searchable_account_structure_child_taxonomies()
    {
        return $this->taxonomies()->whereNotNull('parent_id')
            ->whereHas('parent', function (Builder $query) {
                return $query->where('name', 'Account Structure');
            })
            ->withPivot('use_for_pm_search', 'metadata')
            ->wherePivot('use_for_pm_search', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function job_categorizations_child_taxonomies()
    {
        return $this->taxonomies()->whereNotNull('parent_id')
            ->whereHas('parent', function (Builder $query) {
                return $query->where('name', 'Job Categorizations');
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function root_taxonomies()
    {
        return $this->taxonomies()->whereNull('parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function terms()
    {
        return $this->belongsToMany(\App\Models\Term::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function team()
    {
        return $this->hasOne(Team::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function users()
    {
        return $this->hasManyDeepFromRelations($this->team(), (new Team)->users());
    }

    public function teamOwners()
    {
        return $this->hasManyDeepFromRelations($this->team(), (new Team)->owner());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prodRules()
    {
        return $this->hasMany(Rule::class)->forOp();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmRules()
    {
        return $this->hasMany(Rule::class)->forPm();
    }


    public function flagged_rules()
    {
        return $this->rules()->isFlagged();
    }

    public function published_rules()
    {
        return $this->rules()->isPublished();
    }

    public function omnipresent_rules()
    {
        return $this->rules()->isOmnipresent();
    }

    public function stages()
    {
        return Cache::tags(['taxonomy'])->remember('client-'.$this->id.'-stages', 3600, function () {
            $stage = Taxonomy::whereName(nova_get_setting('stage_taxonomy_name', 'Stage'))->first();
            return $stage->terms()->whereHas('client_accounts', function ($query) {
                $query->where('id', $this->id);
            })->get()->pluck('name')->all();
        });
    }

    /**
     * Check if PM Rules enabled for this account
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isPmRulesEnabled(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->job_categorizations_child_taxonomies()
                    ->where('name', 'PM Section Elements')
                    ->count() != 0
        )->shouldCache();
    }

    /*public function getImageAttribute()
    {
        return'/' . $this->attributes['image'];
    }*/
}

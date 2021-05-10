<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientAccount
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $alias
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
 */
class ClientAccount extends Model
{
    use HasFactory;

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

    protected $with = ['team'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxonomies()
    {
        return $this->belongsToMany(\App\Models\Taxonomy::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function child_taxonomies()
    {
        return $this->taxonomies()->whereNotNull('parent_id');
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

    public function team()
    {
        return $this->hasOne(Team::class);
    }

    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    public function flagged_rules()
    {
        return $this->rules()->isFlagged();
    }

    public function published_rules()
    {
        return $this->rules()->isPublished();
    }

    /*public function getImageAttribute()
    {
        return'/' . $this->attributes['image'];
    }*/
}

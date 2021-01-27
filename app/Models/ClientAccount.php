<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientAccount
 *
 * @property int $id
 * @property string $name
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
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccount whereUpdatedAt($value)
 * @mixin \Eloquent
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
    public function terms()
    {
        return $this->belongsToMany(\App\Models\Term::class);
    }
}

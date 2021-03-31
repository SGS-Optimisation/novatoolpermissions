<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class FieldMapping
 *
 * @package App\Models
 * @mixin \Eloquent
 * @property int $id
 * @property string $api_name
 * @property string|null $api_version
 * @property string $api_action
 * @property string $field_path
 * @property string|null $resolver_name
 * @property int $taxonomy_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Taxonomy $taxonomy
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping query()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereApiAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereApiName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereApiVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereFieldPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereResolverName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereTaxonomyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereUpdatedAt($value)
 */
class FieldMapping extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_name',
        'api_version',
        'api_action',
        'field_path',
        'resolver_name',
        'taxonomy_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonomy(){
        return $this->belongsTo(Taxonomy::class);
    }

    public function getSlugAttribute()
    {
        return \Str::snake($this->api_name . '_' . $this->api_action);
    }
}

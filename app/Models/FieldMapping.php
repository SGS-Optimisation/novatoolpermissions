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
 * @property array $api_params
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
 * @property int $sort_order
 * @property-read mixed $slug
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|FieldMapping whereSortOrder($value)
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
        'api_params',
        'field_path',
        'resolver_name',
        'taxonomy_id'
    ];

    protected $casts = [
        'api_params' => 'array',
    ];

    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonomy()
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function getSlugAttribute()
    {
        $params_str = "";

        if ($this->api_params) {
            foreach ($this->api_params as $k => $v) {
                $params_str .= "[$k=$v]";
            }
        }
        return \Str::snake($this->api_name.'_'.$this->api_action.'_'.$params_str);
    }

    public function getTitleAttribute()
    {
        $params_str = "";

        if ($this->api_params) {
            foreach ($this->api_params as $k => $v) {
                $params_str .= "[$k=$v]";
            }
        }
        return \Str::camel($this->api_action.' '.$params_str);
    }


}

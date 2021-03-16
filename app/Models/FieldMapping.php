<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FieldMapping
 * @package App\Models
 * @mixin \Eloquent
 */
class FieldMapping extends Model
{
    use HasFactory;

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonomy(){
        return $this->belongsTo(Taxonomy::class);
    }
}

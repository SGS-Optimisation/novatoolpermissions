<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\RuleUser
 *
 * @property int $rule_id
 * @property int $user_id
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RuleUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleUser whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleUser whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleUser whereUserId($value)
 * @mixin \Eloquent
 */
class RuleUser extends Pivot
{
    public $timestamps = true;

    protected $casts = [
        'metadata' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MysgsClientAccount
 *
 * @property int $id
 * @property string $name
 * @property string|null $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MysgsClientAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MysgsClientAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MysgsClientAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|MysgsClientAccount whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MysgsClientAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MysgsClientAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MysgsClientAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MysgsClientAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MysgsClientAccount extends Model
{
    use HasFactory;
}

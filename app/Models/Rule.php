<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\Rule
 *
 * @property int $id
 * @property int $client_account_id
 * @property string $name
 * @property string $content
 * @property array $metadata
 * @property bool $flagged
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\ClientAccount $clientAccount
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $terms
 * @property-read int|null $terms_count
 * @method static \Illuminate\Database\Eloquent\Builder|Rule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule newQuery()
 * @method static \Illuminate\Database\Query\Builder|Rule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereClientAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Rule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Rule withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 */
class Rule extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

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
        'client_account_id' => 'integer',
        'metadata' => 'array',
        'flagged' => 'boolean',
    ];

    protected $with = ['terms'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function terms()
    {
        return $this->belongsToMany(\App\Models\Term::class)->with([
            'taxonomy' => function ($query) {
                $query->orderBy('name', 'asc');
            }
        ]);

        //orderBy('name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clientAccount()
    {
        return $this->belongsTo(\App\Models\ClientAccount::class);
    }


    public function recordFlagReason($username, $message = '', $date = null)
    {
        $this->flagged = true;

        $metadata = $this->metadata ?? [];

        if (!isset($metadata['flag_reason'])) {
            $metadata['flag_reason'] = [];
        }

        $metadata['flag_reason'][] = [
            'user' => $username,
            'reason' => $message,
            'date' => $date ?? Carbon::now()->format('Y-m-d H:i:s'),
        ];

        $this->metadata = $metadata;
        $this->timestamps = false;

        $this->save();
    }

    public function unflag()
    {
        $this->flagged = false;

        $metadata = $this->metadata ?? [];
        $metadata['flag_reason'] = [];

        $this->metadata = $metadata;
        $this->timestamps = false;

        $this->save();
    }
}

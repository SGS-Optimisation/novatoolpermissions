<?php

namespace App\Models;

use App\States\HasStates;
use App\States\Rules\DraftState;
use App\States\Rules\PublishedState;
use App\States\Rules\ReviewingState;
use App\States\Rules\RuleState;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
 * @property RuleState $state
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
 * @method static \Database\Factories\RuleFactory factory(...$parameters)
 */
class Rule extends Model implements Auditable
{
    use HasFactory, SoftDeletes, HasStates, \OwenIt\Auditing\Auditable;


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

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
        'state' => RuleState::class,

    ];

    protected $with = ['terms'];

    /**
     * @param  Builder  $query
     * @return Builder
     *
     * All terms in the `Account Structure` taxonomy have the value ANY
     */
    public function scopeIsOmnipresent(Builder $query)
    {
        return $query->whereDoesntHave('accountStructureTerms', function(Builder $termQuery){
            $termQuery->where('name',  '!=', 'ANY');
        });
    }

    public function scopeIsFlagged(Builder $query)
    {
        return $query->where('flagged', true);
    }

    public function scopeIsPublished(Builder $query)
    {
        return $query->whereState('state', PublishedState::class);
    }

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
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accountStructureTerms()
    {
        return $this->terms()->whereHas('taxonomy.parent', function(Builder $query){
            return $query->where('name', 'Account Structure');
        });
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

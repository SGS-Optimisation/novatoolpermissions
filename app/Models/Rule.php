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
use Altek\Accountant\Contracts\Recordable;


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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Term[] $accountStructureTerms
 * @property-read int|null $account_structure_terms_count
 * @method static Builder|Rule isFlagged()
 * @method static Builder|Rule isOmnipresent()
 * @method static Builder|Rule isPublished()
 * @method static Builder|Rule whereNotState(string $column, $states)
 * @method static Builder|Rule whereState($value)
 * @method static Builder|Rule forClient(\App\Models\ClientAccount $clientAccount)
 */
class Rule extends Model implements Recordable
{
    use HasFactory, SoftDeletes, HasStates, \Altek\Accountant\Recordable, \Altek\Eventually\Eventually,
        \Staudenmeir\EloquentHasManyDeep\HasRelationships;


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
    protected $recordableEvents = [
        'created',
        'updated',
        'restored',
        'deleted',
        'synced',
        'forceDeleted',
        'existingPivotUpdated',
        'attached',
        'detached',
    ];

    protected $appends = ['dagId'];

    //protected $with = ['terms'];

    /**
     * @param  Builder  $query
     * @return Builder
     *
     * All terms in the `Account Structure` taxonomy have the value ANY
     */
    public function scopeIsOmnipresent(Builder $query)
    {
        return $query->whereDoesntHave('accountStructureTerms', function (Builder $termQuery) {
            $termQuery->where('name', '!=', 'ANY');
        });
    }

    public function scopeForClient(Builder $query, ClientAccount $clientAccount)
    {
        return $query->where('client_account_id', $clientAccount->id);
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
        ])->using(RuleTerm::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->as('contributor')
            ->withPivot(['metadata'])
            ->withTimestamps();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->as('contributorTeam')
            ->withPivot(['metadata'])
            ->withTimestamps();
    }

    /**
     * Alias for users many-many relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany*
     */
    public function contributors()
    {
        return $this->users();
    }

    /**
     * Alias for teams many-many relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany*
     */
    public function contributorTeams()
    {
        return $this->teams();
    }

    public function teamsViaUsers()
    {
        /*return $this->clientAccount->teams()->whereHas('users', function (Builder $query) {
            return $query->whereIn('users.id', $this->users()->select('id')->get()->pluck('id')->all());
        });*/

        return $this->hasManyDeepFromRelations($this->users(), (new User)->teams())
            ->join('rules', 'rules.id', '=', 'rule_user.rule_id')
            ->whereRaw('rules.client_account_id=teams.client_account_id')
            ->distinct()
            //->whereRaw('teams.client_account_id=rules.client_account_id')
        ;
    }

    /**
     * TODO: merge this query into teams() relationship
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function teamsLeaders()
    {
        /*return $this->clientAccount->teams()->whereHas('users', function (Builder $query) {
            return $query->whereIn('users.id', $this->users()->select('id')->get()->pluck('id')->all());
        });*/

        return $this->hasManyDeepFromRelations($this->users(), (new User)->ownedTeams())
            ->join('rules', 'rules.id', '=', 'rule_user.rule_id')
            ->whereRaw('rules.client_account_id=teams.client_account_id')
            ->distinct()
            //->whereRaw('teams.client_account_id=rules.client_account_id')
        ;
    }

    /**
     * Alias for teams relationship
     * @return Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contributingTeams()
    {
        return $this->teamsViaUsers();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accountStructureTerms()
    {
        return $this->terms()->whereHas('taxonomy.parent', function (Builder $query) {
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

    public function getDagIdAttribute()
    {
        return str_pad($this->id, 6, '0', STR_PAD_LEFT) . 'D';
    }
}

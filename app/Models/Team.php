<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

/**
 * App\Models\Team
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $personal_team
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team wherePersonalTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\ClientAccount $clientAccount
 * @property int $client_account_id
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereClientAccountId($value)
 * @method static Builder|Team personal($personal = true)
 * @property string|null $region
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $rules
 * @property-read int|null $rules_count
 * @method static Builder|Team whereRegion($value)
 */
class Team extends JetstreamTeam
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'personal_team',
        'user_id',
        'region',
        'client_account_id',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    public function scopePersonal(Builder $query, $personal = true)
    {
        return $query->where('personal_team', $personal);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clientAccount()
    {
        return $this->belongsTo(\App\Models\ClientAccount::class);
    }

    public function rules()
    {
        return $this->belongsToMany(User::class)
            ->as('contributorTeam')
            ->withPivot(['metadata'])
            ->withTimestamps();
    }

    public function rulesViaUsers()
    {
        return $this->hasManyDeepFromRelations($this->users(), (new User)->rules())
            ->join('teams', function (\Illuminate\Database\Query\JoinClause $join) {
                $join->on('teams.user_id', '=', 'users.id')
                    ->orOn('teams.id', '=', 'team_user.team_id')
                ;
            })
            ->whereRaw('rules.client_account_id=teams.client_account_id')
            ->distinct()//->whereRaw('teams.client_account_id=rules.client_account_id')
            ;
    }
}

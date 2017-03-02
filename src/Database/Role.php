<?php

namespace Silber\Bouncer\Database;

use App\Models\Account;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Silber\Bouncer\Database\Constraints\Abilities as AbilitiesConstraint;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->table = Models::table('roles');

        parent::__construct($attributes);
    }

    /**
     * The role abilities relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilities()
    {
        return $this->belongsToMany(
            Models::classname(Ability::class),
            Models::table('role_abilities')
        );
    }

    /**
     * The account_abilities relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function account_abilities()
    {
        return $this->belongsToMany(
            Models::classname(Ability::class),
            Models::table('account_abilities')
        );
    }

    /**
     * The users relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            Models::classname(User::class),
            Models::table('user_roles')
        );
    }

    /**
     * The accounts relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accounts()
    {
        return $this->belongsToMany(
            Models::classname(Account::class),
            Models::table('account_roles')
        );
    }

    /**
     * Constrain the given query by the provided ability.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $ability
     * @param  \Illuminate\Database\Eloquent\Model|string|null  $model
     * @return void
     */
    public function scopeWhereCan($query, $ability, $model = null)
    {
        (new AbilitiesConstraint)->constrainRoles($query, $ability, $model);
    }
}

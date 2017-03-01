<?php

namespace Silber\Bouncer\Conductors;

use Silber\Bouncer\Database\Role;
use Silber\Bouncer\Database\Models;

use Illuminate\Database\Eloquent\Model;

class AssignsRole
{
    /**
     * The role to be assigned to a user.
     *
     * @var \Silber\Bouncer\Database\Role|string
     */
    protected $role;

    /**
     * Constructor.
     *
     * @param \Silber\Bouncer\Database\Role|string  $role
     */
    public function __construct($role)
    {
        $this->role = $role;
    }

    /**
     * Assign the role to the given user or account.
     *
     * @param  \Illuminate\Database\Eloquent\Model|array|int  $user
     * @return bool
     */
    public function to($model)
    {
        $role = $this->role();

        if ($model instanceof Model) {
            $user = $model->getKey();
        }

        $ids = is_array($user) ? $user : [$user];
        if($model instanceof \App\Models\Account)
        {
            echo "Model is Account\n";
            $this->assignRoleToAccount($role, $ids);
        }
        else
        {
            echo "Model is User\n";
            $this->assignRole($role, $ids);
        }

        return true;
    }

    /**
     * Get or create the role.
     *
     * @return \Silber\Bouncer\Database\Role
     */
    protected function role()
    {
        if ($this->role instanceof Role) {
            return $this->role;
        }

        return Models::role()->firstOrCreate(['name' => $this->role]);
    }

    /**
     * Assign the role to the users with the given ids.
     *
     * @param  \Silber\Bouncer\Database\Role  $role
     * @param  array  $ids
     * @return void
     */
    protected function assignRole(Role $role, array $ids)
    {
        $existing = $this->getUsersWithRole($role, $ids)->all();

        $ids = array_diff($ids, $existing);

        $role->users()->attach($ids);
    }

    /**
     * Assign the role to the account with the given ids.
     *
     * @param  \Silber\Bouncer\Database\Role  $role
     * @param  array  $ids
     * @return void
     */
    protected function assignRoleToAccount(Role $role, array $ids)
    {
        $existing = $this->getAccountsWithRole($role, $ids)->all();

        $ids = array_diff($ids, $existing);

        $role->accounts()->attach($ids);
    }

    /**
     * Get the IDs of the users that already have the given role.
     *
     * @param  \Silber\Bouncer\Database\Role  $role
     * @param  array  $ids
     * @return \Illuminate\Support\Collection
     */
    protected function getUsersWithRole(Role $role, array $ids)
    {
        $model = Models::user();

        $column = $model->getTable().'.'.$model->getKeyName();

        return $role->users()->whereIn($column, $ids)->lists($column);
    }

    /**
     * Get the IDs of the accounts that already have the given role.
     *
     * @param  \Silber\Bouncer\Database\Role  $role
     * @param  array  $ids
     * @return \Illuminate\Support\Collection
     */
    protected function getAccountsWithRole(Role $role, array $ids)
    {
        $model = Models::account();

        $column = $model->getTable().'.'.$model->getKeyName();

        return $role->accounts()->whereIn($column, $ids)->lists($column);
    }
}

<?php

namespace App\Transformers;

use App\User;
use Spatie\Permission\Models\Role;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['roles'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return $user->attributesToArray();
    }

    public function includeRoles(User $user)
    {
        return count($user->roles) ? $this->collection($user->roles, new RoleTransformer) : null;
    }
}

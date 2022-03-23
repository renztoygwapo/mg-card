<?php

namespace App\Transformers;
use Spatie\Permission\Models\Role;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $this->displayName($role->name)
        ];
    }

    public function displayName($names) {
        if($names == 'forecourt_attendant') {
            return 'Forecourt Attendant';
        } else if ($names == 'sic') {
            return 'SIC';
        } else {
            return 'Admin';
        }
    }
}

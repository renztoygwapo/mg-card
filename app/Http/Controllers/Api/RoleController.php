<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Transformers\RoleTransformer;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::all();

            return fractal()
            ->collection($roles)
            ->transformWith(new RoleTransformer)
            ->toArray();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}

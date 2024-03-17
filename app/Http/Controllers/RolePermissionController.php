<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionController extends Controller
{
    public function attachPermission(Role $role, Permission $permission)
    {
      
        $role->permissions()->attach($permission);
        return response()->json(null, 204);
    }

    public function detachPermission(Role $role, Permission $permission)
    {
        $role->permissions()->detach($permission);
        return response()->json(null, 204);
    }
}

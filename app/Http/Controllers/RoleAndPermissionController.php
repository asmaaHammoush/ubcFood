<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Traits\AuthorizedCheckTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use AuthorizedCheckTrait;
    public function index()
    {
        $this->authorizCheck('setting edit');
        $role=Role::all();
        return Response(['success'=>true,'data'=>$role,200]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission=Permission::all();
        return Response(['success'=>true,'permission'=>$permission,200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $request)
    {
        $data=$request->validated();
        $role=Role::create([
            'name'=>$request->role,
            'guard_name'=>'employee-api'
        ])->givePermissionTo($request->permissions);
//        $permissions=$request->permissions;
//        foreach ($permissions as $permission) {
//            $role->givePermissionTo($permission);
//
//        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
      return Response(['success'=>true,'data'=>$role,200]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
//        $this->authorizCheck('setting edit');
        $role=Role::find($id);
        $role->permission;
        $permission=Permission::all();
        return Response(['success'=>true,'data'=>$role,'permission'=>$permission,200]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, $id)
    {
//        $this->authorizCheck('setting edit');
        $role=Role::find($id);
       $permission= $role->permission;
       $role->revokePermissionTo($permission);
       $role->givePermissionTo($request->permissions);
       $role->update(['name'=>$request->role]);
       $role=$role->refresh();
        $permission=Permission::all();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        return Response(['success'=>true,'data'=>$role,200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
     //   $this->authorizCheck('setting delete');
       $role=Role::find($id);
       $permissions=$role->permissions;
       $role->revokePermissionTo($permissions);
       $role->delete();
        return Response(['success'=>true,200]);
    }
}

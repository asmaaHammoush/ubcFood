<?php
namespace App\Traits;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

trait AuthorizedCheckTrait{
    public function authorizCheck($permission){
        if (!Auth::user('employee-api')->can($permission)){
            throw new AuthenticationException('auth.admin only unauthorized');
        }
    }
}

<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;
    
    public function __construct()
    {
        //
    }
    
    public function index(Admin $admin)
    {
        return in_array($admin->admin_type, [1,2,3,4,5]);
    }
    
    public function create(Admin $admin)
    {
        return in_array($admin->admin_type, [Admin::ADMIN_TYPE_SUPER, Admin::ADMIN_TYPE_BASE_MANAGER, Admin::ADMIN_TYPE_STORE_MANAGER]);
    }
    
    public function store(Admin $admin, $accountType)
    {
        switch ($admin->admin_type) {
            case Admin::ADMIN_TYPE_SUPER:
                return true;
                break;
            case Admin::ADMIN_TYPE_BASE_MANAGER:
                if (in_array($accountType, [3,4,5,6]))
                    return true;
                break;
            case Admin::ADMIN_TYPE_STORE_MANAGER:
                if(in_array($accountType, [5,6]))
                    return true;
                break;
        }
        return false;
    }
    
    public function update(Admin $admin, Admin $account)
    {
        if($account->admin_type == Admin::ADMIN_TYPE_SUPER)
            return false;
        switch ($admin->admin_type) {
            case Admin::ADMIN_TYPE_SUPER:
                return true;
                break;
            case Admin::ADMIN_TYPE_BASE_MANAGER:
                if (in_array($account->admin_type, [3,4,5,6]))
                    return true;
                break;
            case Admin::ADMIN_TYPE_STORE_MANAGER:
                if(in_array($account->admin_type, [5,6]) && $admin->store_id == $account->store_id)
                    return true;
        }
        return false;
    }
    
    public function delete(Admin $admin, Admin $account)
    {
        if($account->admin_type == Admin::ADMIN_TYPE_SUPER)
            return false;
        switch ($admin->admin_type) {
            case Admin::ADMIN_TYPE_SUPER:
                return true;
                break;
            case Admin::ADMIN_TYPE_BASE_MANAGER:
                if (in_array($account->admin_type, [3,4,5,6]))
                    return true;
                break;
            case Admin::ADMIN_TYPE_STORE_MANAGER:
                if(in_array($account->admin_type, [5,6]) && $admin->store_id == $account->store_id)
                    return true;
        }
        return false;
    }
    
    public function view(Admin $admin, Admin $account)
    {
        switch ($admin->admin_type) {
            case Admin::ADMIN_TYPE_SUPER:
                return true;
                break;
            case Admin::ADMIN_TYPE_BASE_MANAGER:
                if (in_array($account->admin_type, [2,3,4,5,6]))
                    return true;
                break;
            case Admin::ADMIN_TYPE_STORE_MANAGER:
                if(in_array($account->admin_type, [3,5,6]) && $admin->store_id == $account->store_id)
                    return true;
                break;
            case Admin::ADMIN_TYPE_BASE_NORMAL:
                if (in_array($account->admin_type, [2,3,4,5,6]))
                    return true;
                break;
            case Admin::ADMIN_TYPE_STORE_NORMAL:
                if (in_array($account->admin_type, [3,5,6]) && $admin->store_id == $account->store_id)
                    return true;
        }
        return false;
    }

    public function queryByStore(Admin $admin)
    {
        return in_array($admin->admin_type, [
            Admin::ADMIN_TYPE_SUPER,
            Admin::ADMIN_TYPE_BASE_MANAGER,
            Admin::ADMIN_TYPE_BASE_NORMAL
        ]);
    }
}

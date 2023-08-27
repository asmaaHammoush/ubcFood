<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $arrayOfPermissionNames=[
            'productAdd','productUpdate','productDelete','showAllproduct','availableProduct',
           'employeeAdd','employeeUpdate','employeeDelete','employeeSearch',
           'showAllEmployee','employeeProfile','employeeAccountAvailable','employeeView',
           'customerDelete','customerAccept','showAllCustomer','searchCustomer',
            'customerAccountAvailable','showAllUNAcceptCustomer' ,'customerProfile',
            'customerLink','CustomerReject','viewWarehouse','viewProduct',
            'warehouseAdd','warehouseUpdate','warehouseDelete','warehouseSearch',
            'showAllOrder','orderDelete','viewOrder','editOrder','rejectOrder',
            'confirmCredit','confirmSalesManager','confirmWarehouse','confirmPicked',
            'confirmShipping','showAllCategory','viewProductCategory',
            'pickerAdd','showAllPicker',
            'viewStock','updateQuantity',
            'viewProductWarehouse',
            'categoryAdd','categoryDelete',
            'pickerOrders','assignOrderToPicker','showPickerInWarehouse',
            'viewShippingInWarehouse','assignOrderToShipping'

        ];
        $permissions=collect($arrayOfPermissionNames)->map(function ($permission){
            return['name' => $permission  ,'guard_name'=>'employee-api'];
        });

        Permission::insert($permissions->toArray());

        $role=Role::create(['name'=>'Admin'])->givePermissionTo($arrayOfPermissionNames);
    }
}

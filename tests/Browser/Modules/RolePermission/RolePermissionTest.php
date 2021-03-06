<?php

namespace Tests\Browser\Modules\RolePermission;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Modules\RolePermission\Entities\Role;
use Tests\DuskTestCase;

class RolePermissionTest extends DuskTestCase
{

    public function setUp(): void
    {
        parent::setUp();


    }

    public function tearDown(): void
    {
        $roles = Role::where('id', '>', 6)->pluck('id');
        Role::destroy($roles);

        parent::tearDown(); // TODO: Change the autogenerated stub
    }
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_for_visit_index_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/hr/role-permission/roles')
                ->assertSee('Role List');
        });
    }

    public function test_for_create_role(){
        $this->test_for_visit_index_page();
        $this->browse(function (Browser $browser) {
            $browser->type('#main-content > section > div.container-fluid.p-0 > div > div.col-lg-3 > div > div > form > div > div > div.row.mt-25 > div > div > input.primary_input_field.form-control', 'Test Role')
                ->click('#main-content > section > div.container-fluid.p-0 > div > div.col-lg-3 > div > div > form > div > div > div.row.mt-40 > div > button')
                ->assertPathIs('/hr/role-permission/roles')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Created successfully!');
        });
    }

    public function test_for_validate_role_create_form(){
        $this->test_for_visit_index_page();
        $this->browse(function (Browser $browser) {
            $browser->type('#main-content > section > div.container-fluid.p-0 > div > div.col-lg-3 > div > div > form > div > div > div.row.mt-25 > div > div > input.primary_input_field.form-control', '')
                ->click('#main-content > section > div.container-fluid.p-0 > div > div.col-lg-3 > div > div > form > div > div > div.row.mt-40 > div > button')
                ->assertPathIs('/hr/role-permission/roles')
                ->assertSeeIn('#main-content > section > div.container-fluid.p-0 > div > div.col-lg-3 > div > div > form > div > div > div.row.mt-25 > div > div > span > strong', 'The name field is required.');
        });
    }

    public function test_for_edit_role(){
        $this->test_for_create_role();
        $this->browse(function (Browser $browser) {
            $role_id = Role::latest()->first()->id;
            $browser->type('#DataTables_Table_0_filter > label > input[type=search]', 'Test Role')
                ->pause(2000)
                ->click('#DataTables_Table_0 > tbody > tr > td:nth-child(4) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr > td:nth-child(4) > div > div > a:nth-child(1)')
                ->assertPathIs('/hr/role-permission/roles/'.$role_id.'/edit')
                ->type('#name', '')
                ->click('#main-content > section > div.container-fluid.p-0 > div > div.col-lg-3 > div > div > form > div > div > div.row.mt-40 > div > button')
                ->assertPathIs('/hr/role-permission/roles/'.$role_id.'/edit')
                ->assertSeeIn('#main-content > section > div.container-fluid.p-0 > div > div.col-lg-3 > div > div > form > div > div > div.row.mt-25 > div > div > span > strong', 'The name field is required.')
                ->type('#name', 'Test Role Edit')
                ->click('#main-content > section > div.container-fluid.p-0 > div > div.col-lg-3 > div > div > form > div > div > div.row.mt-40 > div > button')
                ->assertPathIs('/hr/role-permission/roles')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Updated successfully!');
        });
    }

    public function test_for_delete_role(){
        $this->test_for_create_role();
        $this->browse(function (Browser $browser) {
            $browser->type('#DataTables_Table_0_filter > label > input[type=search]', 'Test Role')
                ->pause(2000)
                ->click('#DataTables_Table_0 > tbody > tr > td:nth-child(4) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr > td:nth-child(4) > div > div > a:nth-child(2)')
                ->whenAvailable('#confirm-delete > div > div > div.modal-body > div.mt-40.d-flex.justify-content-between', function($modal){
                    $modal->click('#delete_link')
                        ->assertPathIs('/hr/role-permission/roles');
                })
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Deleted successfully!');
        });    
    }

    public function test_for_assign_permission(){
        $this->test_for_create_role();
        $this->browse(function (Browser $browser) {
            $role = Role::latest()->first();
            $browser->type('#DataTables_Table_0_filter > label > input[type=search]', 'Test Role')
                ->pause(2000)
                ->click('#DataTables_Table_0 > tbody > tr > td:nth-child(4) > a')
                ->assertPathIs('/hr/role-permission/permissions')
                ->assertSee('Assign Permission ('.$role->name.')')
                ->pause(1000)
                ->click('#\31 75 > div.permission_header.d-flex.align-items-center.justify-content-between > div:nth-child(1) > label')
                ->click('#\31  > div.permission_header.d-flex.align-items-center.justify-content-between > div:nth-child(1) > label')
                ->click('#main-content > form > div > div.row.mt-40 > div > button')
                ->assertPathIs('/hr/role-permission/permissions')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Permission given successfully.');
                
        });        
    }
}

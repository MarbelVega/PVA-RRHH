<?php

Route::macro('common_routes', function () {
	Route::resource('company', 'CompanyController')->except(['create', 'edit', 'destroy']);
	Route::resource('employee', 'EmployeeController')->except(['create', 'edit']);
	Route::resource('city', 'CityController')->except(['store', 'create', 'edit', 'update', 'destroy']);
	Route::resource('management_entity', 'ManagementEntityController')->except(['store', 'create', 'edit', 'update', 'destroy']);
	Route::resource('insurance_company', 'InsuranceCompanyController')->except(['store', 'create', 'edit', 'update', 'destroy']);
	Route::resource('charge', 'ChargeController')->except(['create', 'edit']);
	Route::resource('position', 'PositionController')->except(['create', 'edit']);
	Route::resource('contract', 'ContractController')->except(['create', 'edit']);
	Route::resource('jobschedule', 'JobScheduleController')->except(['create', 'edit']);
	Route::group([
		'prefix' => 'employee/{employee_id}/contract',
	], function () {
		Route::get('', 'EmployeeContractController@get_contracts');
		Route::group([
			'prefix' => '/{contract_id}',
		], function () {
			Route::get('', 'EmployeeContractController@get_contract');
		});
	});
	Route::resource('employer_number', 'EmployerNumberController')->except(['create', 'edit']);
	Route::group([
		'prefix' => 'employer_number/{employer_number_id}/insurance_company/{insurance_company_id}',
	], function () {
		Route::get('', 'EmployerNumberInsuranceCompanyController@get_insurance_company');
		Route::patch('', 'EmployerNumberInsuranceCompanyController@set_insurance_company');
	});
});

Route::macro('admin_routes', function () {
	Route::resource('user', 'UserController')->except(['store', 'create', 'edit']);
	Route::resource('user_action', 'UserActionController')->except(['create', 'store', 'edit', 'update']);
	Route::resource('role', 'RoleController')->except(['store', 'create', 'edit', 'update', 'destroy']);
	Route::group([
		'prefix' => 'user/{user_id}/role',
	], function () {
		Route::get('', 'UserRoleController@get_roles');
		Route::group([
			'prefix' => '/{role_id}',
		], function () {
			Route::get('', 'UserRoleController@get_role');
			Route::post('', 'UserRoleController@set_role');
			Route::delete('', 'UserRoleController@unset_role');
		});
	});
});

Route::group([
	'middleware' => 'api',
	'prefix' => 'v1',
], function ($router) {
	Route::resource('auth', 'AuthController')->except(['index', 'create', 'edit']);
});

if (config('app.debug')) {
	Route::group([
		'middleware' => ['api'],
		'prefix' => 'v1',
	], function ($router) {
		Route::admin_routes();
		Route::common_routes();
	});
} else {
	Route::group([
		'middleware' => ['api', 'jwt.auth'],
		'prefix' => 'v1',
	], function ($router) {
		Route::group([
			'middleware' => ['role:admin'],
		], function ($r) {
			Route::admin_routes();
		});
		Route::common_routes();
	});
}

<?php 

App::booted(function() {
	$namespace = 'Sudo\MailConfig\Http\Controllers';
	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin'])->group(function() {
		// route chung cho cấu hình
		Route::name('settings.')->prefix('settings')->group(function() {
			// Cấu hình chung
			Route::match(['GET', 'POST'], 'mail_config', 'MailConfigController@mailConfig')->name('mail_config');
			Route::post('mail_configs/test_mail','MailConfigController@testMail')->name('test_mail');
		});
	});
});
## Hướng dẫn sử dụng Sudo Mail Config ##

**Giới thiệu:** Đây là package dùng để cấu hình mail.

Mặc định package sẽ tạo ra giao diện cấu hình Mail được đặt tại `/{admin_dir}/settings/mail_config`, trong đó admin_dir là đường dẫn admin được đặt tại `config('app.admin_dir')`

### Cài đặt để sử dụng ###

- Package cần phải có base `sudo/core` để có thể hoạt động không gây ra lỗi
- Để có thể sử dụng Package cần require theo lệnh `composer require sudo/mail-config`
- Buộc phải có modules App\Sudo\Settings để hoạt động

### Cấu hình tại Menu ###

	[
    	'type' 		=> 'single',
		'name' 		=> 'Cấu hình Mail',
		'icon' 		=> 'fas fa-envelope',
		'route' 	=> 'admin.settings.mail_config',
		'role'		=> 'settings_mail_config'
	],
 
- Vị trí cấu hình được đặt tại `config/SudoMenu.php`
- Để có thể hiển thị tại menu, chúng ta có thể đặt đoạn cấu hình trên tại `config('SudoMenu.menu')`

### Cấu hình tại Module ###
	
	'settings' => [
		'name' 			=> 'Cấu hình',
		'permision' 	=> [
			...
			[ 'type' => 'mail_config', 'name' => 'Cấu hình mail' ],
			...
		],
	],

- Vị trí cấu hình được đặt tại `config/SudoModule.php`
- Để có thể phân quyền, chúng ta có thể đặt đoạn cấu hình trên tại `config('SudoModule.modules')`
 
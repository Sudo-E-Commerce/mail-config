<?php

namespace Sudo\MailConfig\Http\Controllers;
use Sudo\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use DB;
use Form;

class MailConfigController extends AdminController
{

	function __construct() {
		$this->models = new \Sudo\MailConfig\Models\Setting;
		parent::__construct();
	}

	public function mailConfig(Request $requests) {
		$setting_name = 'mail_config';
        $title = "Cấu hình Mail";
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
        	$this->models->postData($requests, $setting_name);
        }
        // Lấy dữ liệu ra
        $data = $this->models->getData($setting_name);
        // Giá trị mail mặc định
        $transport = [];
        foreach (array_keys(config('mail.mailers')) as $value) {
        	$transport[$value] = $value;
        }
        $encryption = [
            'tls' => 'TLS',
            'ssl' => 'SSL',
        ];
        $mailers = config('mail.mailers')[config('mail.default')];
        // Khởi tạo form
        $form = new Form;
        $form->select('transport', $data['transport']??config('mail.transport')??'', 0, 'Giao thức', $transport, 0);
        $form->text('host', $data['host']??$mailers['host']??'', 0, 'Máy chủ SMTP', 'smtp.gmail.com');
        $form->text('port', $data['port']??$mailers['port']??'', 0, 'Cổng', '587');
        $form->select('encryption', $data['encryption']??$mailers['encryption']??'', 0, 'Mã hóa bảo mật', $encryption, 0);

        $form->text('username', $data['username']??$mailers['username']??'', 0, 'Tên người dùng SMTP', 'no-reply@sudo.vn');
        $form->password('password', $data['password']??$mailers['password']??'', 0, 'Mật khẩu', 'Mật khẩu');
        $form->text('from_address', $data['from_address']??config('mail.from.address')??'', 0, 'Email Gửi đi', 'no-reply@sudo.vn');
        $form->text('from_name', $data['from_name']??config('mail.from.name')??'', 0, 'Tên đại diện', 'no-reply@sudo.vn');

        $data_form[] = $form->custom('MailConfig::form.test_mail');

        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
        	'title'
        ), 'MailConfig::form.form');
	}

	public function testMail(Request $requests) {
		// Đưa mảng về các biến có tên là các key của mảng
		extract($requests->all(),EXTR_OVERWRITE);
		// Mở check lỗi trên server
		\Barryvdh\Debugbar\Facade::enable();
		// Đặt lại config

		// Set lại transport

        try {
        	if (verifyEmailOrg($email)) {
                // \Mail::to($email)->send(new \App\Modules\MailConfig\Mail\TestEmail);
                return [
					'statis' => 1,
					'message' => __('Gửi thành công'),
				];
            } return [
                'status' => 2,
                'message' => __('Email không tồn tại'),
            ];
        } catch (Exception $e) {
            return [
                'status' => 2,
                'message' => __('Gửi thất bại'),
                'error' => $e->message(),
            ];
        }
		
	}
}
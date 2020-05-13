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
            if (in_array($value, ['smtp', 'ses', 'mailgun'])) {
        	   $transport[$value] = $value;
            }
        }
        // Khởi tạo form
        $form = new Form;
        $form->select('default', $data['default']??config('mail.default')??'', 0, 'Giao thức', $transport, 0);
        $form->custom('MailConfig::form.test_mail', [ 'value' => $data ]);
        $form->text('from_address', $data['from_address']??config('mail.from.address')??'', 0, 'Email Gửi đi', 'no-reply@sudo.vn');
        $form->text('from_name', $data['from_name']??config('mail.from.name')??'', 0, 'Tên đại diện', 'no-reply@sudo.vn');
        $form->custom('MailConfig::form.send_test_mail');
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
        config([
            'mail.from.address'     => $from_address ?? config('mail.form.address') ?? '',
            'mail.from.name'        => $from_name ?? config('mail.form.name') ?? '',
        ]);
        switch ($default) {
            case 'smtp':
                config([
                    'mail.default'                      => $default ?? config('mail.default') ?? '',
                    'mail.mailers.smtp.transport'       => $default ?? config('mail.mailers.smtp.transport') ?? '',
                    'mail.mailers.smtp.host'            => $smtp_host ?? config('mail.mailers.smtp.host') ?? '',
                    'mail.mailers.smtp.port'            => $smtp_port ?? config('mail.mailers.smtp.port') ?? '',
                    'mail.mailers.smtp.encryption'      => $smtp_encryption ?? config('mail.mailers.smtp.encryption') ?? '',
                    'mail.mailers.smtp.username'        => $smtp_username ?? config('mail.mailers.smtp.username') ?? '',
                    'mail.mailers.smtp.password'        => $smtp_password ?? config('mail.mailers.smtp.password') ?? '',
                ]);
            break;
            case 'ses':
                config([
                    'mail.default'                      => $default ?? config('mail.default') ?? '',
                    'mail.mailers.ses.transport'        => $default ?? config('mail.mailers.ses.transport') ?? '',
                    'mail.services.ses.key'             => $ses_key ?? config('mail.services.ses.key') ?? '',
                    'mail.services.ses.secret'          => $ses_secret ?? config('mail.services.ses.secret') ?? '',
                    'mail.services.ses.region'          => $ses_region ?? config('mail.services.ses.region') ?? '',
                ]);
            break;
            case 'mailgun': 
                config([
                    'mail.default'                      => $default ?? config('mail.default') ?? '',
                    'mail.mailers.mailgun.transport'    => $default ?? config('mail.mailers.mailgun.transport') ?? '',
                    'mail.services.mailgun.domain'      => $mailgun_domain ?? config('mail.services.mailgun.domain') ?? '',
                    'mail.services.mailgun.secret'      => $mailgun_secret ?? config('mail.services.mailgun.secret') ?? '',
                    'mail.services.mailgun.endpoint'    => $mailgun_endpoint ?? config('mail.services.mailgun.endpoint') ?? '',
                ]);
            break;
            case 'postmark': 
                config([
                    'mail.default'                      => $default ?? config('mail.default') ?? '',
                    'mail.mailers.postmark.transport'   => $default ?? config('mail.mailers.postmark.transport') ?? '',
                    'mail.services.postmark.token'      => $postmark_token ?? config('mail.services.postmark.token') ?? '',
                ]);
            break;
            case 'sendmail': 
                config([
                    'mail.default'                      => $default ?? config('mail.default') ?? '',
                    'mail.mailers.sendmail.transport'   => $default ?? config('mail.mailers.sendmail.transport') ?? '',
                    'mail.mailers.sendmail.path'        => $sendmail_path ?? config('mail.mailers.sendmail.path') ?? '',
                ]);
            break;
            case 'log': 
                config([
                    'mail.default'                      => $default ?? config('mail.default') ?? '',
                    'mail.mailers.log.transport'        => $default ?? config('mail.mailers.log.transport') ?? '',
                    'mail.mailers.log.channel'          => $log_channel ?? config('mail.mailers.log.channel') ?? '',
                ]);
            break;
            case 'array': 
                config([
                    'mail.default'                      => $default ?? config('mail.default') ?? '',
                    'mail.mailers.array.transport'      => $default ?? config('mail.mailers.array.transport') ?? '',
                ]);
            break;
        }
        // Set lại transport
        (new \Illuminate\Mail\MailServiceProvider(app()))->register();
        
        try {
            if (verifyEmailOrg($email)) {
                // 
                \Mail::to($email)->send(new \Sudo\MailConfig\Mail\MailTest($email));
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
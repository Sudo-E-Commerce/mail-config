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
        switch ($default) {
            case 'smtp':
                config([
                    'mail.default'                  => $default,
                    'mail.mailers.smtp.transport'   => $default,
                    'mail.mailers.smtp.host'        => $smtp_host,
                    'mail.mailers.smtp.port'        => $smtp_port,
                    'mail.mailers.smtp.encryption'  => $smtp_encryption,
                    'mail.mailers.smtp.username'    => $smtp_username,
                    'mail.mailers.smtp.password'    => $smtp_password,
                    'mail.from.address'             => $from_address,
                    'mail.from.name'                => $from_name,
                ]);
            break;
            case 'ses': 
                config([
                    'mail.default'                  => $default,
                    'mail.mailers.smtp.transport'   => $default,
                    'mail.services.ses.key'          => $ses_key,
                    'mail.services.ses.secret'       => $ses_secret,
                    'mail.services.ses.region'       => $ses_region,
                ]);
            break;
            case 'mailgun': 
                config([
                    'mail.default'                  => $default,
                    'mail.mailers.smtp.transport'   => $default,
                    'mail.services.mailgun.domain'   => $mailgun_domain,
                    'mail.services.mailgun.secret'   => $mailgun_secret,
                    'mail.services.mailgun.endpoint' => $mailgun_endpoint,
                ]);
            break;
            case 'postmark': 
                config([
                    'mail.default'                  => $default,
                    'mail.mailers.smtp.transport'   => $default,
                    'mail.services.postmark.token'   => $postmark_token,
                ]);
            break;
            case 'sendmail': 
                config([
                    'mail.default'                  => $default,
                    'mail.mailers.smtp.transport'   => $default,
                    'mail.mailers.sendmail.path'    => $sendmail_path,
                ]);
            break;
            case 'log': 
                config([
                    'mail.default'                  => $default,
                    'mail.mailers.smtp.transport'   => $default,
                    'mail.mailers.log.channel'      => $log_channel,
                ]);
            break;
            case 'array': 
                config([
                    'mail.default'                  => $default,
                    'mail.mailers.smtp.transport'   => $default,
                ]);
            break;
        }
		// Set lại transport
        $swiftMailer = app('mailer')->getSwiftMailer();
        $swiftTransport = $swiftMailer->getTransport();
        $mailer = new \Swift_Mailer($swiftTransport);
        \Mail::setSwiftMailer($swiftMailer);

        try {
        	if (verifyEmailOrg($email)) {
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
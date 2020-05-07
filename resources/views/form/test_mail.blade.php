@php
	$value_email = [
		'smtp' => [
			'host' => $value['smtp_host'] ?? config('mail.mailers.smtp.host') ?? '',
			'port' => $value['smtp_port'] ?? config('mail.mailers.smtp.port') ?? '',
			'encryption' => $value['smtp_encryption'] ?? config('mail.mailers.smtp.encryption') ?? '',
			'username' => $value['smtp_username'] ?? config('mail.mailers.smtp.username') ?? '',
			'password' => $value['smtp_password'] ?? config('mail.mailers.smtp.password') ?? '',
		],
		'ses' => [
			'key' => $value['ses_key'] ?? config('mail.services.ses.key') ?? '',
			'secret' => $value['ses_secret'] ?? config('mail.services.ses.secret') ?? '',
			'region' => $value['ses_region'] ?? config('mail.services.ses.region') ?? '',
		],
		'mailgun' => [
			'domain' => $value['mailgun_domain'] ?? config('mail.services.mailgun.domain') ?? '',
			'secret' => $value['mailgun_secret'] ?? config('mail.services.mailgun.secret') ?? '',
			'endpoint' => $value['mailgun_endpoint'] ?? config('mail.services.mailgun.endpoint') ?? '',
		],
		'postmark' => [
			'token' => $value['postmark_token'] ?? config('mail.services.postmark.token') ?? '',
		],
		'sendmail' => [
			'path' => $value['sendmail_path'] ?? config('mail.mailers.sendmail.path') ?? '',
		],
		'log' => [
			'channel' => $value['log_channel'] ?? config('mail.mailers.log.channel') ?? '',
		],
		'array' => [],
	];
@endphp
<div class="col-lg-12 m-0 p-0 mail-config-generate"></div>

<script>
	$(document).ready(function() {
		generateForm();
		$('body').on('change', 'select[name=default]', function() {
			generateForm();
		});
	});
	function generateForm() {
		default_transport = $('select[name=default]').val();
		form_html = '';
		switch (default_transport) {
			@foreach ($value_email as $key => $field)
				case '{!! $key ?? '' !!}': 
					form_html = `
						@foreach ($field as $name => $value_form)
							<div class="form-group row">
							    <label for="{!! $key ?? '' !!}_{!! $name ?? '' !!}" class="col-lg-3 col-md-2 col-form-label text-right"> @lang(ucfirst($name ?? ''))</label>
							    <div class="col-lg-8 col-md-10">
							    	<input type="{{(in_array($name, ['password', 'secret']))? 'password': 'text'}}" class="form-control" name="{!! $key ?? '' !!}_{!! $name ?? '' !!}" id="{!! $key ?? '' !!}_{!! $name ?? '' !!}" value="{!! $value_form ?? '' !!}" placeholder="@lang(ucfirst($name ?? ''))">
							    </div>
							</div>
						@endforeach
					`;
				break;
			@endforeach
			
		}
		$('.mail-config-generate').html(form_html);
	}
</script>
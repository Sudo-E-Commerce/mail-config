<div class="form-group row">
	<label for="test_mail" class="col-lg-3 col-md-2 col-form-label text-right">@lang('Gửi thử Email')</label>
    <div class="col-lg-6 col-md-8" style="padding-right: 0;">
    	<input type="text" class="form-control" id="test_mail" placeholder="@lang('Email của bạn')">
    	<p class="mt-2 mb-0" id="test_mail_notificate"></p>
    </div>
    <div class="col-lg-2 col-md-2" style="padding-left: 0;">
    	<button type="button" class="btn btn-primary" id="test_mail_btn" style="width: 100%;">@lang('Kiểm tra')</button>
    </div>
</div>
<script>
	$(document).ready(function() {
		$('body').on('click', '#test_mail_btn', function() {
			email 			= $('#test_mail').val();
			transport 		= $('#transport').val();
			host 			= $('#host').val();
			port 			= $('#port').val();
			encryption 		= $('#encryption').val();
			username 		= $('#username').val();
			password 		= $('#password').val();
			from_address 	= $('#from_address').val();
			from_name 		= $('#from_name').val();
			if (email == '') {
				alertText('@lang('Email của bạn không được để trống')');
			} else {
				data = {
					email 			: email,
					transport 		: transport,
					host 			: host,
					encryption		: encryption,
					port 			: port,
					username 		: username,
					password 		: password,
					from_address	: from_address,
					from_name 		: from_name,
				};
				$.ajax({
			        headers: {
			            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			        },
			        type: 'POST',
			        url: '{{route('admin.settings.test_mail')}}',
			        data: data,
			        beforeSend: function(){
			            $('#test_mail_notificate').html('@lang('Đang kiểm tra! Vui lòng chờ giây lát!')');
			        },
			        success:function(result){
			            $('#test_mail_notificate').html(result.message);
			        },
			        error: function (error) {
			            $('#test_mail_notificate').html('@lang('Có lỗi xảy ra! Lỗi:') <br>'+error.responseJSON.message);
			        }
			    });
			}
		});
	});
</script>
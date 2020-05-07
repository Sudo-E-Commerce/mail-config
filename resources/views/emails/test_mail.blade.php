@extends('Core::emails.layouts')
@section('content')

<div class="container">
	<div class="chanhtuoi-email">
		<div class="css-content">
			<h3>@lang('Chào') {{$email ?? ''}},</h3>
			<p>@lang('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu kiểm tra cấu hình mail hiện tại của bạn.')</p>
			<p>@lang('Bạn vui lòng bỏ qua mail này.')</p>
			<p>@lang('Trân trọng'),</p>
			<p><b>@lang('Sudo Team')</b></p>
		</div>
	</div>
</div>

@endsection
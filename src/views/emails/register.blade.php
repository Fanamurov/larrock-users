@extends('vendor.larrock.emails.template.body')

@section('content')
	<h1 style="font:26px/32px Calibri,Helvetica,Arial,sans-serif;">Вы успешно зарегистрировались на сайте {{ env('SITE_NAME', env('APP_URL')) }}</h1>
	<p style="font:14px/16px Calibri,Helvetica,Arial,sans-serif;"><strong>Email/логин:</strong> {{ $data['email'] }}</p>
	<p style="font:14px/16px Calibri,Helvetica,Arial,sans-serif;"><strong>ФИО:</strong> {{ $data['fio'] }}</p>
	<p style="font:14px/16px Calibri,Helvetica,Arial,sans-serif;"><strong>Телефон:</strong> {{ $data['tel'] }}</p>
	<p style="font:14px/16px Calibri,Helvetica,Arial,sans-serif;"><strong>Адрес:</strong> {{ $data['address'] }}</p>

	<p style="font:18px/20px Calibri,Helvetica,Arial,sans-serif;">Ссылка для оплаты/отслеживания заказов: <a href="{{ env('APP_URL') }}/user" target="_blank">личный кабинет</a></p>
@endsection

@section('footer')
    @include('vendor.larrock.emails.template.footer')
@endsection
@extends('larrock::front.main')

@section('title') Регистрация и вход на сайт {!! env('MAIL_TO_ADMIN_NAME') !!} @endsection

@section('content')
    <div class="loginPage">
        <div class="uk-grid uk-grid-divider">
            <div class="uk-width-1-1 uk-width-medium-1-2">
                @include('larrock::front.auth.login')
            </div>
            <div class="uk-width-1-1 uk-width-medium-1-2">
                @include('larrock::front.auth.register')
            </div>
        </div>
        @if(env('ODNOKLASSNIKI_ID'))
            <div class="uk-grid uk-margin-large-top">
                <div class="uk-width-1-1">
                    @include('larrock::front.auth.socialite')
                </div>
            </div>
        @endif
    </div>
@endsection

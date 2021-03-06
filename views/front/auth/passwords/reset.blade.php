@extends('larrock::front.main')
@section('title', 'Сброс пароля')

@section('content')
    <div class="uk-container-center auth-container">
        <div class="uk-grid">
            <div class="uk-width-1-1">
                <h1 class="uk-margin-bottom">Сброс пароля</h1>
            </div>
            <div class="uk-width-1-1 uk-width-medium-1-2 text-container">
                <form class="uk-form uk-form-stacked" method="POST" action="{{ url('/password/reset') }}">
                    <div class="uk-form-row {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="uk-form-label" for="email">E-Mail:</label>
                        <div class="uk-form-controls">
                            <input class="uk-form-large uk-width-1-1" type="email" name="email" id="email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="uk-alert uk-alert-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="uk-form-row {{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="uk-form-label" for="password">Новый пароль</label>
                        <input type="password" class="uk-form-large uk-width-1-1" name="password" id="password">
                        @if ($errors->has('password'))
                            <span class="uk-alert uk-alert-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="uk-form-row {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label class="uk-form-label" for="password_confirmation">Повторите новый пароль</label>
                        <input type="password" class="uk-form-large uk-width-1-1" name="password_confirmation" id="password_confirmation">
                        @if ($errors->has('password_confirmation'))
                            <span class="uk-alert uk-alert-danger">{{ $errors->first('password_confirmation') }}</span>
                        @endif
                    </div>

                    <div class="uk-form-row">
                        {!! csrf_field() !!}
                        <input type="hidden" name="token" value="{!! $token !!}">
                        <button type="submit" class="uk-button uk-button-large uk-width-1-1">Сбросить пароль</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

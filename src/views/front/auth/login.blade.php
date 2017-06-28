<div class="block-login">
    <p class="uk-h2">Авторизация</p>
    <form id="form-login" class="uk-form uk-form-stacked validate" method="POST" action="/user/login">
        {!! csrf_field() !!}

        <div class="uk-form-row">
            <label class="uk-form-label" for="login_email">E-Mail (он же логин):</label>
            <div class="uk-form-controls">
                <input id="login_email" type="email" class="{{ $errors->has('email') ? 'uk-form-danger' : '' }} uk-width-1-1" name="email" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
            </div>
        </div>

        <div class="uk-form-row">
            <label class="uk-form-label" for="login_password">Пароль:</label>
            <div class="uk-form-controls">
                <input id="login_password" type="password" class="{{ $errors->has('password') ? 'uk-form-danger' : '' }} uk-width-1-1" name="password">
                @if ($errors->has('password'))
                    <span class="help-block">{{ $errors->first('password') }}</span>
                @endif
            </div>
        </div>

        <div class="uk-form-row uk-text-right">
            <div class="uk-form-controls">
                <label class="uk-form-label">
                    <input type="checkbox" name="remember"> Запомнить меня
                </label>
            </div>
        </div>

        <div class="uk-form-row">
            <div class="uk-form-controls">
                <input type="hidden" name="page" value="{{ Request::get('page') }}">
                <button type="submit" class="uk-button uk-button-large">
                    <i class="uk-icon-sign-in"></i> Войти в личный кабинет
                </button>
                <a class="lost-password" href="{{ url('/password/reset') }}">Восстановить пароль</a>
            </div>
        </div>
    </form>
</div>
@push('scripts')
{!! JsValidator::formRequest('Larrock\ComponentUsers\Requests\LoginRequest', '#form-login') !!}
@endpush
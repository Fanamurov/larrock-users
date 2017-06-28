<div class="block-register">
    <p class="uk-h2">Регистрация</p>
    <form id="form-register" class="uk-form uk-form-stacked validate" role="form" method="POST" action="{{ url('/register') }}">
        {!! csrf_field() !!}

        <div class="uk-form-row">
            <label class="uk-form-label" for="register_name">Ваше имя:</label>
            <div class="uk-form-controls">
                <input id="register_name" type="text"
                       class="uk-width-1-1 {{ $errors->has('name') ? 'uk-form-danger' : '' }}" name="name" value="{{ old('name') }}">
                @if ($errors->has('name'))
                    <span class="help-block">{{ $errors->first('name') }}</span>
                @endif
            </div>
        </div>

        <div class="uk-form-row">
            <label class="uk-form-label" for="register_email">E-Mail (он же логин):</label>
            <div class="uk-form-controls">
                <input id="register_email" type="email"
                       class="uk-width-1-1 {{ $errors->has('email') ? 'uk-form-danger' : '' }}" name="email" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
            </div>
        </div>

        <div class="uk-form-row">
            <div class="uk-grid">
                <div class="uk-width-1-1 uk-width-medium-1-2">
                    <label class="uk-form-label" for="register_password">Придумайте пароль:</label>
                    <div class="uk-form-controls">
                        <input id="register_password" type="password"
                               class="uk-width-1-1 {{ $errors->has('password') ? 'uk-form-danger' : '' }}" name="password">
                        @if ($errors->has('password'))
                            <span class="help-block">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                </div>
                <div class="uk-width-1-1 uk-width-medium-1-2">
                    <label class="uk-form-label" for="register_password_confirmation">Повторите пароль:</label>
                    <div class="uk-form-controls">
                        <input id="register_password_confirmation" type="password"
                               class="uk-width-1-1 {{ $errors->has('password_confirmation') ? 'uk-form-danger' : '' }}" name="password_confirmation">
                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="uk-form-row">
            <input type="hidden" name="page" value="{{ Request::get('page') }}">
            <button type="submit" class="uk-button uk-button-large uk-width-1-1">
                <i class="uk-icon-user"></i> Зарегистрироваться
            </button>
        </div>
    </form>
</div>
@push('scripts')
{!! JsValidator::formRequest('Larrock\ComponentUsers\Requests\RegisterRequest', '#form-register') !!}
@endpush
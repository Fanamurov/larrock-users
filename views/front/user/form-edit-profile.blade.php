<form action="/user/edit" method="post" id="edit-profile" class="uk-form uk-form-stacked">
    <div class="uk-grid uk-form-row">
        <div class="uk-width-1-1 uk-width-medium-1-3">
            <div class="uk-form-row">
                <label class="uk-form-label" for="email">Email(логин):</label>
                <input type="text" name="email" value="{{ $user->email }}" id="email" class="uk-width-1-1">
            </div>
        </div>
        <div class="uk-width-1-1 uk-width-medium-1-3">
            <div class="uk-form-row">
                <label class="uk-form-label" for="old-password">Старый пароль:</label>
                <input type="password" name="old-password" value="" id="old-password" class="uk-width-1-1">
            </div>
        </div>
        <div class="uk-width-1-1 uk-width-medium-1-3">
            <div class="uk-form-row">
                <label class="uk-form-label" for="password">Новый пароль:</label>
                <input type="password" name="password" value="" id="password" class="uk-width-1-1">
            </div>
        </div>
    </div>
    <div class="uk-grid uk-form-row">
        <div class="uk-width-1-1 uk-width-medium-1-2">
            <div class="uk-form-row">
                <label class="uk-form-label" for="fio">ФИО:</label>
                <input type="text" name="fio" value="{{ $user->fio }}" id="fio" class="uk-width-1-1">
            </div>
        </div>
        <div class="uk-width-1-1 uk-width-medium-1-2">
            <div class="uk-form-row">
                <label class="uk-form-label" for="tel">Телефон:</label>
                <input type="text" name="tel" value="{{ $user->tel }}" id="tel" class="uk-width-1-1">
            </div>
        </div>
    </div>
    <div class="uk-form-row">
        <label class="uk-form-label" for="address">Адрес:</label>
        <textarea name="address" id="address" class="uk-width-1-1">{{ $user->address }}</textarea>
    </div>
    <div class="uk-form-row">
        {!! csrf_field() !!}
        <button type="submit" class="uk-button">Изменить личные данные</button>
    </div>
    <div class="clearfix"></div>
</form>
{!! JsValidator::formRequest('Larrock\ComponentUsers\Requests\EditProfileRequest', '#edit-profile') !!}
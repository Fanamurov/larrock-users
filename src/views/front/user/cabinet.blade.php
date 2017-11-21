@extends('larrock::front.main')
@section('title') Личный кабинет {{ $user->fio or $user->first_name }} @endsection

@section('content')
    <div class="cabinetPage">
        <div class="container">
            <div class="uk-grid">
                <div class="uk-width-1-1">
                    <div class="user-buttons">
                        <a class="uk-button uk-align-right" href="/logout"><i class="uk-icon-close"></i> Выйти</a>
                        <button class="uk-button uk-align-right" type="button" data-uk-toggle="{target:'#collapseEditProfile'}">Редактирование профиля</button>
                    </div>
                    <div class="uk-clearfix"></div>
                    <h1><small>Личный кабинет:</small> {{ $user->fio or $user->first_name }}</h1>
                    <div class="uk-clearfix"></div>
                    <div class="uk-hidden" id="collapseEditProfile">
                        @include('larrock::front.user.form-edit-profile')
                    </div>

                    @if(file_exists(base_path(). '/vendor/fanamurov/larrock-discount'))
                        @if(isset($discounts['num']))
                            <p class="alert alert-success text-center">Поздравляем Вас! К следующему заказу вы получите скидку в
                                @if(isset($discounts['num']) && $discounts['num'] > 0)
                                    {{ $discounts['num'] }} рублей!
                                @endif
                                @if(isset($discounts['percent']) && $discounts['percent'] > 0)
                                    {{ $discounts['percent'] }}%!
                                @endif
                            </p>
                        @endif
                    @endif

                    @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
                        @each('larrock::front.user.orderItem', $user->cart, 'data')
                        @if(count($user->cart) === 0)
                            <div class="uk-alert uk-alert-warning">У вас еще нет заказов</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

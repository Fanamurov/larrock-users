@extends('larrock::admin.main')
@section('title', 'Управление пользователями')

@section('content')
    <div class="container-head uk-margin-bottom">
        <div class="uk-grid">
            <div class="uk-width-expand">
                {!! Breadcrumbs::render('admin.'. $package->name .'.index') !!}
            </div>
            <div class="uk-width-auto">
                @if(isset($allowCreate))
                    <a class="uk-button uk-button-primary" href="/admin/{{ $package->name }}/create">Добавить пользователя</a>
                @endif
            </div>
        </div>
    </div>

    <div class="uk-margin-large-bottom ibox-content">
        <table class="uk-table uk-table-striped uk-form">
            <thead>
            <tr>
                <th width="20" class="uk-visible@s">ID</th>
                @foreach($package->rows as $row)
                    @if($row->inTableAdmin || $row->inTableAdminEditable)
                        <th style="width: 90px" class="@if($row->name !== 'email') uk-visible@s @endif">{{ $row->title }}</th>
                    @endif
                @endforeach
                @if($enable_cart)
                    <th width="80" class="uk-visible@s">Заказы</th>
                    <th width="120" class="uk-visible@s">Сумма покупок</th>
                @endif
                <th width="80" class="uk-visible@s">Роль</th>
                <th width="70"></th>
                <th width="90" class="uk-visible@s"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $data_value)
                <tr>
                    <td class="row-id uk-visible@s">{{ $data_value->id }}</td>
                    @foreach($package->rows as $row)
                        @if($row->inTableAdminEditable)
                            @if($row instanceof \Larrock\Core\Helpers\FormBuilder\FormCheckbox)
                                <td class="row-active @if($row->name !== 'email') uk-visible@s @endif">
                                    <div class="uk-button-group btn-group_switch_ajax" role="group" style="width: 100%">
                                        <button type="button" class="uk-button uk-button-primary uk-button-small @if($data_value->{$row->name} === 0) uk-button-outline @endif"
                                                data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="{{ $package->table }}"
                                                data-row="active" data-value="1" style="width: 50%">on</button>
                                        <button type="button" class="uk-button uk-button-danger uk-button-small @if($data_value->{$row->name} === 1) uk-button-outline @endif"
                                                data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="{{ $package->table }}"
                                                data-row="active" data-value="0" style="width: 50%">off</button>
                                    </div>
                                </td>
                            @elseif($row instanceof \Larrock\Core\Helpers\FormBuilder\FormInput)
                                <td class="uk-visible@s">
                                    <input type="text" value="{{ $data_value->{$row->name} }}" name="{{ $row->name }}"
                                           class="ajax_edit_row form-control" data-row_where="id" data-value_where="{{ $data_value->id }}"
                                           data-table="{{ $package->table }}">
                                </td>
                            @endif
                        @endif
                        @if($row->inTableAdmin)
                            <td class="@if($row->name !== 'email') uk-visible@s @endif">
                                @if($row->name === 'email')
                                    <a href="/admin/users/{{ $data_value->id }}/edit">{{ $data_value->{$row->name} }}</a>
                                @else
                                    {{ $data_value->{$row->name} }}
                                @endif
                            </td>
                        @endif
                    @endforeach
                    @if($enable_cart)
                        <td class="uk-visible@s">
                            {{ \count($data_value->cart) }}
                        </td>
                        <td class="uk-visible@s">
                            @php($cost = 0)
                            @foreach($data_value->cart as $order)
                                @php($cost += $order->cost)
                            @endforeach
                            <a data-uk-tooltip title="Перейти к заказам пользователя" target="_blank" href="/admin/cart?user_search={{ $data_value->email }}">{{ $cost }} руб.</a>
                        </td>
                    @endif
                    <td class="uk-visible@s">
                        @if(\count($data_value->role) > 0)
                            <span class="uk-label">{{ $data_value->role->first()->slug }}</span>
                        @else
                            <span class="uk-label uk-label-danger">Роль не назначена!</span>
                        @endif
                    </td>
                    <td>
                        <a href="/admin/users/{{ $data_value->id }}/edit" class="uk-button uk-button-default uk-button-small">Свойства</a>
                    </td>
                    <td class="uk-visible@s">
                        <form action="/admin/users/{{ $data_value->id }}" method="post">
                            <input name="_method" type="hidden" value="DELETE">
                            {{csrf_field()}}
                            <button type="submit" class="uk-button uk-button-small uk-button-danger please_conform">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $data->links('larrock::admin.pagination.uikit3') !!}
    </div>
@endsection
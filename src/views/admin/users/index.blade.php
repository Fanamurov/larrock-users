@extends('larrock::admin.main')
@section('title', 'Управление пользователями')

@section('content')
    <div class="container-head uk-margin-bottom">
        <div class="uk-grid">
            <div class="uk-width-expand">
                {!! Breadcrumbs::render('admin.'. $app->name .'.index') !!}
            </div>
            <div class="uk-width-auto">
                @if(isset($allowCreate))
                    <a class="uk-button uk-button-primary" href="/admin/{{ $app->name }}/create">Добавить пользователя</a>
                @endif
            </div>
        </div>
    </div>

    <div class="uk-margin-large-bottom ibox-content">
        <table class="uk-table uk-table-striped uk-form">
            <thead>
            <tr>
                <th width="20">ID</th>
                @foreach($app->rows as $row)
                    @if($row->in_table_admin || $row->in_table_admin_ajax_editable)
                        <th style="width: 90px">{{ $row->title }}</th>
                    @endif
                @endforeach
                @if($enable_cart)
                    <th width="80">Заказы</th>
                    <th width="120">Сумма покупок</th>
                @endif
                <th width="80">Роль</th>
                <th width="70"></th>
                <th width="90"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $data_value)
                <tr>
                    <td class="row-id">{{ $data_value->id }}</td>
                    @foreach($app->rows as $row)
                        @if($row->in_table_admin_ajax_editable)
                            @if($row instanceof \Larrock\Core\Helpers\FormBuilder\FormCheckbox)
                                <td class="row-active @if($row->name !== 'active') uk-hidden-small @endif">
                                    <div class="uk-button-group btn-group_switch_ajax" role="group" style="width: 100%">
                                        <button type="button" class="uk-button uk-button-primary uk-button-small @if($data_value->{$row->name} === 0) uk-button-outline @endif"
                                                data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="{{ $app->table }}"
                                                data-row="active" data-value="1" style="width: 50%">on</button>
                                        <button type="button" class="uk-button uk-button-danger uk-button-small @if($data_value->{$row->name} === 1) uk-button-outline @endif"
                                                data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="{{ $app->table }}"
                                                data-row="active" data-value="0" style="width: 50%">off</button>
                                    </div>
                                </td>
                            @elseif($row instanceof \Larrock\Core\Helpers\FormBuilder\FormInput)
                                <td class="uk-hidden-small">
                                    <input type="text" value="{{ $data_value->{$row->name} }}" name="{{ $row->name }}"
                                           class="ajax_edit_row form-control" data-row_where="id" data-value_where="{{ $data_value->id }}"
                                           data-table="{{ $app->table }}">
                                </td>
                            @endif
                        @endif
                        @if($row->in_table_admin)
                            <td class="uk-hidden-small">
                                @if($row->name === 'email')
                                    <a href="/admin/users/{{ $data_value->id }}/edit">{{ $data_value->{$row->name} }}</a>
                                @else
                                    {{ $data_value->{$row->name} }}
                                @endif
                            </td>
                        @endif
                    @endforeach
                    @if($enable_cart)
                        <td>
                            {{ \count($data_value->cart) }}
                        </td>
                        <td>
                            @php($cost = 0)
                            @foreach($data_value->cart as $order)
                                @php($cost += $order->cost)
                            @endforeach
                            <a data-uk-tooltip title="Перейти к заказам пользователя" target="_blank" href="/admin/cart?user_search={{ $data_value->email }}">{{ $cost }} руб.</a>
                        </td>
                    @endif
                    <td>
                        @if(\count($data_value->role) > 0)
                            <span class="uk-label">{{ $data_value->role->first()->slug }}</span>
                        @else
                            <span class="uk-label uk-label-danger">Роль не назначена!</span>
                        @endif
                    </td>
                    <td>
                        <a href="/admin/users/{{ $data_value->id }}/edit" class="uk-button uk-button-default uk-button-small">Свойства</a>
                    </td>
                    <td>
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
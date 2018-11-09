@if ($action == 'browse')

    @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
    <span>{{ $dataTypeContent->{$row->field} }}</span>

@else

    @if(isset($dataTypeContent->{$row->field}))
        <br>
        <small>{{ __('voyager::form.field_password_keep') }}</small>
    @endif
    <input type="password"
           @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif
           class="form-control"
           name="{{ $row->field }}"
           value="">

@endif

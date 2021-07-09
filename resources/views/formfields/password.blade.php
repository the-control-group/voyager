@if(isset($dataTypeContent->{$row->field}))
    <br>
    <small>{{ __('voyager::form.field_password_keep') }}</small>
@endif
<input type="password"
       @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif
       class="form-control"
       {!! outputAriaForHelperText($row) !!}
       name="{{ $row->field }}"
       value="">

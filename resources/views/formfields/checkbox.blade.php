<br>
<?php $checked = false; ?>
@if(isset($dataTypeContent->{$row->field}) || old($row->field))
    <?php $checked = old($row->field, $dataTypeContent->{$row->field}); ?>
@else
    <?php $checked = isset($options->checked) &&
        filter_var($options->checked, FILTER_VALIDATE_BOOLEAN) ? true: false; ?>
@endif

<?php $class = $options->class ?? "toggleswitch"; ?>

@if(isset($options->on) && isset($options->off))
    <input type="checkbox" name="{{ $row->field }}" class="{{ $class }}"
        {!! outputAriaForHelperText($row) !!}
        data-on="{{ $options->on }}" {!! $checked ? 'checked="checked"' : '' !!}
        data-off="{{ $options->off }}">
@else
    <input type="checkbox" name="{{ $row->field }}" class="{{ $class }}"
        {!! outputAriaForHelperText($row) !!}
        @if($checked) checked @endif>
@endif

<input type="date" class="form-control" name="{{ $row->field }}"
       placeholder="{{ $row->getTranslatedAttribute('display_name') }}"
       {!! outputAriaForHelperText($row) !!}
       value="@if(isset($dataTypeContent->{$row->field})){{ \Carbon\Carbon::parse(old($row->field, $dataTypeContent->{$row->field}))->format('Y-m-d') }}@else{{old($row->field)}}@endif">

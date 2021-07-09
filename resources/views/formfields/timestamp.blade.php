<input @if($row->required == 1) required @endif type="datetime" class="form-control datepicker" name="{{ $row->field }}"
       {!! outputAriaForHelperText($row) !!}
       value="@if(isset($dataTypeContent->{$row->field})){{ \Carbon\Carbon::parse(old($row->field, $dataTypeContent->{$row->field}))->format('m/d/Y g:i A') }}@else{{old($row->field)}}@endif">

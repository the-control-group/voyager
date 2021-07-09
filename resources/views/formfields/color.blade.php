<input type="color" class="form-control" name="{{ $row->field }}"
       {!! outputAriaForHelperText($row) !!}
       value="{{ old($row->field, $dataTypeContent->{$row->field}) }}">

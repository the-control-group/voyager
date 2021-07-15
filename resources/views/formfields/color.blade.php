<input type="color" class="form-control" name="{{ $row->field }}"
       value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}">

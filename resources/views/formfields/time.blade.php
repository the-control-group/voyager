<input @if($row->required == 1) required @endif type="time"  data-name="{{ $row->getTranslatedAttribute('display_name') }}"  class="form-control" name="{{ $row->field }}"
       placeholder="{{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }}"
       {!! isBreadSlugAutoGenerator($options) !!}
       {!! outputAriaForHelperText($row) !!}
       value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}">

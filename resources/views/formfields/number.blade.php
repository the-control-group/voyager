<input type="number"
       class="form-control"
       name="{{ $row->field }}"
       type="number"
       @if($row->required == 1) required @endif
       @if(isset($options->min)) min="{{ $options->min }}" @endif
       @if(isset($options->max)) max="{{ $options->max }}" @endif
       step="{{ $options->step ?? 'any' }}"
       placeholder="{{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }}"
       {!! outputAriaForHelperText($row) !!}
       value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}">

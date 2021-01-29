@if ($isModelTranslatable)
    <input type="hidden"
           data-i18n="true"
           name="{{ $_field_name }}_i18n"
           id="{{ $_field_name }}_i18n"
           @if(!empty(session()->getOldInput($_field_name.'_i18n') && is_null($dataTypeContent->id)))
             value="{{ session()->getOldInput($_field_name.'_i18n') }}"
           @else
             value="{{ $_field_trans }}"
           @endif>
@endif

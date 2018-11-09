@if ($action == 'browse')

    @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
    <span>{{ $dataTypeContent->{$row->field} }}</span>

@else

    <div id="{{ $row->field }}" data-theme="{{ @$options->theme }}" data-language="{{ @$options->language }}" class="ace_editor min_height_200" name="{{ $row->field }}">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif</div>
    <textarea name="{{ $row->field }}" id="{{ $row->field }}_textarea" class="hidden">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif</textarea>

@endif

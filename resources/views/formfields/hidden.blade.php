@if ($action == 'browse')

    @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
    <span>{{ $dataTypeContent->{$row->field} }}</span>

@else

    <input type="hidden" class="form-control" name="{{ $row->field }}"
           placeholder="{{ $row->display_name }}"
           {!! isBreadSlugAutoGenerator($options) !!}
           value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif">

@endif

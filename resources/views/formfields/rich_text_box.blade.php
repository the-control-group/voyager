@if ($action == 'browse')

    @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
    <div class="readmore">{{ mb_strlen( strip_tags($dataTypeContent->{$row->field}, '<b><i><u>') ) > 200 ? mb_substr(strip_tags($dataTypeContent->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($dataTypeContent->{$row->field}, '<b><i><u>') }}</div>

@else

    <textarea @if($row->required == 1) required @endif class="form-control richTextBox" name="{{ $row->field }}" id="richtext{{ $row->field }}">
    @if(isset($dataTypeContent->{$row->field}))
            {{ old($row->field, $dataTypeContent->{$row->field}) }}
        @else
            {{old($row->field)}}
        @endif
    </textarea>

@endif

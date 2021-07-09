{{-- If this is a relationship and the method does not exist, show a warning message --}}
@if(isset($options->relationship) && !method_exists( $dataType->model_name, \Illuminate\Support\Str::camel($row->field) ) )
    <p class="label label-warning"><i class="voyager-warning"></i> {{ __('voyager::form.field_select_dd_relationship', ['method' => \Illuminate\Support\Str::camel($row->field).'()', 'class' => $dataType->model_name]) }}</p>
@endif
@php
$dataTypeContent->{$row->field} = json_decode($dataTypeContent->{$row->field})
@endphp
<select class="form-control select2" name="{{ $row->field }}[]" multiple {!! outputAriaForHelperText($row) !!}>
    @if(isset($options->relationship))
        {{-- Check that the relationship method exists --}}
        @if( method_exists( $dataType->model_name, \Illuminate\Support\Str::camel($row->field) ) )
            <?php $selected_values = isset($dataTypeContent) ? $dataTypeContent->{\Illuminate\Support\Str::camel($row->field)}()->pluck($options->relationship->key)->all() : []; ?>
            <?php
            $relationshipListMethod = \Illuminate\Support\Str::camel($row->field) . 'List';
            if (isset($dataTypeContent) && method_exists($dataTypeContent, $relationshipListMethod)) {
                $relationshipOptions = $dataTypeContent->$relationshipListMethod();
            } else {
                $relationshipClass = get_class(app($dataType->model_name)->{\Illuminate\Support\Str::camel($row->field)}()->getRelated());
                $relationshipOptions = $relationshipClass::all();
            }
            ?>
            @foreach($relationshipOptions as $relationshipOption)
                <option value="{{ $relationshipOption->{$options->relationship->key} }}" @if(in_array($relationshipOption->{$options->relationship->key}, $selected_values)) selected="selected" @endif>{{ $relationshipOption->{$options->relationship->label} }}</option>
            @endforeach
        @endif
    @elseif(isset($options->options))
        @foreach($options->options as $key => $label)
                <?php $selected = ''; ?>
            @if(is_array($dataTypeContent->{$row->field}) && in_array($key, $dataTypeContent->{$row->field}))
                <?php $selected = 'selected="selected"'; ?>
            @elseif(!is_null(old($row->field)) && in_array($key, old($row->field)))
                <?php $selected = 'selected="selected"'; ?>
            @endif
            <option value="{{ $key }}" {!! $selected !!}>
                {{ $label }}
            </option>
        @endforeach
    @endif
</select>

@extends('voyager::bread.edit-add')

@section('content')
    @parent
    <div class="form-group col-md-12" id="permissions">
        <label for="permission">{{ __('voyager::generic.permissions') }}</label><br>
        <a href="#" class="permission-select-all">{{ __('voyager::generic.select_all') }}</a> / <a href="#"  class="permission-deselect-all">{{ __('voyager::generic.deselect_all') }}</a>
        <ul class="permissions checkbox">
            @foreach($permissions as $table => $permission)
                <li>
                    <input type="checkbox" id="{{$table}}" class="permission-group">
                    <label for="{{$table}}"><strong>{{\Illuminate\Support\Str::title(str_replace('_',' ', $table))}}</strong></label>
                    <ul>
                        @foreach($permission as $perm)
                            <li>
                                <input type="checkbox" id="permission-{{$perm->id}}" name="permissions[{{$perm->id}}]" class="the-permission" value="{{$perm->id}}" @if(in_array($perm->key, $rolePermissions)) checked @endif>
                                <label for="permission-{{$perm->id}}">{{\Illuminate\Support\Str::title(str_replace('_', ' ', $perm->key))}}</label>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
@stop

@section('javascript')
    @parent
    <script>
        $('document').ready(function () {
            $("#permissions").appendTo("form.form-edit-add .panel-body");

            $('.toggleswitch').bootstrapToggle();

            $('.permission-group').on('change', function(){
                $(this).siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
            });

            $('.permission-select-all').on('click', function(){
                $('ul.permissions').find("input[type='checkbox']").prop('checked', true);
                return false;
            });

            $('.permission-deselect-all').on('click', function(){
                $('ul.permissions').find("input[type='checkbox']").prop('checked', false);
                return false;
            });

            function parentChecked(){
                $('.permission-group').each(function(){
                    var allChecked = true;
                    $(this).siblings('ul').find("input[type='checkbox']").each(function(){
                        if(!this.checked) allChecked = false;
                    });
                    $(this).prop('checked', allChecked);
                });
            }

            parentChecked();

            $('.the-permission').on('change', function(){
                parentChecked();
            });
        });
    </script>
@stop

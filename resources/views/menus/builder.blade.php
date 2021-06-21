@extends('voyager::master')

@section('page_title', __('voyager::generic.menu_builder'))

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-list"></i>{{ __('voyager::generic.menu_builder') }} ({{ $menu->name }})
        <div class="btn btn-success add_item"><i class="voyager-plus"></i> {{ __('voyager::menu_builder.new_menu_item') }}</div>
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    @include('voyager::menus.partial.notice')

    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <p class="panel-title" style="color:#777">{{ __('voyager::menu_builder.drag_drop_info') }}</p>
                    </div>

                    <div class="panel-body" style="padding:30px;">
                        <div class="dd">
                            {!! menu($menu->name, 'admin', ['isModelTranslatable' => $isModelTranslatable]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::menu_builder.delete_item_question') }}</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.menus.item.destroy', ['menu' => $menu->id, 'id' => '__id']) }}"
                          id="delete_form"
                          method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="{{ __('voyager::menu_builder.delete_item_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <div class="modal modal-info fade" tabindex="-1" id="menu_item_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 id="m_hd_add" class="modal-title hidden"><i class="voyager-plus"></i> {{ __('voyager::menu_builder.create_new_item') }}</h4>
                    <h4 id="m_hd_edit" class="modal-title hidden"><i class="voyager-edit"></i> {{ __('voyager::menu_builder.edit_item') }}</h4>
                </div>
                <form action="" id="m_form" method="POST"
                      data-action-add="{{ route('voyager.menus.item.add', ['menu' => $menu->id]) }}"
                      data-action-update="{{ route('voyager.menus.item.update', ['menu' => $menu->id]) }}">

                    <input id="m_form_method" type="hidden" name="_method" value="POST">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div>
                            @include('voyager::multilingual.language-selector')
                            <label for="name">{{ __('voyager::menu_builder.item_title') }}</label>
                            @include('voyager::multilingual.input-hidden', ['_field_name' => 'title', '_field_trans' => ''])
                            <input type="text" class="form-control" id="m_title" name="title" placeholder="{{ __('voyager::generic.title') }}"><br>
                        </div>
                        <label for="type">{{ __('voyager::menu_builder.link_type') }}</label>
                        <select id="m_link_type" class="form-control" name="type">
                            <option value="url" selected="selected">{{ __('voyager::menu_builder.static_url') }}</option>
                            <option value="route">{{ __('voyager::menu_builder.dynamic_route') }}</option>
                        </select><br>
                        <div id="m_url_type">
                            <label for="url">{{ __('voyager::menu_builder.url') }}</label>
                            <input type="text" class="form-control" id="m_url" name="url" placeholder="{{ __('voyager::generic.url') }}"><br>
                        </div>
                        <div id="m_route_type">
                            <label for="route">{{ __('voyager::menu_builder.item_route') }}</label>
                            <input type="text" class="form-control" id="m_route" name="route" placeholder="{{ __('voyager::generic.route') }}"><br>
                            <label for="parameters">{{ __('voyager::menu_builder.route_parameter') }}</label>
                            <textarea rows="3" class="form-control" id="m_parameters" name="parameters" placeholder="{{ json_encode(['key' => 'value'], JSON_PRETTY_PRINT) }}"></textarea><br>
                        </div>
                        <label for="icon_class">{{ __('voyager::menu_builder.icon_class') }} <a
                                    href="{{ route('voyager.compass.index') }}#fonts"
                                    target="_blank">{!! __('voyager::menu_builder.icon_class2') !!}</label>
                        <input type="text" class="form-control" id="m_icon_class" name="icon_class"
                               placeholder="{{ __('voyager::menu_builder.icon_class_ph') }}"><br>
                        <label for="color">{{ __('voyager::menu_builder.color') }}</label>
                        <input type="color" class="form-control" id="m_color" name="color"
                               placeholder="{{ __('voyager::menu_builder.color_ph') }}"><br>
                        <label for="target">{{ __('voyager::menu_builder.open_in') }}</label>
                        <select id="m_target" class="form-control" name="target">
                            <option value="_self" selected="selected">{{ __('voyager::menu_builder.open_same') }}</option>
                            <option value="_blank">{{ __('voyager::menu_builder.open_new') }}</option>
                        </select>
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <input type="hidden" name="id" id="m_id" value="">

                        <br>
                        <label for="permissions">{{ __('voyager::menu_builder.permissions') }}</label>
                        <div>
                            <select id="m_permission_name" class="col-6 form-control update-permissions">
                                <option value="" disabled="disabled" selected="selected">Select Permission</option>
                                @foreach($permissions as $permission)
                            <option value="{{$permission->key}}">{{$permission->key}}</option>
                            @endforeach
                            </select>
                            <select id="m_role_name" class="col-6 form-control update-permissions">
                                <option value="" disabled="disabled" selected="selected">Select Role</option>
                                @foreach($roles as $role)
                            <option value="{{$role->name}}">{{$role->display_name}}</option>
                            @endforeach
                            </select>
                            <input  id="m_permissions" name="permissions" type="hidden"><br>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-success pull-right delete-confirm__" value="{{ __('voyager::generic.update') }}">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->




@stop

@section('javascript')

    <script>
        $(document).ready(function () {
            @if ($isModelTranslatable)
                /**
                 * Multilingual setup for main page
                 */
                $('.side-body').multilingual({
                    "transInputs": '.dd-list input[data-i18n=true]'
                });

                /**
                 * Multilingual for Add/Edit Menu
                 */
                $('#menu_item_modal').multilingual({
                    "form":          'form',
                    "transInputs":   '#menu_item_modal input[data-i18n=true]',
                    "langSelectors": '.language-selector input',
                    "editing":       true
                });
            @endif

            $(".update-permissions").change((e) => {
                let new_value = {};
                if (e.target.id == "m_role_name" && e.target.value!="") {new_value.needs_role = e.target.value; $("#m_permission_name").val("")};
                if (e.target.id == "m_permission_name" && e.target.value!="") {new_value.needs_permission = e.target.value; $("#m_role_name").val("")};
                $("#m_permissions").val(JSON.stringify(new_value));
            })

            $('.dd').nestable({
                expandBtnHTML: '',
                collapseBtnHTML: ''
            });


            /**
             * Set Variables
             */
            var $m_modal       = $('#menu_item_modal'),
                $m_hd_add      = $('#m_hd_add').hide().removeClass('hidden'),
                $m_hd_edit     = $('#m_hd_edit').hide().removeClass('hidden'),
                $m_form        = $('#m_form'),
                $m_form_method = $('#m_form_method'),
                $m_title       = $('#m_title'),
                $m_title_i18n  = $('#title_i18n'),
                $m_url_type    = $('#m_url_type'),
                $m_url         = $('#m_url'),
                $m_link_type   = $('#m_link_type'),
                $m_route_type  = $('#m_route_type'),
                $m_route       = $('#m_route'),
                $m_parameters  = $('#m_parameters'),
                $m_icon_class  = $('#m_icon_class'),
                $m_color       = $('#m_color'),
                $m_target      = $('#m_target'),
                $m_id          = $('#m_id'),
                $m_permissions = $('#m_permission_name,#m_role_name');

            /**
             * Add Menu
             */
            $('.add_item').click(function() {
                $m_form.trigger('reset');
                $m_form.find("input[type=submit]").val('{{ __('voyager::generic.add') }}');
                $m_modal.modal('show', {data: null});
            });

            /**
             * Edit Menu
             */
            $('.item_actions').on('click', '.edit', function (e) {
                $m_form.find("input[type=submit]").val('{{ __('voyager::generic.update') }}');
                $m_modal.modal('show', {data: $(e.currentTarget)});
            });

            /**
             * Menu Modal is Open
             */
            $m_modal.on('show.bs.modal', function(e, data) {
                var _adding      = e.relatedTarget.data ? false : true,
                    translatable = $m_modal.data('multilingual'),
                    $_str_i18n   = '';

                if (_adding) {
                    $m_form.attr('action', $m_form.data('action-add'));
                    $m_form_method.val('POST');
                    $m_hd_add.show();
                    $m_hd_edit.hide();
                    $m_target.val('_self').change();
                    $m_link_type.val('url').change();
                    $m_url.val('');
                    $m_icon_class.val('');
                    $m_permissions.val('');

                } else {
                    $m_form.attr('action', $m_form.data('action-update'));
                    $m_form_method.val('PUT');
                    $m_hd_add.hide();
                    $m_hd_edit.show();

                    var _src = e.relatedTarget.data, // the source
                        id   = _src.data('id');

                    $m_title.val(_src.data('title'));
                    $m_url.val(_src.data('url'));
                    $m_route.val(_src.data('route'));
                    $m_parameters.val(JSON.stringify(_src.data('parameters')));
                    $m_permissions.val(JSON.stringify(_src.data('permissions')));
                    $m_icon_class.val(_src.data('icon_class'));
                    $m_color.val(_src.data('color'));
                    $m_id.val(id);

                    let perms = _src.data('permissions');
                    console.log("src", _src.data());
                    if (perms) {
                        console.log("setting perms",perms);
                        if (perms.needs_permission) $("#m_permission_name").val(perms.needs_permission);
                        if (perms.needs_role) $("#m_role_name").val(perms.needs_role);
                    }

                    if(translatable){
                        $_str_i18n = $("#title" + id + "_i18n").val();
                    }

                    if (_src.data('target') == '_self') {
                        $m_target.val('_self').change();
                    } else if (_src.data('target') == '_blank') {
                        $m_target.find("option[value='_self']").removeAttr('selected');
                        $m_target.find("option[value='_blank']").attr('selected', 'selected');
                        $m_target.val('_blank');
                    }
                    if (_src.data('route') != "") {
                        $m_link_type.val('route').change();
                        $m_url_type.hide();
                    } else {
                        $m_link_type.val('url').change();
                        $m_route_type.hide();
                    }
                    if ($m_link_type.val() == 'route') {
                        $m_url_type.hide();
                        $m_route_type.show();
                    } else {
                        $m_route_type.hide();
                        $m_url_type.show();
                    }
                }

                if (translatable) {
                    $m_title_i18n.val($_str_i18n);
                    translatable.refresh();
                }
            });


            /**
             * Toggle Form Menu Type
             */
            $m_link_type.on('change', function (e) {
                if ($m_link_type.val() == 'route') {
                    $m_url_type.hide();
                    $m_route_type.show();
                } else {
                    $m_url_type.show();
                    $m_route_type.hide();
                }
            });


            /**
             * Delete menu item
             */
            $('.item_actions').on('click', '.delete', function (e) {
                id = $(e.currentTarget).data('id');
                $('#delete_form')[0].action = '{{ route('voyager.menus.item.destroy', ['menu' => $menu->id, 'id' => '__id']) }}'.replace('__id', id);
                $('#delete_modal').modal('show');
            });


            /**
             * Reorder items
             */
            $('.dd').on('change', function (e) {
                $.post('{{ route('voyager.menus.order_item',['menu' => $menu->id]) }}', {
                    order: JSON.stringify($('.dd').nestable('serialize')),
                    _token: '{{ csrf_token() }}'
                }, function (data) {
                    toastr.success("{{ __('voyager::menu_builder.updated_order') }}");
                });
            });
        });
    </script>
@stop

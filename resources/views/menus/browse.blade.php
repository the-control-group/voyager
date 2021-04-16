@extends('voyager::master')

@section('breadcrumbs')
    @include('voyager::partials.breadcrumbs')
@endsection

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-list-add"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        @can('add',app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success">
                <i class="voyager-plus"></i> {{ __('voyager::generic.add_new') }}
            </a>
        @endcan
    </h1>
@stop

@section('content')
    @include('voyager::menus.partial.notice')

    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <table id="dataTable" class="table table-hover">
                            <thead>
                            <tr>
                                @foreach($dataType->browseRows as $row)
                                <th>{{ $row->display_name }}</th>
                                @endforeach
                                <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($dataTypeContent as $data)
                                <tr>
                                    @foreach($dataType->browseRows as $row)
                                    <td>
                                        @if($row->type == 'image')
                                            <img src="@if( strpos($data->{$row->field}, 'http://') === false && strpos($data->{$row->field}, 'https://') === false){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                                        @else
                                            {{ $data->{$row->field} }}
                                        @endif
                                    </td>
                                    @endforeach
                                    <td class="no-sort no-click bread-actions">
                                        @can('delete', $data)
                                            <div class="btn btn-sm btn-danger pull-right delete" data-id="{{ $data->{$data->getKeyName()} }}">
                                                <i class="voyager-trash"></i> {{ __('voyager::generic.delete') }}
                                            </div>
                                        @endcan
                                        @can('edit', $data)
                                            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $data->{$data->getKeyName()}) }}" class="btn btn-sm btn-primary pull-right edit">
                                                <i class="voyager-edit"></i> {{ __('voyager::generic.edit') }}
                                            </a>
                                        @endcan
                                        @can('edit', $data)
                                            <a href="{{ route('voyager.'.$dataType->slug.'.builder', $data->{$data->getKeyName()}) }}" class="btn btn-sm btn-success pull-right">
                                                <i class="voyager-list"></i> {{ __('voyager::generic.builder') }}
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">
                        <i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ $dataType->getTranslatedAttribute('display_name_singular') }}?
                    </h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_this_confirm') }} {{ $dataType->getTranslatedAttribute('display_name_singular') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <!-- DataTables -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "order": [],
                "language": {!! json_encode(__('voyager::datatable'), true) !!},
                "columnDefs": [{"targets": -1, "searchable":  false, "orderable": false}]
                @if(config('dashboard.data_tables.responsive')), responsive: true @endif
            });
        });

        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', ['id' => '__menu']) }}'.replace('__menu', $(this).data('id'));

            $('#delete_modal').modal('show');
        });
    </script>
@stop

<ol class="breadcrumb hidden-xs">
    <li class="active">
        <a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}
        </a>
    </li>
@if(isset($segments))
	@if(isset($dataType))
	<li>
		<a href="{{route('voyager.'.$dataType->slug.'.index')}}">{{ $dataType->getTranslatedAttribute('display_name_plural') }}</a>
	</li>
	@endif
	
	@foreach(array_slice($segments, 0, -1) as $href => $label)		
    <li>
        <a href="{{$href}}">{{ $label }}</a>
    </li>
	@endforeach
    <li>{{ end($segments) }}</li>
@else
    <li>{{ $dataType->getTranslatedAttribute('display_name_plural') }}</li>		
@endif
</ol>

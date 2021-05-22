<div class="container-fluid">
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger text-left text-white" style="font-size: 15px">
            @foreach (collect([$errors->all()])->collapse() as $error)
                {{ $error }} <br>
            @endforeach
        </div>
    @endif
</div>

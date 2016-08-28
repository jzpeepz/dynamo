@foreach (['info', 'danger', 'warning', 'success'] as $messageType)
    @if (Session::has('alert-' . $messageType))
        <div class="alert alert-{{ $messageType }}" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            {{ Session::pull('alert-' . $messageType) }}
        </div>
    @endif
@endforeach

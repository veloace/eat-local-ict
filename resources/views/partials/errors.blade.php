@if(Session::has('success_message'))
    @component('components.toast',['color'=>'success'])
            {{Session::get('success_message')}}
    @endcomponent
@endif
@if(Session::has('error_message'))
    @component('components.toast',['color'=>'danger'])
            {{Session::get('error_message')}}
    @endcomponent
@endif
@if ($errors->any())
    @component('components.toast',['color'=>'danger'])
        <p>The following errors were encountered:</p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endcomponent
@endif
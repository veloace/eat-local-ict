@extends('layouts.app')

@section('content')
    <div class="container">
        @include('partials.errors')
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">All Places</div>

                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <td>ID</td>
                                <td>Name</td>
                                <td>Google Place ID</td>
                                <td>Description</td>
                                <td>Phone</td>
                                <td>Email</td>
                                <td>Website</td>
                                <td>Facebook</td>
                                <td>Instagram</td>
                                <td></td>
                                <td></td>
                            </thead>
                            <tbody>
                            @foreach($places as $place)
                                <tr>
                                    <td>{{$place->id}}</td>
                                    <td><a href="{{route('editPlace',$place->id)}}" target="_blank">{{$place->name}}</a></td>
                                    @if(!empty($place->google_place_id))
                                        <td class="bg-success">YES</td>
                                    @else
                                        <td class="bg-danger">NO PLACE ID</td>
                                    @endif

                                @if(!empty($place->summary))
                                        <td class="bg-success">YES</td>
                                        @else
                                        <td class="bg-warning">NO DESCRIPTION</td>
                                    @endif

                                    @if(!empty($place->phone_number))
                                        <td class="bg-success">YES</td>
                                    @else
                                        <td class="bg-warning">NO PHONE</td>
                                    @endif

                                    @if(!empty($place->email_address))
                                        <td class="bg-success">YES</td>
                                    @else
                                        <td class="bg-warning">NO EMAIL</td>
                                    @endif
                                  @if(!empty($place->website_url))
                                        <td class="bg-success">YES</td>
                                    @else
                                        <td class="bg-warning">NO WEBSITE</td>
                                    @endif

                                    @if(!empty($place->facebook_link))
                                        <td class="bg-success">YES</td>
                                    @else
                                        <td class="bg-warning">NO FACEBOOK</td>
                                    @endif

                                    @if(!empty($place->instagram_link))
                                        <td class="bg-success">YES</td>
                                    @else
                                        <td class="bg-warning">NO INSTAGRAM</td>
                                    @endif
                                    <td>
                                        <a class="btn btn-success" href="{{route('editPlace',$place->id)}}" target="_blank">EDIT</a>
                                    </td>
                                    <td>
                                        <form action="{{route('deletePlace')}}" method="post">
                                            {{csrf_field()}}
                                            {{method_field('delete')}}
                                            <input type="hidden" name="id" value="{{$place->id}}">
                                            <button class="btn btn-danger delete-place" data-target="{{$place->name}}">Delete</button>
                                        </form>
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
    <script>
        document.addEventListener('click', function (event) {

            // If the clicked element doesn't have the right selector, bail
            if (!event.target.matches('.delete-place')) return;

            // Don't follow the link
            if(!confirm('Are you sure you want to delete "'+event.target.getAttribute('data-target')+'"?'))
            {
                event.preventDefault();

            }

        }, false);
    </script>
@endsection

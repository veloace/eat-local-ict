@extends('layouts.app')

@section('content')
    <header class="container">
        <h1>Hi, {{$user->name}}!</h1>
        <p> Welcome to your EatLocalICT Dashboard! From here you can admin the places that you own.</p>
        <p>Think you might be in the wrong place? <a href="//www.eatlocalict.com">click here</a> to go back to the EatLocalICT web app.</p>
    </header>
    @if($user->has_world_admin_access)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        <div class="col-md-12">
                            <h2>Place Description Suggestions</h2>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>Place Name</td>
                                        <td>Existing Description</td>
                                        <td>Suggested Description</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($description_suggestions as $sugg)
                                        <tr>
                                            <td>{{$sugg->place->name}}</td>
                                            <td>{{$sugg->place->summary}}</td>
                                            <td>{{$sugg->description}}</td>
                                            <td>
                                                <form method="post">
                                                    {{csrf_field()}}
                                                    <input type="hidden" name="id" value="{{$sugg->id}}">
                                                    <button class="btn btn-success">Accept</button>
                                                </form>
                                                <form method="post">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input type="hidden" name="id" value="{{$sugg->id}}">
                                                    <button class="btn btn-danger">Decline</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(count($description_suggestions)<1)
                                        <tr>
                                            <td colspan="4">No results found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h2>Missing Place Suggestions</h2>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($missing_suggestions as $missing)
                                        <tr>
                                            <td>{{$missing->name}}</td>
                                            <td>{{$missing->description}}</td>
                                            <td>{{$missing->created_at->toDateTimeString()}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-b">
                            <a href="{{route('indexPlaces')}}" class="btn btn-info">Manage All Places</a>
                            <a href="{{route('addPlace')}}" class="btn btn-success">Add New Place</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    @endif
@endsection

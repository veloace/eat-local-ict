@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Editing {{$place->name}}</div>
                    <div class="card-body">
                        <a  class="btn btn-success"  href="https://google.com/search?q={{urlencode("{$place->name} {$place->city}")}}" target="_blank">Google It.</a>
                        <form method="post">
                            <h2>Edit Information for {{$place->name}}</h2>
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Place Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{$place->name}}">
                                        <small id="emailHelp" class="form-text text-muted">This shouldn't need changing</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image_url">Place Name</label>
                                        <input type="text" class="form-control" id="image_url" name="image_url" value="{{$place->image_url}}">
                                        <small id="emailHelp" class="form-text text-muted">For future use. Leave blank</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="summary">Summary/Description</label>
                                        <textarea class="form-control" name="summary" id="summary" rows="3">{{$place->summary}}</textarea>
                                    </div>
                                </div>

                            </div>


                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

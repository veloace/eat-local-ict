@extends('layouts.app')

@section('content')
    <div class="container">
        @include('partials.errors')
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Add a Place</div>
                    <div class="card-body">
                        <form method="post" action="{{route('saveNewPlace')}}">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Place Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}">
                                        <small id="emailHelp" class="form-text text-muted">This shouldn't need changing</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image_url">Image URL</label>
                                        <input type="text" disabled class="form-control" id="image_url" name="image_url" value="{{old('image_url')}}">
                                        <small id="emailHelp" class="form-text text-muted">For future use. Leave blank</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="summary">Summary/Description</label>
                                        <textarea class="form-control" name="summary" id="summary" rows="3">{{old('summary')}}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email_address">Email Address</label>
                                        <input type="email" class="form-control" id="email_address" name="email_address" value="{{old('email_address')}}">
                                        <small id="emailHelp" class="form-text text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="menu_link">Menu Link</label>
                                        <input type="text" class="form-control" id="menu_link" name="menu_link" value="{{old('menu_link')}}">
                                        <small id="emailHelp" class="form-text text-muted"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website_url">Website URL</label>
                                        <input type="text" class="form-control" id="website_url" name="website_url" value="{{old('website_url')}}">
                                        <small id="emailHelp" class="form-text text-muted"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="facebook_link">Facebook Username</label>
                                        <input type="text" class="form-control" id="facebook_link" name="facebook_link" value="{{old('facebook_link')}}">
                                        <small id="emailHelp" class="form-text text-muted">Just the page slug, the part after facebook.com/</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="instagram_link">Instagram Username</label>
                                        <input type="text" class="form-control" id="instagram_link" name="instagram_link" value="{{old('instagram_link')}}">
                                        <small id="emailHelp" class="form-text text-muted">Just the username, such as "veloace"</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="google_place_id">Google Place ID</label>
                                        <input type="text" class="form-control" id="google_place_id" name="google_place_id" value="{{old('google_place_id')}}">
                                        <small id="emailHelp" class="form-text text-muted"></small>
                                    </div>
                                </div>


                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xs-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_vegan_options" id="has_vegan_options" value="1" {{old('has_vegan_options') ? 'checked':''}}>
                                        <label class="form-check-label" for="has_vegan_options">Has Vegan Options</label>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_gluten_free_options" name="has_gluten_free_options" value="1" {{old('has_gluten_free_options') ? 'checked':''}}>
                                        <label class="form-check-label" for="has_gluten_free_options">Has Gluten-Free Options</label>
                                    </div>
                                </div>


                                <div class="col-xs-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_food_truck" name="is_food_truck" value="1" {{old('is_food_truck') ? 'checked':''}}>
                                        <label class="form-check-label" for="is_food_truck">Is a food-truck</label>
                                    </div>
                                </div>


                                <div class="col-xs-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="serves_full_meals" id="serves_full_meals"  value="1" {{old('serves_full_meals') ? 'checked':''}}>
                                        <label class="form-check-label" for="serves_full_meals">Serves Full Meals</label>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="serves_alcohol" name="serves_alcohol" value="1" {{old('serves_alcohol') ? 'checked':''}}>
                                        <label class="form-check-label" for="serves_alcohol">Serves Alcohol</label>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_public_wifi" name="has_public_wifi" value="1" {{old('has_public_wifi') ? 'checked':''}}>
                                        <label class="form-check-label" for="has_public_wifi">Public Wi-Fi</label>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_bike_rack" id="has_bike_rack" value="1" {{old('has_bike_rack') ? 'checked':''}}>
                                        <label class="form-check-label" for="has_bike_rack">Has Bike Rack</label>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_carryout" id="has_carryout" value="1" {{old('has_carryout') ? 'checked':''}}>
                                        <label class="form-check-label" for="has_carryout">Has Carryout/Drive-Through</label>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xs-12 col-md-12">
                                    <button type="submit" class="btn btn-success btn-lg btn-block">Add Place</button>

                                </div>
                            </div>


                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

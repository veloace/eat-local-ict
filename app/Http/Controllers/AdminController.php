<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeletePlaceRequest;
use App\Http\Requests\EditPlaceRequest;
use App\Http\Requests\EditSuggestionRequest;
use App\MissingPlaceSuggestion;
use App\Place;
use App\PlaceDescriptionSuggestion;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    public function index()
    {
        $data['description_suggestions'] = PlaceDescriptionSuggestion::all();
        $data['missing_suggestions'] = MissingPlaceSuggestion::orderBy('created_at','desc')->get();
        return view('admin.dash',$data);
    }

    public function dataEditRequest()
    {

    }


    public function acceptSuggestion(EditSuggestionRequest $request)
    {
        $suggestion = PlaceDescriptionSuggestion::find($request['id']);
        $place = Place::find($suggestion->place_id);

        $place->summary = $suggestion->description;
        $place->save();
        return $this->deleteSuggestion($request);
    }

    public function deleteSuggestion(EditSuggestionRequest $request)
    {

        PlaceDescriptionSuggestion::destroy($request['id']);
        return $this->index();
    }



    public function indexPlaces()
    {
        $data['places'] = Place::all();
        return view('admin.place.index',$data);

    }


    public function editPlace(Place $place)
    {
        $data['place'] = $place;
        return view('admin.place.edit',$data);
    }

    public function savePlaceEdits(EditPlaceRequest $request)
    {

        $place = Place::find($request['id']);
        $place->name=!empty($request['name']) ? $request['name']:null;
        $place->image_url=!empty($request['image_url']) ? $request['image_url']:null;
        $place->summary=!empty($request['summary']) ? $request['summary']:null;
        $place->email_address=!empty($request['email_address']) ? $request['email_address']:null;
        $place->menu_link=!empty($request['menu_link']) ? $request['menu_link']:null;
        $place->website_url=!empty($request['website_url']) ? $request['website_url']:null;
        $place->facebook_link=!empty($request['facebook_link']) ? $request['facebook_link']:null;
        $place->instagram_link=!empty($request['instagram_link']) ? $request['instagram_link']:null;
        $place->google_place_id=!empty($request['google_place_id']) ? $request['google_place_id']:null;
        $place->has_vegan_options=!empty($request['has_vegan_options']) ;
        $place->has_gluten_free_options=!empty($request['has_gluten_free_options']);
        $place->is_food_truck=!empty($request['is_food_truck']) ;
        $place->serves_full_meals=!empty($request['serves_full_meals']) ? $request['serves_full_meals']:null;
        $place->serves_alcohol=!empty($request['serves_alcohol']) ? $request['serves_alcohol']:null;
        $place->has_public_wifi=!empty($request['has_public_wifi']) ? $request['has_public_wifi']:null;
        $place->has_bike_rack=!empty($request['has_bike_rack']) ? $request['has_bike_rack']:null;
        $place->save();

        return redirect()->route('editPlace',$place->id)->with('success_message', 'Your changes have been applied to this listing.');
    }

    public function addPlace()
    {
        return view('admin.place.add');

    }

    public function saveNewPlace()
    {

    }


    public function deletePlace(DeletePlaceRequest $request)
    {
        Place::find($request['id'])->delete();
        return redirect()->route('indexPlaces')->with('success_message', 'Place successfully deleted.');

    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditSuggestionRequest;
use App\Place;
use App\PlaceDescriptionSuggestion;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    public function index()
    {
        $data['description_suggestions'] = PlaceDescriptionSuggestion::all();
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

    public function savePlaceEdits()
    {

    }

    public function addPlace()
    {
        return view('admin.place.add');

    }

    public function saveNewPlace()
    {

    }

}

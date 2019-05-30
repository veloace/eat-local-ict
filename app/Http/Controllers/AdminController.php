<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddNewPlaceRequest;
use App\Http\Requests\DeletePlaceRequest;
use App\Http\Requests\EditPlaceRequest;
use App\Http\Requests\EditSuggestionRequest;
use App\Http\Requests\ProcessOwnershipClaimRequest;
use App\MissingPlaceSuggestion;
use App\Place;
use App\PlaceDescriptionSuggestion;
use App\PlaceTag;
use App\Tag;
use App\User;
use App\UserPlaceOwnershipClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //

    public function index()
    {
        $data['token']=json_encode(['csrfToken' => csrf_token()]);
        return view('admin.dash',$data);
    }

    public function loadDashboard()
    {
       $data['description_suggestions'] =PlaceDescriptionSuggestion::count();
       $data['missing_place_suggestions']=MissingPlaceSuggestion::count();
       $data['ownership_claims']=UserPlaceOwnershipClaim::where(
           [
               ['is_approved',false],
               ['is_rejected',false]
           ]
       )
           ->count();
        $data['places'] = Place::count();
       $data['users'] = User::count();

       return $data;
    }


    public function showOwnershipClaims()
    {
        $data['ownership_claims']=UserPlaceOwnershipClaim::where(
            [
                ['is_approved',false],
                ['is_rejected',false]
            ]
        )
            ->with('place','user')
            ->get();
        return $data;
    }

    public function processOwnershipClaim(ProcessOwnershipClaimRequest $request)
    {
        $owner = UserPlaceOwnershipClaim::where('id',$request['id'])->first();

        if($request['is_approved'])
        {
            $place = Place::find($owner->place_id);
            $place->owner_user_id = $owner->requester_user_id;
            $place->save();

        }

        $owner->is_approved=$request['is_approved'];
        $owner->is_rejected=$request['is_rejected'];
        $owner->admin_comments=$request['admin_comments'];
        $owner->save();

        return response(null,204);
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
        return Place::with('tags')->paginate(50);

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
        $place->has_carryout=!empty($request['has_carryout']) ? $request['has_carryout']:null;
        $place->serves_brunch=!empty($request['serves_brunch']) ? $request['serves_brunch']:null;
        $place->has_delivery=!empty($request['has_delivery']) ? $request['has_delivery']:null;
        $place->has_ev_charger=!empty($request['has_ev_charger']) ? $request['has_ev_charger']:null;
        $place->wifi_password=!empty($request['wifi_password']) ? $request['wifi_password']:null;

        $this->updateTags($place,$request);

        $place->save();
        return response(null,204);

    }

    /**
     * @param Place $place
     * @param EditPlaceRequest $request
     */
    public function updateTags(Place $place, EditPlaceRequest $request)
    {
        $existing = PlaceTag::select('tag_id')
            ->where('place_id',$place->id)
            ->get()
            ->pluck('tag_id')
            ->toArray();
        $incoming = [];
        if(!empty($request['tags']))
        {
            foreach ($request['tags'] as $tag)
            {
                if(!empty($tag['id']))
                {
                    $incoming[]=$tag['id'];
                    //id exists, so this tag is in our database
                    if(!in_array($tag['id'],$existing))
                    {//it's not in the existing, so we must add it
                        PlaceTag::create([
                            'place_id'=>$place->id,
                            'tag_id'=>$tag['id']
                        ]);
                    }

                }
                else
                {//no id, so we must create the tag
                    $newTag = Tag::where('name',$tag)->first();//check to see if an identical tag exits
                    if($newTag)
                    {//it does exist
                        $incoming[]=$newTag->id;
                        //id exists, so this tag is in our database
                        if(!in_array($newTag->id,$existing))
                        {//it's not in the existing, so we must add it
                            PlaceTag::create([
                                'place_id'=>$place->id,
                                'tag_id'=>$newTag->id
                            ]);
                        }
                    }
                    else
                    {//it does not exist, so create it
                        $newTag  = new Tag();
                        $newTag->name = $tag;
                        $newTag->save();
                        $newTag->fresh();
                        PlaceTag::create([
                            'place_id'=>$place->id,
                            'tag_id'=>$newTag->id
                        ]);
                        $incoming[]=$newTag->id;
                    }
                }
            }
        }

        //now delete all that don't need to be there anymore

        PlaceTag::where('place_id',$place->id)
            ->whereNotIn('tag_id',$incoming)
            ->delete();

    }


    public function addPlace()
    {
        return view('admin.place.add');

    }




    public function deletePlace(DeletePlaceRequest $request)
    {
        Place::find($request['id'])->delete();
        return redirect()->route('indexPlaces')->with('success_message', 'Place successfully deleted.');

    }

}

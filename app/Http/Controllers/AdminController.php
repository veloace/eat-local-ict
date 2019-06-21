<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddNewPlaceRequest;
use App\Http\Requests\DeletePlaceRequest;
use App\Http\Requests\EditPlaceRequest;
use App\Http\Requests\EditSuggestionRequest;
use App\Http\Requests\ProcessOwnershipClaimRequest;
use App\MissingPlaceSuggestion;
use App\Notifications\PlaceClaimAccepted;
use App\Notifications\PlaceClaimRejected;
use App\Place;
use App\PlaceDescriptionSuggestion;
use App\PlaceTag;
use App\PlaceUpdateLog;
use App\Tag;
use App\User;
use App\UserPlaceOwnershipClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //

    /**
     * Serves up the admin Vue web app shell /SPA (corresponds to backend.eatlocalict.com)
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View the SPA shell
     */
    public function index()
    {
        $data['token']=json_encode(['csrfToken' => csrf_token()]);
        return view('admin.dash',$data);
    }//function index.

    /**
     * Get the information needed to display the dashboard for the admins as JSON
     * @return mixed
     */
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
    }//function load dashboard


    function indexPlaceForEdits($place)
    {
        $place = Place::with('tags')->find($place);
        $data['tags'] = Tag::select('id','name')->get();
        $data['listing'] = $place;
        //
        $data['previous'] = Place::where('id','<',$place->id)
            ->orderBy('id','desc')
            ->first();
        $data['previous'] = empty($data['previous']) ? null:$data['previous']->id;
        //
        $data['next'] = Place::where('id','>',$place->id)
            ->orderBy('id','asc')
            ->first();
        $data['next'] = empty($data['next']) ? null:$data['next']->id;

        return $data;
    }


    /**
     * Gets user place ownership claims that haven't been processed yet and returns it as a JSON object
     * @return mixed
     */
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

    /**
     * Processes either the admin approval or admin disapproval of a user's place ownership claim. Notifies claimant of the decision
     *
     * @param ProcessOwnershipClaimRequest $request HTTP request from the front-end
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function processOwnershipClaim(ProcessOwnershipClaimRequest $request)
    {
        $claim = UserPlaceOwnershipClaim::where('id',$request['id'])->first();
        $place = Place::find($claim->place_id);
        $owner = User::where('id',$claim->requester_user_id )->first();

        if($request['is_approved'])
        {
            $place->owner_user_id = $claim->requester_user_id;
            $place->save();

            $owner->notify(new PlaceClaimAccepted($place->name,$request['admin_comments']));
        }
        else
        {
            $owner->notify(new PlaceClaimRejected($place->name,$request['admin_comments']));

        }

        $claim->is_approved=$request['is_approved'];
        $claim->is_rejected=$request['is_rejected'];
        $claim->admin_comments=$request['admin_comments'];
        $claim->save();

        return response(null,204);
    }


    /**
     * Process an admin's approval of a user submitted place description suggestion and saves the user's suggestion as the
     * summary description for the corresponding  place object in our daatabase
     *
     * @param EditSuggestionRequest $request HTTP request from the front-end
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function acceptSuggestion(EditSuggestionRequest $request)
    {
        $suggestion = PlaceDescriptionSuggestion::find($request['id']);
        $place = Place::find($suggestion->place_id);

        $place->summary = $suggestion->description;
        $place->save();
        return $this->deleteSuggestion($request);
    }

    /**
     * Deletes a user-submitted place description suggestion from our database
     *
     * @param EditSuggestionRequest $request HTTP request from the front-end
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deleteSuggestion(EditSuggestionRequest $request)
    {

        PlaceDescriptionSuggestion::destroy($request['id']);
        return $this->index();
    }


    /**
     *
     * Lists all places in the databases, returns (paginates) 50 at a time
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function indexPlaces()
    {
        return Place::with('tags')->paginate(50);

    }


    /**
     * Saves place edits from the Admin frontend and logs the changes
     *
     * @param EditPlaceRequest $request HTTP request from the front-end
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function savePlaceEdits(EditPlaceRequest $request)
    {

        //applies edits to place model
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
        $place->fresh();

        //saves history of the edits to our update log.
        $log = new PlaceUpdateLog();
        $log->place_id = $place->id;
        $log->edited_by_user_id = Auth::id();
        $log->request_json = $request->getContent();
        $log->new_model_json = $place->toJson(JSON_PRETTY_PRINT);
        $log->save();

        return response(null,204);

    }//function savePlaceEdits


    /**
     * Updates the tags for a place based on the incoming place tags. This function deletes all place tags that aren't in $request and
     * also adds place tags that are in $request but aren't yet in the $place. Common tags are left untouched.
     *
     * @param Place $place the place object with old tags  need to be updated
     * @param EditPlaceRequest $request HTTP request that contains the new tags for the $place object
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

    }//function updateTags

    /**
     * Removes a place from our database.
     *
     * @param DeletePlaceRequest $request
     * @return $this
     */
    public function deletePlace(DeletePlaceRequest $request)
    {
        Place::find($request['id'])->delete();
        return redirect()->route('indexPlaces')->with('success_message', 'Place successfully deleted.');

    }//function deletePlace

}

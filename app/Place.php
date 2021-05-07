<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\OpeningHours\OpeningHours;


class Place extends Model
{
    use SoftDeletes;
    //
    protected $appends=['user_distance','is_open','is_favorited','user_comment','claim_status'];

    protected $favorite;
    protected $openingHours;
    protected $hidden = ['placeHours','placeHourExceptions'];
    /**
     * Generates the Apple Maps navigation link for use by the frontend
     * @return string
     */
    public function getMapLinkAttribute()
    {
        $query ="{$this->name}";
        $address = "{$this->name} {$this->address} {$this->city}, {$this->state}";
        $query= urlencode($query);
        return "http://maps.apple.com/?q={$query}&address={$address}";
    }


    /**
     * Determines if this places is favorited by the current user
     * @return bool
     */
    function getIsFavoritedAttribute()
    {
        if(Auth::check())
        {
            return UserFavorite::where([
                ['user_id',Auth::id()],
                ['place_id',$this->id],
            ])->count() >0;
        }//if user is logged in
        return false;
    }

    function getUserCommentAttribute()
    {
        return !empty($this->favorite) ? $this->favorite->comment : null;
    }


    /**
     * determines if the location is open or closed at this exact time.
     * @return bool
     */
    public function getIsOpenAttribute()
    {
        if(empty($this->openingHours)){
            $this->setOpeningHours();//set the hours, if they are not yet set
        }
        return $this->openingHours->isOpenAt(Carbon::now()); // false

    }//is_open



    /**
     * determines if the necessary data is there to calculate the distance between the user and this location.
     * @return float|null
     */
    public function getUserDistanceAttribute()
    {
        $lat = session('lat');
        $lng = session('lng');
        if(!empty($lat) && !empty($lng) && !empty($this->latitude) && !empty($this->longitude))
        {//if we have coordinates for this user and this place, we can calculate distance
            return $this->calculateDistance($lat, $lng, $this->latitude,$this->longitude);
        }

        return null;
    }

    /**
     * Calculates the spherical straight-line distance of between the the two sets of points
     * @param $lat1 float latitude of point 1
     * @param $lng1 float longitude of point 1
     * @param $lat2 float latitude of point 2
     * @param $lng2 float longitude of point 2
     * @return float
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $radiusEarth=3959;
        $deltaLat = deg2rad($lat2 -$lat1);
        $deltaLong =deg2rad($lng2-$lng1);
        $lat1 = deg2rad($lat1);
        $lat2=deg2rad($lat2);
        $a = sin($deltaLat/2)*sin($deltaLat/2)+sin($deltaLong/2)*sin($deltaLong/2)*cos($lat1)*cos($lat2);
        $c = 2*atan2(sqrt($a), sqrt(1-$a));
        return (round($radiusEarth*$c,2));
    }

    /**
     * Determines the claim status of this listing in relation to current user
     * There are five types of claim status:
     *
     * 1. 'unclaimed' Means this place has not been claimed by anyone and is open to being claimed
     * 2. 'claimed' Means this place has been claimed, but it is claimed by someone who is not the current user
     * 3. 'pending' Means this place has been claimed by the current user, but the ownership has not been verified by an admin.
     * 4. 'approved' means the current user has claimed this place and it has been approved by and admin. Logically similar to 'claimed'
     * 5. 'denied' means the current user has claimed this place, but it was denied by an admin
     *
     * @return string the claim status as described above
     */
    public function getClaimStatusAttribute()
    {

        if(!Auth::check())
        {
            //if the current user is  NOT logged in only unclaimed and claimed are possible, so let's get the simplest query possible
            return  UserPlaceOwnershipClaim::where([
                ['place_id',$this->id],
                ['is_approved',true]
            ])->count() > 0 ? 'claimed':'unclaimed';//as long as there is at least one that is approved, then return 'claimed' else return 'unclaimed'

        }
        else
        {
            //first, get all of the ownership claims belonging to this user.
            $claims = UserPlaceOwnershipClaim::where([
                ['place_id',$this->id],
                ['requester_user_id',Auth::id()],
            ])
            ->orderBy('created_at','desc')
            ->first();//only get the most recent claim from this user, if there is more than one.

            if($claims)
            {//if there are claims, we need to sort through them
                if($claims->is_approved)
                {
                    return 'approved';
                }
                elseif($claims->is_rejected)
                {
                    return 'denied';
                }
                else
                {
                    return 'pending';
                }
            }
            else
            {//just return the same as if the user wasn't logged in.
                return  UserPlaceOwnershipClaim::where([
                    ['place_id',$this->id],
                    ['is_approved',true]
                ])->count() > 0 ? 'claimed':'unclaimed';//as long as there is at least one that is approved, then return 'claimed' else return 'unclaimed'

            }//else
        }//else



    }//function getClaimStatusAttribute.

    /**
     * gets the tags associated with this place
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
     public function tags()
    {
        return $this->hasManyThrough(Tag::class,PlaceTag::class,'place_id','id','id','tag_id');
    }

    /**
     * Gets the raw place hours
     *
     */
    public function placeHours(){
         return $this->hasMany(PlaceHour::class,'place_id','id');
    }
    /**
     * Gets the raw place hours
     *
     */
    public function placeHourExceptions(){
         return $this->hasMany(PlaceHourException::class,'place_id','id')->orderBy('day_of_week')->orderBy('start');
    }


    public function setOpeningHours(){
        /*first step is to get our place hours from the database and convert them to a form usable
         by the spatie openingHours package*/
        $weekHours=[
            'monday'     => [],
            'tuesday'    => [],
            'wednesday'  => [],
            'thursday'   => [],
            'friday'     => [],
            'saturday'   => [],
            'sunday'     => [],
            'exceptions'     => [],
        ];//prototype of array used by Spatie package
        foreach ($this->placeHours as $dayHours){
           $day =  strtolower(jddayofweek($dayHours->day_of_week,1));
           if($dayHours->open_all_day){
               $weekHours[$day][]="00:00-23:59";

           } elseif($dayHours->closed){
               $weekHours[$day]=[];

           } else {
               $start = substr($dayHours->start,0,5);
               $end = substr($dayHours->end,0,5);
               $weekHours[$day][]="{$start}-{$end}";
           }
        }
        $this->openingHours = OpeningHours::create($weekHours);
    }

    public function getOpenHoursAttribute(){
        if(empty($this->openingHours)){
            $this->setOpeningHours();//set the hours, if they are not yet set
        }
        return $this->openingHours->forWeekConsecutiveDays();
    }


    public function getStoreHoursForDisplayAttribute(){
        $weekHours = [];
        $today = Carbon::now()->format('l');
        foreach ($this->placeHours as $dayHours){
            $day =  jddayofweek($dayHours->day_of_week,1);
            $day = $day == $today ? 'Today' : $day ;
            if($dayHours->open_all_day){
                $weekHours[$day] = "Open 24 Hours";
            } elseif($dayHours->closed){
                $weekHours[$day]='CLOSED ALL DAY';
            } else {
                $start = (new Carbon($dayHours->start))->format('g:i A');
                $end = (new Carbon($dayHours->end))->format('g:i A');
                $weekHours[$day]="{$start} to {$end}";
            }
        }

        return $weekHours;
    }







}



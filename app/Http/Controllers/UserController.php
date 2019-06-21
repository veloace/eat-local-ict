<?php

namespace App\Http\Controllers;

use App\Http\Requests\SPALoginRequest;
use App\Http\Requests\UpdateUserSelfRequest;
use App\Http\Requests\UserAccountDeletionRequest;
use App\Http\Requests\UserPasswordSelfServiceChangeRequest;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    //

    /**
     * Process an AJAX login request from the frontend
     *
     * @param SPALoginRequest $request
     * @param Encrypter $enc
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    function loginViaSpa(SPALoginRequest $request,Encrypter $enc)
    {


        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            // Authentication passed...
            $data['user']=User::where('id',Auth::id())->first();
            $data['token']=csrf_token();
            return response()->json($data);

        }
        else
        {
            $data=['message'=>'Incorrect email or password'];
            return response($data,422);
        }//ekse
    }//function loginViaSPA

    /**
     * Returns the current user information to front end. Used primarily as a canary on the front end to determine if the current session is valid.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function currentUser()
    {
        $id = Auth::id();
        $user = User::where('id',$id)
            ->select('name','email','can_get_newsletter')
            ->first();
        if($user)
        {
            return($user);

        }
        else
        {
            return response(null,204);
        }
    }//function currentUser


    /**
     * Allows user to change their own password from the account page of the SPA
     *
     * @param UserPasswordSelfServiceChangeRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePassword(UserPasswordSelfServiceChangeRequest $request)
    {
        $user = User::find(Auth::id());

        if(Hash::check($request->old_password,$user->password))
        {
            //change password
            $user->password = Hash::make($request->password);
            $user->save();
            return response(null,204);
        }
        return response()->json(['message'=>'The given data was invalid', 'errors'=>['old_password'=>['Existing password is incorrect.']]],422);
    }

    /**
     * Allows the user to securely delete their account. End result is that any trace of them is delete from our DB
     *
     * @param UserAccountDeletionRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAccount(UserAccountDeletionRequest $request)
    {
        $user = User::find(Auth::id());

        if(Hash::check($request->delete_password,$user->password))
        {

            //first, since we use soft deletes, purge the user data.
            //we'll do that by first deleting any references to their email in the password_resets table
            DB::table('password_resets')
                ->where('email',$user->email)
                ->delete();
            //next, we'll delete their PII on the user model
            $user->name = md5(time());//just to prevent non-null errors
            $user->email = md5(time());//just to prevent non-null errors
            $user->remember_token =null;
            $user->password ='DELETED PASSWORD';
            $user->save();
            //
            //next, let's log the user out
            Auth::logout();
            //
            //and finally, let's delete the user's model
            $user->delete();
            return response(null,204);
        }
        return response()->json(['message'=>'The given data was invalid', 'errors'=>['delete_password'=>['Password is incorrect.']]],422);
    }


    /**
     * Allows user to update their name and/or email address from the frontend
     *
     * @param UpdateUserSelfRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function updateUser(UpdateUserSelfRequest $request)
    {

        //first, check if the email changed
        $hasEmailChanged = Auth::user()->email != $request['email'];

        if($hasEmailChanged)
        {
            //first, check email to make sure it doesn't belong to someone else
            $emailInUse = User::where('email',$request['email'])
                    ->where('id','<>',Auth::id())
                    ->count() > 0;

            if($emailInUse)
            {
                return response()->json(['errors'=>['email'=>['That email is already in use by another user.']]],422);

            }
        }//email Changed
            $user = User::where('id', Auth::id())
                ->first();

            $user->name = $request['name'];
            $user->email =$request['email'];
            $user->can_get_newsletter = !empty($request['newsletter']);
            $user->email_verified_at  = $hasEmailChanged ? null: $user->email_verified_at;
            //

            if($hasEmailChanged)
            {
                $user->fresh();
                $user->sendEmailVerificationNotification();//resendEmailVerification

                return response()->json(['message'=>'It looks like you changed your email, so for security purposes we are requiring you to re-verify your email. Please check your email for a message from us.'],200);
            }

            return response(null,204);

    }//function update user

}//class

<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'g-recaptcha-response'=>'required',

        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * The user has been registered (overrides the default)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
        return response()->json(["Message" => "Success"]);

    }


    /**
     * Handle a registration request for the application. (overrides RegistersUsers.php)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        if(!$this->validateRecaptcha($request['g-recaptcha-response']))
        {
            return response()->withErrors(['register' => 'Registration failed due to Google Recaptcha failure. Please try again or contact support if this problem persists.']);

        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * @param $response
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function validateRecaptcha($response)
    {
        try {
            $grecaptchaClient = new Client(['base_uri' => 'https://www.google.com']);
            $gresponse = $grecaptchaClient->request('POST', '/recaptcha/api/siteverify', ['form_params' => [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $response
            ]]);
            return (json_decode($gresponse->getBody())->success);
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

}

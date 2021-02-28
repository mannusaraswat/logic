<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use PDF;

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
    protected $redirectTo = RouteServiceProvider::HOME;

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'product_id' => ['required'],
        ],[
        'product_id.required' => 'Please first run seed',
    ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'product_id' => $data['product_id'],
            'password' => Hash::make($data['password']),
        ]);

        //    $user->notify(new WelcomeEmail());
        $product_name = $user->product->name;
        // send email with the template
        $email = $user->email;

        // creating download url
        $download_url = URL::signedRoute('download', ['id' => Crypt::encrypt($user->id)]);

        // sending mail to user
        Mail::send('mail',
            [
                'name' => ucfirst($user->name),
                'product' => $product_name,
                'download' => $download_url
            ],
            function ($m) use ($email) {
                $m->from('manmohan.iws@gmail.com', 'Logic');
                $m->to($email)->subject('Welcome mail');
            });

        $data = [
            'name' => ucfirst($user->name),
            'product' => $product_name
        ];

        $pdf = PDF::loadView('pdf', $data);
        $path = "public/pdf/" . $user->id . ".pdf";

        // saving pdf file into storage folder
        Storage::put($path, $pdf->output());

        // saving download path
        Email::create([
            'user_id' => $user->id,
            'path' => $path,
        ]);

        return $user;
    }
}

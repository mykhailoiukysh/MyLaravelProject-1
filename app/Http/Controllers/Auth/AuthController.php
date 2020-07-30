<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Models\Administrators;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Validator;

/**
 * Class AuthController.
 */
class AuthController extends Controller
{
    /**
     ******************************************************* Redirections (surcharge des valeurs par défaut de Laravel). ******************************************************.
     */

    /**
     * @var string
     */
    protected $loginPath = '/login';

    /**
     * @var string
     */
    protected $redirectPath = '/admin/';

    /**
     * @var string
     */
    protected $redirectAfterLogout = '/login/';

    /**
     * @var string
     */
    protected $redirectTo = '/login';

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * La construction de mon controlleur
     * agit sur toutes les methodes qui seront appelé dans ce constructeur
     */
    public function __construct()
    {
        // application du middleware guest
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:administrators',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        return Administrators::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /********************************************** Github Auth ****************************************************************************/

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return Redirect::to('auth/facebook');
        }

        $authUser = $this->findOrCreateUser($user);

        Auth::login($authUser, true);

        return Redirect::to('home');
    }

    /**
     * Return user if exists; create and return if doesn't.
     *
     * @param $githubUser
     *
     * @return User
     */
    private function findOrCreateUser($githubUser)
    {
        if ($authUser = Administrators::where('github_id', $githubUser->id)->first()) {
            return $authUser;
        }

        return Administrators::create([
            'name'      => $githubUser->name,
            'email'     => $githubUser->email,
            'github_id' => $githubUser->id,
            'avatar'    => $githubUser->avatar,
        ]);
    }
}

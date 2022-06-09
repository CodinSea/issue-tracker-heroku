<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Models\Ticket;
use App\Models\ProjectPersonnel;
use App\Notifications\RoleAssignmentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserAuthController extends Controller
{
    public function login() 
    {
        return view('auth.login');
    }

    public function register() 
    {
        return view('auth.register');
    }

    public function demoUser() 
    {
        return view('auth.demoUser');
    }

    public function directDemoUser(Request $request) 
    {
        $user = User::where('id', 3)->first();
        $request->session()->put('LoggedUser', $user);
        $request->session()->put('LoggedUserFirstName', $user->first_name);
        $request->session()->put('LoggedUserId', $user->id);
        $request->session()->put('LoggedUserRole', $request->role);

        return redirect('dashboard');       
    }

    public function createUser(Request $request) 
    {
        $request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8|max:16|confirmed',
            'password_confirmation' => 'required'
        ]);

        $newUser = new User;
        $newUser->first_name = $request->firstname;
        $newUser->last_name = $request->lastname;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);
        $query = $newUser->save();

        if ($query) {
            return back()->with('success','You have been successfully registered.');
        } else {
            return back()->with('fail','Something went wrong!');
        }

        return view('auth.login');
    }

    public function checkUser(Request $request) 
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:8|max:16'
        ]);

        $user = User::where('email', "=", $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $request->session()->put('LoggedUser', $user);
                $request->session()->put('LoggedUserFirstName', $user->first_name);
                $request->session()->put('LoggedUserId', $user->id);
                $request->session()->put('LoggedUserRole', $user->role);

                return redirect('dashboard');
            } else {
                return back()->with('fail', 'Invalid password.');
            }
        } else {
            return back()->with('fail', 'No account found for this email.');
        }
    }

    public function logout() 
    {
        if (session()->has('LoggedUser')) {
            session()->pull('LoggedUser');
            
            return redirect('login');
        }
    }

    public function forgotPassword() 
    {
        return view('auth.forgotPassword');
    }

    public function sendPasswordResetLink(Request $request) 
    {
        $request->validate([
            'email'=>'required|email|exists:users,email'
        ]);

        $token = Str::random(64);
        \DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $action_link = route('resetPasswordForm', [
                        'token' => $token,
                        'email' => $request->email
                    ]);
        $body = "We have received a request to reset the password for <b> Issue Tracker </b> account associated with ". $request->email . ". You can reset your password by clicking the link below.";

        \Mail::send('email-forgotPassword', [
                'action_link' => $action_link,
                'body' => $body
            ], function($message) use ($request) {
                $message->from('noreply@example.com', 'Issue Tracker');
                $message->to($request->email, 'Your name')
                        ->subject('Reset Password');
            });

        return back()->with('success', 'We have emailed your password reset link!');
    }

    public function resetPasswordForm(Request $request, $token = null) 
    {
        return view('auth.resetPassword')->with(['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request) 
    {
        $request->validate([
            'email'=>'required|email|exists:users,email',
            'password'=>'required|min:8|max:16|confirmed',
            'password_confirmation' => 'required'
        ]);

        $check_token = \DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->token,
        ])->first();

        if(!$check_token) {
            return back()->withInput()->with('fail', 'Invalid token');
        } else {
            User::where('email', $request->email)->update([
                'password' => Hash::make($request->password)
            ]);

            \DB::table('password_resets')->where([
                'email' => $request->email
            ])->delete();

            return redirect()->route('login')->with('info', 'Your password has been changed! <br> You can login with new password.')->with('verifiedEmail', $request->email);
        }    
    }

    public function index(Request $request) 
    {
        $allUsers = User::where('id', '!=', 3)->get();

        if(isset($_GET['entriesPerPage'])) {
            $entriesPerPage = $_GET['entriesPerPage'];
            if(isset($_GET['currentQuery'])) {
                $currentQuery = $_GET['currentQuery'];
            } else {
                $currentQuery = "";
            }
        } else {
            $entriesPerPage = 5;
            $currentQuery = "";
        }

        if(isset($_GET['query'])) {
            $searchText = $_GET['query'];
            $users = User::where('id', '!=', 3)
                            ->where(function($query) use ($searchText) {
                                $query->where('first_name','LIKE','%'.$searchText.'%')
                                      ->orWhere('last_name','LIKE','%'.$searchText.'%')
                                      ->orWhere('role','LIKE','%'.$searchText.'%');
                            })
                            ->sortable()
                            ->paginate($entriesPerPage)
                            ->withQueryString();

            return view('role_assignment')->with(compact('allUsers', 'users'))->with('searchText', $searchText);
        } else {          
            $users = User::where('id', '!=', 3)
                            ->where(function($query) use ($currentQuery) {
                                $query->where('first_name','LIKE','%'.$currentQuery.'%')
                                      ->orWhere('last_name','LIKE','%'.$currentQuery.'%')
                                      ->orWhere('role','LIKE','%'.$currentQuery.'%');
                            })
                            ->sortable()
                            ->paginate($entriesPerPage)
                            ->withQueryString();

            return view('role_assignment')->with(compact('allUsers', 'users'))->with('searchText', $currentQuery);
        }
    }

    public function assignRole(Request $request) 
    {
        $request->validate([
            'assigned_userlist' => 'required'
        ]);

        foreach ($request->assigned_userlist as $assigned_user) {
            $user = User::find($assigned_user);
            if ($request->assigned_role != $user->role) {
                if ($user->role == "Developer") {
                    $tickets = Ticket::where('developer_id', $user->id)->get();
                    foreach ($tickets as $ticket) {
                        $ticket->update(['developer_id' => null]);
                    }
                } 

                if ($user->role != null) {
                    $deleted = ProjectPersonnel::where('user_id', $user->id)->delete();
                }
                 
                $user->role = $request->assigned_role;
                $user->save();
                
                Notification::send($user, new RoleAssignmentNotification($request->assigned_role));
            }
        }

        return redirect()->route('roleAssignment');
    }

    public function deleteUser(Request $request) 
    {
        $request->validate([
            'toBeDeleted_userlist' => 'required'
        ]);

        foreach ($request->toBeDeleted_userlist as $toBeDeleted_user) {
            $deleted = User::find($toBeDeleted_user)->delete();
        }

        return redirect()->route('roleAssignment');
    }

    public function userProfile() {
        $user = User::where('id', session('LoggedUserId'))->first();
        
        return view('user_profile')->with(compact('user'));
    }

    public function editUserProfile(Request $request) {
        $user = User::where('id', session('LoggedUserId'))->first();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email'=> [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);
 
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        } 

        $user->first_name = $request->firstname;
        $user->last_name = $request->lastname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->mobile = $request->mobile;
        $user->city = $request->city;
        $user->province = $request->province;
        $user->country = $request->country;
        $query = $user->save();
        
        return view('user_profile')->with(compact('user'));
    }

    public function uploadPicture(Request $request) {
        $user = User::where('id', session('LoggedUserId'))->first();

        $request->validate([
            'picture' => 'required|mimes:jpeg,png,jpg,gif|max:2048'
         ]);

        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::lower($user->first_name)."_".Str::lower($user->last_name).".".$extension;
            $file->storeAs('public\images\user_pictures',$filename);
            $user->picture = $filename;
            $user->save();
        }

        return redirect('userProfile');
    }
}
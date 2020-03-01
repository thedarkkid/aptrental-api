<?php

namespace App\Http\Controllers;

use App\Apartment;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display user listings.
     * @param int $type
     * @return \Illuminate\Http\Response
     */
    public function index($type = null)
    {
        //get users
        $users = ($type == null)? User::orderBy('id', 'DESC')->paginate(7) :User::where('type', $type)->orderBy('id', 'DESC')->paginate(7);

        //return collection of users as a resource
        return UserResource::collection($users);
    }


    /**
     * Store or update a user.
     * @param int $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        //check type of request and create user object
        $user = $request->isMethod('put') ? User::findOrFail($id) : new User;


        //add new password if needed
        if(!$request->isMethod('put')){
            $user->password = Hash::make($request->input('password')); //check Hash::check

        }else{
            //check if user is allowed
            if( !($request->user()->id == $id || $request->user()->type === 3) ){
                $response = ['message' => "not allowed"];
                return response($response, 401);
            }

            //check for duplicate entries
            $checkuser = User::where('email', '=', $request->input('email'))->first();
            if ($checkuser !== null) {
                if($checkuser->id !== $user->id){
                    $response = ['error' => "user with email already exists"];
                    return response($response, 422);
                }
            }
            $checkuser = User::where('username', '=', $request->input('username'))->first();
            if ($checkuser !== null) {
                if($checkuser->id !== $user->id){
                    $response = ['error' => "user with username already exists"];
                    return response($response, 422);
                }
            }
        }



        //add other user object properties
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->type = $request->input('type');

        //save user if transaction goes well
        if($user->save()){
            return new UserResource($user);
        }
    }


    /**
     * Display the specified user.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Get a single user
        $user = User::findOrFail($id);

        //Return single user as a resource
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //check if user is allowed
        if( !($request->user()->id == $id || $request->user()->type === 3) ){
            $response = ['message' => "not allowed"];
            return response($response, 401);
        }
        //Get a single user
        $user = User::findOrFail($id);

        Apartment::where('user_id', $id)->delete();


        if($user->delete()){
            return new UserResource($user);

        }
    }
}

<?php

namespace App\Http\Controllers;

use \App\Apartment;
use App\Http\Resources\Apartment as ApartmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the apartments.
     * @param int status
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $status = null)
    {
        //get apartments based on status, gets all apartments is status isnt provided
        $apartments = ($status == null)? Apartment::orderBy('id', 'DESC')->paginate(7) : Apartment::where('status', $status)->orderBy('id', 'DESC')->paginate(7);

        //filter Apartments
        if($request->query("filtered") == true){
            return $this->getFilteredApartments($request, $status);
        }
//        return $apartments;
        //return collection of apartments as a resource
        return ApartmentResource::collection($apartments);
    }

    public function getFilteredApartments(Request $request, $status=null, $extra=null){
        $filters = $this->returnFilter($request);
        if($status == null){
            if($extra == null){
                $apartments = Apartment::where($filters)->orderBy($request->query('order_by'), $request->query('order_type'))->paginate(7);
            }else{
                $apartments = Apartment::where($filters)->where($extra)->orderBy($request->query('order_by'), $request->query('order_type'))->paginate(7);
            }
        }else{
            if($extra == null){
                $apartments = Apartment::where($filters)->where('status', $status)->orderBy($request->query('order_by'), $request->query('order_type'))->paginate(7);
            }else{
                $apartments = Apartment::where($filters)->where($extra)->orderBy($request->query('order_by'), $request->query('order_type'))->paginate(7);
            }
        }
        return ApartmentResource::collection($apartments);

    }
    public function returnFilter(Request $request){
        $filters = [];
        if($request->query('price_per_month') == 0){
            array_push($filters, ['price_per_month', '<',100 ]);
        }
        if($request->query('price_per_month') > 0){
            array_push($filters, ['price_per_month', '>',$request->query('price_per_month') ]);

        }

        if($request->query('number_of_rooms') == 0){
            array_push($filters, ['number_of_rooms', '<', 3]);

        }
        if($request->query('number_of_rooms') > 0){
            array_push($filters, ['number_of_rooms', '>', $request->query('number_of_rooms')]);
        }

        if($request->query('floor_area_size') > 0){
            array_push($filters, ['floor_area_size', '>', $request->query('floor_area_size')]);

        }

        return $filters;
    }
    /**
     * Display a listing of the apartment from a particular user.
     * @param int id
     * @return \Illuminate\Http\Response
     */
    public function getUserApartments($id)
    {
        //get apartments based on user(author)
        $apartments = Apartment::where('user_id', $id)->orderBy('id', 'DESC')->paginate(7);

        //return collection of apartments as a resource
        return ApartmentResource::collection($apartments);
    }

    /**
     * Display a listing of the apartment from a particular user based on apartment status.
     * @param Request $request
     * @param int $id
     * @param int $status
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getUserApartmentsByStatus(Request $request, $id, $status)
    {
        //get apartments based on user(author)
        $apartments = Apartment::where(['user_id' => $id, 'status' => $status])->orderBy('id', 'DESC')->paginate(7);

        //filter Apartments
        if($request->query("filtered") == true){
            return $this->getFilteredApartments($request, $status, ['user_id' => $id, 'status' => $status]);
        }
        //return collection of apartments as a resource
        return ApartmentResource::collection($apartments);
    }


    /**
     * Store or update apartments
     * @param int id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        //check type of request and create user object
        $apartment = $request->isMethod('put') ? Apartment::findOrFail($id) : new Apartment;

        if($request->isMethod('put')){
            if( !($request->user()->id == $request->input('user_id') || $request->user()->type === 3) ){
                $response = ['message' => "not allowed"];
                return response($response, 401);
            }
        }

//        $validator = Validator::make($request->all(), [
//            'name' => 'required|string',
//            'description' => 'required|string',
//            'address' => 'required|string',
//            'floor_area_size' => 'required|numeric',
//            'price_per_month' => 'required|numeric',
//            'number_of_rooms' => 'required|numeric',
//            'status' => 'required|numeric',
//            'user_id' => 'required|numeric',
//            'longitude' => 'required',
//            'latitude' => 'required',
//        ]);
//
//        if ($validator->fails())
//        {
//            return response(['errors'=>$validator->errors()->all()], 422);
//        }

        //add other user object properties
        $apartment->name = $request->input('name');
        $apartment->description = $request->input('description');
        $apartment->floor_area_size = $request->input('floor_area_size');
        $apartment->price_per_month = $request->input('price_per_month');
        $apartment->number_of_rooms = $request->input('number_of_rooms');
        $apartment->longitude = $request->input('longitude');
        $apartment->latitude = $request->input('latitude');
        $apartment->address = $request->input('address');
        $apartment->user_id = $request->input('user_id');
        $apartment->status = $request->input('status');

        //save user if transaction goes well
        if($apartment->save()){
            return new ApartmentResource($apartment);
        }
    }

    /**
     * Display the specified apartments data.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Get a single user
        $apartment = Apartment::findOrFail($id);

        //Return single user as a resource
        return new ApartmentResource($apartment);
    }

    /**
     * Remove the specified apartments from db.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //Get a single user
        $apartment = Apartment::findOrFail($id);
        if( !($request->user()->id == $apartment->user_id || $request->user()->type === 3) ){
            $response = ['message' => "not allowed"];
            return response($response, 401);
        }
        //Return deleted single article as a resource
        if($apartment->delete()){
            return new ApartmentResource($apartment);
        }
    }

    public function deleteUnusedApartments(){
        $apartments = Apartment::all();

        foreach ($apartments as $apartment){
            $user = \App\User::where('id', '=', $apartment->user_id)->first();
            if ($user === null) {
                $apartment->delete();
            }
        }

    }
}

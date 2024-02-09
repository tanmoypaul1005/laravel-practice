<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller {
    
    function addAddress( Request $request ) {
        try {
            $validator = Validator::make( $request->all(), [
                'title' => 'required',
                'address' => 'required',
            ] );

            if ( $validator->fails() ) {
                return  response()->json( [
                    'status'=>500,
                    'success'=>false,
                    'msg'=>$validator->errors()
                ] );
            }
            
            $address = new Address();
            $address->title = $request->title;
            $address->address = $request->address;

            $address->save();

            return response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$address,
                'message'=>'Address Added Successfully'
            ] );
            
        } catch( Exception $e ) {
            return response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>$e,
                'message'=>'Address Added Fail'
            ] );
        }

    }
}

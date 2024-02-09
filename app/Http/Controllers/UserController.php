<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\PasswordStrength;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller {
    function register( Request $request ) {
        try {
            $validator = Validator::make( $request->all(), [
                'name' => [ 'required', 'string', 'max:255' ],
                'email' => [ 'required', 'string', 'email', 'max:255', 'unique:users' ],
                'password' => [
                    'required',
                    'string',
                    'min:8', // Adjust the minimum length as needed
                    new PasswordStrength(), // Use the custom rule here
                ],
            ] );
            if ( $validator->fails() ) {
                $data = json_decode( $validator->errors(), true );

                if ( isset( $data[ 'name' ][ 0 ] ) ) {
                    return response()->json( [
                        'status'=>500,
                        'success'=>false,
                        'data'=>null,
                        'msg'=>$data[ 'name' ][ 0 ],
                    ] );
                } else if ( isset( $data[ 'email' ][ 0 ] ) ) {
                    return response()->json( [
                        'status'=>500,
                        'success'=>false,
                        'data'=>null,
                        'msg'=>$data[ 'email' ][ 0 ],
                    ] );
                } else if ( isset( $data[ 'password' ][ 0 ] ) ) {
                    return response()->json( [
                        'status'=>500,
                        'success'=>false,
                        'data'=>null,
                        'msg'=>$data[ 'password' ][ 0 ],
                    ] );
                } else {
                    return response()->json( [
                        'status'=>500,
                        'success'=>false,
                        'data'=>null,
                        'msg'=>$validator->errors()
                    ] );
                }
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt( $request->password );
            $user->email_verified_at = $request->email_verified_at;

            $user->save();
            return response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$user,
                'msg'=>$validator->errors()
            ] );

        } catch( \Exception $e ) {
            return response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>$e->getMessage(),
                'msg'=>$validator->errors()
            ] );

        }

    }

    function login( Request $request ) {
        $credentials = $request->only( 'email', 'password' );

        if ( Auth::attempt( $credentials ) ) {
            // Authentication passed, generate and return a token
            // $token = auth()->user()->createToken( 'MyApp' )->accessToken;
            return response()->json( [ 'token' => '333' ], 200 );
        } else {
            // Authentication failed
            return response()->json( [ 'message' => 'Unauthorized' ], 401 );
        }
    }

    function generateOtp( Request $request ) {
        try {
            // $otp = Str::random( 6 );
            $otp = strval( rand( 100000, 999999 ) );
            $user = User::where( 'id', $request->id )->update( [ 'otp'=>$otp ] );
            return response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$otp,
                'msg'=>'OTP sent successfully'
            ] );
        } catch( \Exception $e ) {
            return response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>null,
                'msg'=>'OTP sent fail'
            ] );
        }
    }

    function validateOtp( Request $request ) {
        try {
            $user = User::find($request->id);
            if ( $user->otp == $request->otp ) {
                User::where('id', $request->id)->update(['otp'=>null]);
                return response()->json( [
                    'status'=>200,
                    'success'=>true,
                    'data'=> $user,
                    'msg'=>'OTP is valid.'
                ] );
            }else{
                return response()->json( [
                    'status'=>500,
                    'success'=>false,
                    'data'=>null,
                    'msg'=>'Invalid OTP.'
                ] );
            }
        } catch( \Exception $e ) {
            return response()->json( [
                'status'=>500,
                'success'=>null,
                'msg'=>'OTP validate fail'
            ] );
        }
    }

}

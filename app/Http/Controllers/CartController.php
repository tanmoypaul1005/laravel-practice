<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CartController extends Controller {
    
    function addCart( Request $request ) {
        try {

            $validator = Validator::make( $request->all(), [
                'user_id' => 'required',
                'products_id' => 'required',
      
                'quantity' => 'required'

            ] );
            if ( $validator->fails() ) {
                return response()->json( [
                    'success'=>false,
                    'msg'=>$validator->errors()
                ]);
            }
            $cart = new Cart();
            $cart->user_id = $request->user_id;
            $cart->products_id = $request->products_id;
            $cart->quantity = $request->quantity;
            $cart->save();
            return response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$cart,
                'message'=>'Cart Added Successfully'
            ] );
        } catch ( Exception $e ) {
            return response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>$e,
                'message'=>'Cart Added Fail'
            ] );
        }

    }

    function getCart( Request $request ) {
        try {
            $cart = Cart:: where( 'user_id',  $request->user_id )->get();
            return response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$cart,
                'message'=>'Cart Fetched Successfully'
            ] );
        } catch ( Exception $e ) {
            return response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>$e,
                'message'=>'Cart Fetched Fail'
            ] );
        }
    }

    function updateCartQuantity( Request $request ) {
        try {
            Cart:: where( 'id',  $request->id )->update( [
                'quantity' => $request->quantity
            ] );
            
            $cart=Cart::find($request->id);
            return response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$cart,
                'message'=>'Cart Updated Successfully'
            ] );
        } catch( Exception $e ) {
            return response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>$e,
                'message'=>'Cart Updated Fail'
            ] );
        }
    }
}

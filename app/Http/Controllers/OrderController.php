<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller {

    // add order
    function addOrder( Request $request ) {
        try {

            $validator = Validator::make( $request->all(), [
                'address_id' => 'required',
            ]);

            if ( $validator->fails() ) {
                $error= json_decode( $validator->errors(), true );
                return response()->json( [
                    'status'=>500,
                    'success'=>false,
                    'data'=>null,
                    'message'=>$error['address_id'][0]
                ] );
            }

            foreach ( $request->cart as $item ) {
                $cart = Cart::find( $item );
                $product = Product::find( $cart->products_id );

                $order = new Order();
                $order->quantity = $cart->quantity;
                $order->price = $cart->quantity*$product->price;
                $order->products_id = $cart->products_id;
                $order->address_id = $request->address_id;
                $order->user_id = $cart->user_id;
                $order->status = 'pending';

                $order->save();

                $product->stock = $product->stock - $cart->quantity;
                $product->update();
                $cart->delete();
            }

            return response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>Order:: get(),
                'message'=>'Order Added Successfully'
            ] );
        } catch( Exception $e ) {
            return response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>$e,
                'message'=>'Order Added Fail'
            ] );
        }

    }

    // change order status
    function changeOrderStatus( Request $request ) {

        try {
            $validator = Validator::make( $request->all(), [
                'id' => 'required',
                'status'=>'required',
            ] );

            if ( $validator->fails() ) {
                $data = json_decode( $validator->errors(), true );

                if ( $data[ 'id' ][ 0 ] ) {
                    return response()->json( [
                        'status'=>400,
                        'success'=>false,
                        'data'=>null,
                        'message'=>$data[ 'id' ][ 0 ]
                    ] );
                }

                if ( $data[ 'status' ][ 0 ] ) {
                    return response()->json( [
                        'status'=>400,
                        'success'=>false,
                        'data'=>null,
                        'message'=>$data[ 'status' ][ 0 ]
                    ] );
                }

            }

            if ( !in_array( $request->status, [ 'packed', 'shipped', 'delivered', 'canceled' ] ) ) {
                return  response()->json( [
                    'status'=>500,
                    'success'=>false,
                    'data'=>null,
                    'message'=>'Invalid status'
                ] );
            }

            $order = Order::find( $request->id );

            if ( !$order ) {
                return  response()->json( [
                    'status'=>500,
                    'success'=>false,
                    'data'=>null,
                    'message'=>'Order Not Found'
                ] );
            }
            $order->status = $request->status;
            $order->update();
            $data = Order::with( 'order_products:id,name,price' )->get();

            return  response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$data,
                'message'=>'Order Status Change Fail'
            ] );
        } catch( Exception $e ) {
            return  response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>null,
                'message'=>'Order Status Change Fail'
            ] );
        }
    }

    // get order    
    function getOrder( Request $request ) {
        try {
            $order = Order::where( 'user_id', $request->user_id )->with( 'order_products:id,name,price' )->get();
            $total_price = 0;
            foreach ( $order as $item ) {
                $total_price = $total_price+$item->price;
            }
            return  response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>[
                    'order_list'=>$order,
                    '$total_price'=>$total_price
                ],
                'message'=>'Order List successfully rendered'
            ] );
        } catch( Exception $e ) {
            return  response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>null,
                'message'=>'Order Status Change Fail'
            ] );
        }
    }
}

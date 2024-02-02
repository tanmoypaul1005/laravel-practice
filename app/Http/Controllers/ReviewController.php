<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller {

    //add reviews
    function addReview( Request $request ) {
        try {

            $validator = Validator::make( $request->all(), [
                'products_id'=>'required',
                'rating'=>'required',
            ] );

            if ( $validator->fails() ) {

                if ( $validator->errors()->has( 'products_id' ) ) {
                    return  response()->json( [
                        'status'=>400,
                        'success'=>false,
                        'data'=>null,
                        'message'=>$validator->errors()->first( 'products_id' )
                    ] );
                }
                if ( $validator->errors()->has( 'rating' ) ) {
                    return  response()->json( [
                        'status'=>400,
                        'success'=>false,
                        'data'=>null,
                        'message'=>$validator->errors()->first( 'rating' )
                    ] );
                }
            }

            $product = Product::find( $request->products_id );

            if ( !$product ) {
                return  response()->json( [
                    'status'=>500,
                    'success'=>false,
                    'data'=>null,
                    'message'=>'Product Not Found'
                ] );
            }

            if ( $request->rating > 5 ) {
                return  response()->json( [
                    'status'=>500,
                    'success'=>false,
                    'data'=>null,
                    'message'=>'Rating must be less than 5'
                ] );
            }

            $review = new Review();
            $review->products_id = $request->products_id;
            $review->rating = $request->rating;
            $review->comment = $request->comment;
            $review->save();

            return response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$review,
                'message'=>'Add Review Success'
            ] );

        } catch( Exception $e ) {
            
            return  response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>null,
                'message'=>'Add Review Fail'
            ] );
        }

    }

    //get reviews
    function getReviews( Request $request ) {
        try {
            $reviews = Review::where( 'products_id', $request->products_id )->get();

            $point=0;
            foreach( $reviews as $review ) {
                $point=$point+$review->rating;
            }

            $data=[
                'point'=>$point/$reviews->count(),
                'total'=>$reviews->count(),
                'reviews'=>$reviews,  
            ];


            return  response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$data,
                'message'=>'get Review'
            ] );
        } catch( Exception $e ) {
            return  response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>null,
                'message'=>'get Review Fail'
            ] );
        }
    }

    //edit reviews
    function editReviews( Request $request ) {
        try {
             $validator = Validator::make( $request->all(), [
                 'comment'=>'required',
                 'rating'=>'required',
             ]);

             if( $validator->fails()){

                if($validator->errors()->has( 'comment' )){
                    return  response()->json( [
                        'status'=>500,
                        'success'=>false,
                        'data'=>null,
                        'message'=>$validator->errors()->first( 'comment' )
                    ] );
                }


                if($validator->errors()->has( 'rating' )){
                    return  response()->json( [
                        'status'=>500,
                        'success'=>false,
                        'data'=>null,
                        'message'=>$validator->errors()->first( 'rating' )
                    ] );
                }


             }

             if ( $request->rating > 5 ) {
                return  response()->json( [
                    'status'=>500,
                    'success'=>false,
                    'data'=>null,
                    'message'=>'Rating must be less than 5'
                ] );
            }

            $reviews = Review::where( 'id', $request->id )->update([
                'comment'=>$request->comment,
                'rating'=>$request->rating,
            ]);

            return  response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$reviews,
                'message'=>'get Review'
            ] );
        } catch( Exception $e ) {
            return  response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>null,
                'message'=>'get Review Fail'
            ] );
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductController extends Controller {

    // get product

    function getProduct( Request $request ) {

        try {
            $searchTerm = $request->input( 'search' );
            $status = $request->input( 'status' );

            if ( $status ) {
                // regular_product, featured_product, new_product, popular_product
                if ( !in_array( $status, [ 'regular_product', 'featured_product', 'new_product', 'popular_product' ] ) ) {
                    return  response()->json( [
                        'status'=>500,
                        'success'=>false,
                        'data'=>null,
                        'message'=>'Invalid Status'
                    ] );
                } else {
                    $filter_products = Product::where( 'status', $status )->get();
                    return  response()->json( [
                        'status'=>200,
                        'success'=>true,
                        'data'=>$filter_products,
                        'message'=>'Product filter result fetched'
                    ] );
                }

            }

            if ( $searchTerm ) {
                $products = Product::where( 'name', 'like', "%$searchTerm%" )->get();
                return  response()->json( [
                    'status'=>200,
                    'success'=>true,
                    'data'=>$products,
                    'message'=>'Search Product successfully rendered'
                ] );

            }

            $product = Product::with( 'categories:id,name' )->paginate( 5 );
            return  response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$product,
                'message'=>'Fetch Product list'
            ] );
        } catch( Exception $e ) {
            return  response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>null,
                'message'=>'Fetch Product list Fail'
            ] );
        }

    }

    // add new product

    function addProduct( Request $request ) {
        try {
            $validator = Validator::make( $request->all(), [
                'name' => 'required',
                'price' => 'required',
                'categories_id' => 'required'

            ] );
            if ( $validator->fails() ) {
                return $data[] = [
                    'success'=>false,
                    'msg'=>$validator->errors()
                ];
            }
            $product = new Product();
            $product->name = isset( $request->name );
            $product->price = isset( $request->price );
            $product->categories_id = isset( $request->categories_id );
            $product->save();
            $data[] = [
                'success'=>true,
                'data'=>$product,
                'msg'=>'success'
            ];
        } catch ( \Exception $e ) {
            return  $data[] = [
                'success'=>false,
                'data'=>$product,
                'msg'=>'fail'
            ];
            ;
        }
        return $data;
    }

    // get product details

    function getProductDetails( Request $request ) {
        $product = Product::find( $request->product_id );
        $data[] = [
            'id'=>$product->id,
            'name'=>$product->name,
            'price'=>$product->price,
            'categories'=>Category::find( $product->categories_id )
        ];
        return $data;

    }

    // search product

    function searchProduct( Request $request ) {

        try {
            $searchTerm = $request->input( 'search' );

            $products = Product::where( 'name', 'like', "%$searchTerm%" )->get();

            return  response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$products,
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

    function productPriceFilter( Request $request ) {

        try {

            $validator = Validator::make( $request->all(), [
                'min_price' => 'required',
                'max_price' => 'required'
            ]);

            if ( $validator->fails() ) {
                $error= json_decode( $validator->errors(), true );

                if($error['min_price'][0]){
                    return  response()->json( [
                        'status'=>400,
                        'success'=>false,
                        'data'=>null,
                        'message'=>$error['min_price'][0]
                    ] );
                }

                if($error['max_price'][0]){
                    return  response()->json( [
                        'status'=>400,
                        'success'=>false,
                        'data'=>null,
                        'message'=>$error['max_price'][0]
                    ] );
                }
            }

            $min_price = $request->min_price ? $request->min_price : 0;
            $max_price =  $request->max_price ? $request->max_price : 1000000;

            $products = Product::where( 'price', '>=', $min_price )->where( 'price', '<=', $max_price )->get();

            return  response()->json( [
                'status'=>200,
                'success'=>true,
                'data'=>$products,
                'message'=>'Filter apply Filter apply Successfully'
            ] );
        } catch( Exception $e ) {
            return  response()->json( [
                'status'=>500,
                'success'=>false,
                'data'=>null,
                'message'=>'Filter apply Fail'
            ] );
        }

    }
}

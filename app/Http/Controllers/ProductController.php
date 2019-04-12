<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Product;
use Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('product.product');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allproduct()
    {
        return Product::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $get_id = Product::insertGetId([
            'title' => $request->title,
            'description' => $request->description,
            'created_at' => Carbon::now()
        ]);
        
        if($request->photo) {
            $getphoto = $request->photo;
            $image_parts = explode(";base64,", $getphoto);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            // $image_base64 = base64_decode($image_parts[1]);
            $filename = $get_id . '.' . $image_type;
            Image::make($getphoto)->save(base_path('public/uploads/product/' . $filename), 90);
            // Save image file id name 
            Product::findOrFail($get_id)->update([
                'photo' => $filename,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        Product::findOrFail($id)->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);
        // Get photo name from databse
        $getcurrentphoto = Product::find($request->id)->photo;
        // If Current photo Will not do anything 
        if($request->photo != $getcurrentphoto) {
            if($getcurrentphoto != 'default.png') {
                $delete_old_photo = base_path('public/uploads/product/' . $getcurrentphoto);
                unlink($delete_old_photo);
            }

            $getphoto = $request->photo;
            $image_parts = explode(";base64,", $getphoto);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $filename = $id . '.' . $image_type;
            Image::make($getphoto)->save(base_path('public/uploads/product/' . $filename), 90);
            // Save image file id name 
            Product::findOrFail($id)->update([
                'photo' => $filename,
            ]);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $photo = Product::find($id)->photo;
        $unlink = base_path('public/uploads/product/' . $photo );
        unlink($unlink);
        Product::findOrFail($id)->delete();
        return ["message" => "Product Deleted Successfully"];
    }
}

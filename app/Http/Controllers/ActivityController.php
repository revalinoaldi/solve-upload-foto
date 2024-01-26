<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ImageActivity;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Image;
use Str;


class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validators (request)
        $rules = [
            'imei'          => 'required',
            'name'          => 'required',
            'nik'           => 'required',
            'phone'         => 'required',
            'email'         => 'required:email',
        ];

        $messages = [
            'imei.required'         => 'IMEI is required!',
            'name.required'         => 'Name is required!',
            'nik.required'          => 'NIK is required!',
            'phone.required'        => 'Phone is required!',
            'email.required'        => 'Email is required!',
            'email.email'           => 'The email address is not in the correct format!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = [
                'error_code'    => 400,
                'validator'     => $validator->errors(),
            ];
            Log::error($data);

            return response()->json(false, 'Terjadi Kesalahan!', $data, 400);
        }

        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $strtotime = $now->timestamp;
            $prefix_name = $strtotime . rand(pow(10, 3-1), pow(10, 3)-1);

            $submit = new Activity;

            $submit->imei           = $request->imei;
            $submit->name           = $request->name;
            $submit->nik            = $request->nik;
            $submit->phone          = $request->phone;
            $submit->email          = $request->email;
            $submit->save();

            $response['code']     = 201;
            $response['validation'] = "Terima kasih atas registrasi anda.";
            $response['name']     = $request->name;
            $response['phone']    = $request->phone;
            $response['email']    = $request->email;

            DB::commit();
            return response()->json($response, 201);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            $data = [
                'code' => 400,
                'validation' => $th->getMessage()
            ];
            return response()->json($data, 400);
        }
    }

    public function upload_image(Request $request){
        $rules = [
            'imei'          => 'required',
            'image_receipt' => ['required', File::types(['jpg', 'png'])->min(100)->max(20 * 1024)],
            // 'image_ig'      => 'required'
        ];

        $messages = [
            'imei.required'         => 'IMEI is required!',
            'image_receipt.required'=> 'Receipt Photo is required!',
            // 'image_ig.required'     => 'Photo Post Image Instagram is required!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = [
                'error_code'    => 400,
                'validator'     => $validator->errors(),
            ];
            Log::error($data);

            return response()->json($data, 400);
        }

        $now = Carbon::now();
        $strtotime = $now->timestamp;
        $prefix_name = $strtotime . rand(pow(10, 3-1), pow(10, 3)-1);

        DB::beginTransaction();
        try {
            $data = [];
            if ($request->file('image_receipt')) {
                $extension = $request->file('image_receipt')->getClientOriginalExtension();
                $filenameSimpan = $prefix_name.'_'.$request->imei.'.'.$extension;


                $data['image_receipt'] = $filenameSimpan;
            }else{
                throw new Exception('Failed! Image is empty');
            }

            $data['imei'] = $request->imei;
            ImageActivity::create($data);
            DB::commit();

            $request->image_receipt->move(public_path('assets/images/receipt'), $filenameSimpan);

            $response['code']     = 201;
            $response['validation'] = "Terima kasih atas registrasi anda.";
            $response['imei']     = $request->imei;
            return response()->json($response, 201);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            $data = [
                'code' => 400,
                'validation' => $th->getMessage()
            ];
            return response()->json($data, 400);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        //
    }
}

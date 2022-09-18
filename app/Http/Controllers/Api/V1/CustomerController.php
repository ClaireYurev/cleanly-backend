<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller{

    /* スピードハンターズ  NEW FUNCTION BELOW - UNTESTED YET *CY*/
    /*public function update_cm_firebase_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => "Validator failed with the provided token"], 403);
        }

        DB::table('users')->where('id', $request->user()->id)->update([
            'remember_token' => $request['cm_firebase_token'],
        ]);

        return response()->json(['message' => "Laravel: CM Firebase Token Updated"], 200);
        
    }*/
    
    /* スピードハンターズ  ORIGINAL FUNCTION BELOW *CY*/ 
    public function info(Request $request)
    {
        $data = $request->user();
        
        $data['order_count'] =0;//(integer)$request->user()->orders->count();
        $data['member_since_days'] =(integer)$request->user()->created_at->diffInDays();
        //unset($data['orders']);
        return response()->json($data, 200);
    }
    
    /* スピードハンターズ  ORIGINAL FUNCTION BELOW *CY*/
    public function address_list(Request $request)
    {
        return response()->json(CustomerAddress::where('user_id', $request->user()->id)->latest()->get(), 200);
    }

    /* 用户列表  SUSPECTED SOURCE FUNCTION BELOW *CY*
    public function address_list(Request $request)
    {
        return response()->json(ShippingAddress::where('customer_id', $request->user()->id)->get(), 200);
    }
    */

    /* スピードハンターズ  ORIGINAL FUNCTION BELOW *CY*/ 
    public function add_new_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
          
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => "Error with the address"], 403);
        }


        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->insert($address);
        return response()->json(['message' => trans('messages.successfully_added')], 200);
    }
    /* スピードハンターズ  ORIGINAL FUNCTION BELOW *CY*/ 
    public function update_address(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        /*$point = new Point($request->latitude,$request->latitude);
        $zone = Zone::contains('coordinates', $point)->first();
        if(!$zone)
        {
            $errors = [];
            array_push($errors, ['code' => 'coordinates', 'message' => trans('messages.out_of_coverage')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }*/
        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'zone_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->where('user_id', $request->user()->id)->update($address);
        return response()->json(['message' => trans('messages.updated_successfully'),'zone_id'=>$zone->id], 200);
    }

    /* スピードハンターズ  NEW FUNCTION BELOW - UNTESTED YET *CY*/
    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'phone' => 'required',
        ], [
            'f_name.required' => trans('messages.name_is_required'),
            'phone.required' => trans('messages.phone_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => "Error validating customer during a profile-update request"], 403);
        }

        /* below 'image' code is untested*/
        if ($request->has('image')) {
            $imageName = ImageManager::update('profile/', $request->user()->image, 'png', $request->file('image'));
        } else {
            $imageName = $request->user()->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $request->user()->password;
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'phone' => $request->phone,
            'password' => $pass,
            'updated_at' => now(),
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => trans('messages.successfully_updated')], 200);
    }

}
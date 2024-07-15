<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Posts;
use Illuminate\Http\Request;
use App\Jobs\KlaviyoCreateProfile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Faker\Factory as Faker;

class UserAuthController extends Controller
{

    public function register (Request $request){
       
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        // try {
            // DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            KlaviyoCreateProfile::dispatch($user);
            // dispatch(new KlaviyoCreateProfile($user));
            // DB::commit();
            return response()->json(['message' => 'User Regisered Successfully', 'status' => 200, 'user' => $user]);
        // } catch (\Throwable $th) {
        //     return response()->json(['message' => 'Something went wrong'.$th->getMessage(), 'status' => 500]);
        // }

       
    }

    // public function register(Request $request)
    // {
    //     // Validate the incoming request
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'email' => 'required|email',
    //         'password' => 'required|min:8'
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors());
    //     }
    
    //     try {
    //         DB::beginTransaction();
    
    //         // Create the user
    //         $user = User::create([
    //             'name' => $request->name,
    //             'email' => $request->email,
    //             'password' => Hash::make($request->password),
    //         ]);
    
    //         // Prepare data for Klaviyo
            // $faker = Faker::create();
            // $url = 'https://a.klaviyo.com/api/profiles/';
    
            // $data = [
            //     'data' => [
            //         'type' => 'profile',
            //         'attributes' => [
            //             'email' => $faker->unique()->safeEmail,
            //         // 'email' => $this->user->email,
            //         'phone_number' => $faker->unique()->e164PhoneNumber(),
                    
            //         // 'first_name' => $this->user->name,
            //         'first_name' => $faker->firstName,
            //             'last_name' => $faker->lastName,
            //             'organization' => $faker->company,
            //             'title' => $faker->jobTitle,
            //             'image' => $faker->imageUrl(640, 480, 'people'),
            //             'location' => [
            //                 'address1' => $faker->streetAddress,
            //                 'address2' => '1st floor',
            //                 'city' => $faker->city,
            //                 'country' => 'United States',
            //                 'region' => $faker->stateAbbr,
            //                 'zip' => $faker->postcode,
            //                 'timezone' => $faker->timezone,
            //                 'ip' => $faker->ipv4
            //             ],
            //             'properties' => ['newKey' => 'New Value'],
            //             // 'external_id' => $user->id
            //         ]
            //     ]
            // ];
    
            // // Send the request to Klaviyo
            // $response = Http::withHeaders([
            //     'accept' => 'application/json',
            //     'content-type' => 'application/json',
            //     'Authorization' => 'Klaviyo-API-Key pk_f7db0153df734809b21cd480b9843ba329',
            //     'revision' => '2024-06-15'
            // ])->post($url, $data);
    
    //         if ($response->successful()) {
    //             // Commit the transaction
    //             DB::commit();
    //             return response()->json([
    //                 'message' => 'User registered successfully',
    //                 'status' => 200,
    //                 'user' => $user,
    //                 'klaviyo_response' => $response->json()
    //             ]);
    //         } else {
    //             // Rollback the transaction
    //             DB::rollBack();
    //             return response()->json([
    //                 'message' => 'Failed to create profile on Klaviyo',
    //                 'status' => $response->status(),
    //                 'error' => $response->body()
    //             ]);
    //         }
    
    //     } catch (\Throwable $th) {
    //         // Rollback the transaction on error
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Something went wrong: ' . $th->getMessage(),
    //             'status' => 500
    //         ]);
    //     }
    // }
    
    public function login (Request $request){
        
        $validator = Validator::make($request->all(),[
       'email' => 'required|email|exists:users',
        'password' => 'required'
        ]);  
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'UnAuthenticated User', 'status' => 401]);
        }
        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
        $response = [
            'message' => 'Sign In Successfully',
            'token' => $token,
            'status' => 200
        ];
        return  response()->json($response);
    }


    public function logout()
    {
        auth('api')->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }


 


    public function posts(){
        $posts = Posts::with('comments', 'media')->get();
        return response()->json(['posts' => $posts],200);
    }
    
}

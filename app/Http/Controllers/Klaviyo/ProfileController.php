<?php

namespace App\Http\Controllers\Klaviyo;

use App\Http\Controllers\Controller;
use App\Services\Klaviyo\Profile\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public $data;
    public function __construct(Profile $profile){
        $this->data = $profile;
    }
    public function all_profiles(){
        return response()->json(['data' => $this->data], 200);
    }
}

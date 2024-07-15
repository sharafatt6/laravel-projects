<?php

namespace App\Jobs;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Testing\WithFaker;

class KlaviyoCreateProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public  $user;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


        $faker = Faker::create();
            $url = 'https://a.klaviyo.com/api/profiles/';
    
            $data = [
                'data' => [
                    'type' => 'profile',
                    'attributes' => [
                        // 'email' => $faker->unique()->safeEmail,
                    'email' => $this->user->email,
                    'phone_number' => $faker->unique()->e164PhoneNumber(),
                    
                    'first_name' => $this->user->name,
                    // 'first_name' => $faker->firstName,
                        'last_name' => $faker->lastName,
                        'organization' => $faker->company,
                        'title' => $faker->jobTitle,
                        'image' => $faker->imageUrl(640, 480, 'people'),
                        'location' => [
                            'address1' => $faker->streetAddress,
                            'address2' => '1st floor',
                            'city' => $faker->city,
                            'country' => 'United States',
                            'region' => $faker->stateAbbr,
                            'zip' => $faker->postcode,
                            'timezone' => $faker->timezone,
                            'ip' => $faker->ipv4
                        ],
                        'properties' => ['password' => $this->user->password],
                        'external_id' => $this->user->id
                    ]
                ]
            ];
    
            // Send the request to Klaviyo
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Authorization' => 'Klaviyo-API-Key pk_f7db0153df734809b21cd480b9843ba329',
                'revision' => '2024-06-15'
            ])->post($url, $data); 
            $id = $response['data']['id'];
            $email = $response['data']['attributes']['email'];
            $sub_data = [
                'data' => [
                    "type" => "profile-subscription-bulk-create-job",
                    "attributes" => [
                        "custom_source" => "Marketing Event",
                        "profiles" => [
                            "data" => [
                                [
                                    "type" => "profile",
                                    "id" => $id,
                                    "attributes" => [
                                        "email" => "john.smith@example.com",
                                        "phone_number" => "+15005550006",
                                        "subscriptions" => [
                                            "email" => [
                                                "marketing" => [
                                                    "consent" => "SUBSCRIBED"
                                                ]
                                            ],
                                            "sms" => [
                                                "marketing" => [
                                                    "consent" => "SUBSCRIBED"
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                             ]
                             ],
                           "relationships" =>[
                            "list" =>[
                                    "data" =>[
                                          "type" => "list",
                                        "id" => "RRwXQA"
                                   ]
                            ]
                   ]
                ]
            ];

            $subuUrl = 'https://a.klaviyo.com/api/profile-subscription-bulk-create-jobs';

            $subResponse = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Authorization' => 'Klaviyo-API-Key pk_f7db0153df734809b21cd480b9843ba329',
                'revision' => '2024-06-15'
            ])->post($subuUrl, $sub_data);
   


    }
}

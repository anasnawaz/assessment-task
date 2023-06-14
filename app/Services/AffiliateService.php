<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method
        $isAlreadyExist=User::where('email',$email)->first();
        if($isAlreadyExist){
            throw new AffiliateCreateException('Affiliate with email '.$email.' already exists for the users ');
        }
        $affiliateUser=User::create([
            'email' => $email,
            'name' => $name,
            'type' => User::TYPE_AFFILIATE,
        ]);
        // Generate a discount code for the affiliate using the ApiService
        $discountCode = $this->apiService->createDiscountCode($merchant);
        $affiliate = Affiliate::create([
            'commission_rate' => $commissionRate,
            'user_id'=>$affiliateUser->id,
            'merchant_id'=>$merchant->id,
            'discount_code'=>$discountCode['code']
        ]);
        Mail::to($email)->send(new AffiliateCreated($affiliate));
        return  $affiliate;
    }
}

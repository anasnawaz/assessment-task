<?php

namespace App\Services;

use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function __construct(protected  AffiliateService $affiliateService) {

    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method
        // Check if an order with the same order_id already exists
        $existingOrder = Order::where('external_order_id', $data['order_id'])->first();
        if($existingOrder){
            return;
        }
        $merchant = Merchant::where('domain',$data['merchant_domain'])->first();
        if(!$merchant){
            return;
        }
        $affiliateUserExist = User::where('email', $data['customer_email'])->first();
         if(!$affiliateUserExist){
             $this->affiliateService->register($merchant,$data['customer_email'],$data['customer_name'],0.1);
        }
        $affiliate=Affiliate::where('merchant_id',$merchant->id)->first();
        $affiliateId=$affiliate->id;
        Order::create([
            'merchant_id'=>$merchant->id,
            'affiliate_id'=>$affiliateId,
            'subtotal'=>$data['subtotal_price'],
            'commission_owed'=>$data['subtotal_price']*$affiliate->commission_rate ,
            'external_order_id'=>$data['order_id'],
         ]);

    }
}

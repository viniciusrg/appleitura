<?php

namespace App\Http\Actions\Webhook;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WebhookHandleAction
{
    public function execute($request)
    {
        try {
            // Log::info('Webhook received:', $request->all());
            // dd($request->all());

            $category = Category::where('name', $request->Product['product_name'])->firstOrFail();
            $user = User::where('email', $request->Customer['email'])->firstOrFail();

            $eventType = $request->webhook_event_type;
            switch ($eventType) {
                case 'order_approved':
                    $isEmpty = $user->categories()->where('category_id', $category->id)->get()->isEmpty();
                    if (!$isEmpty) {
                        break;
                    }
                    $user->categories()->attach($category->id);
                    break;
                case 'subscription_renewed':
                    $isEmpty = $user->categories()->where('category_id', $category->id)->get()->isEmpty();
                    if (!$isEmpty) {
                        break;
                    }
                    $user->categories()->attach($category->id);
                    break;
                case 'subscription_late':
                    $user->categories()->detach($category->id);
                    break;
                case 'subscription_canceled':
                    $user->categories()->detach($category->id);
                    break;

                default:
                    return response()->json(['message:' => 'Category not found.'], 404);
            }

            // dd($request->Custumer);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message:' => 'Category or customer email not found.'], 404);
        } catch (\Exception $e) {
            Log::error(['Show user error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Actions\Auth;

use App\Mail\ForgotPasswordToken;
use App\Models\PasswordResetToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordAction
{
    public function execute($request)
    {
        try {

            $newToken = str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);

            $resetToken = PasswordResetToken::where('email', $request->only('email'))->first();
            
            if (!$resetToken) {
                PasswordResetToken::create([
                    'email' => $request->email,
                    'token' => $newToken
                ]);
            }

            $resetToken->update([
                'email' => $request->email,
                'token' => $newToken,
                'created_at' => Carbon::now(),
            ]);

            try{
                $send = Mail::to('viniciusrg10@msn.com', 'Vinicius')->send(
                    new ForgotPasswordToken([
                        'email' => $request->email,
                        'token' => $newToken
                    ])
                );
            } catch (\Exception $e){
                Log::error(['Send email error: ' . $e]);
                return response()->json(['message' => 'Algo deu errado'], 500);
            }

            return ;
        } catch (\Exception $e) {
            Log::error(['Email error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

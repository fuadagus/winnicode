<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->input('email');
        
        // Here you can save to database or send to email service
        // For now, we'll just return success
        
        try {
            // You can implement actual newsletter subscription logic here
            // For example: save to newsletter_subscribers table
            // or integrate with services like Mailchimp, SendinBlue, etc.
            
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Terima kasih! Anda berhasil berlangganan newsletter.'
                ]);
            }
            
            return back()->with('success', 'Terima kasih! Anda berhasil berlangganan newsletter.');
            
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan. Silakan coba lagi.'
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}

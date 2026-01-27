<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class DevPasswordResetController extends Controller
{
    /**
     * Show the latest password reset link from logs (dev only)
     */
    public function showLatestResetLink()
    {
        if (config('app.env') !== 'local') {
            abort(404);
        }

        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            return view('auth.dev-reset-link', [
                'link' => null,
                'message' => 'No log file found.'
            ]);
        }

        $logContent = File::get($logPath);

        // Extract the most recent reset password link
        preg_match_all('/http:\/\/[^\/]+\/reset-password\/[^\s"]+/', $logContent, $matches);

        if (empty($matches[0])) {
            return view('auth.dev-reset-link', [
                'link' => null,
                'message' => 'No password reset link found. Try requesting a password reset first.'
            ]);
        }

        // Get the last match (most recent)
        $latestLink = end($matches[0]);

        // Clean up HTML entities if present
        $latestLink = html_entity_decode($latestLink);

        return view('auth.dev-reset-link', [
            'link' => $latestLink,
            'message' => null
        ]);
    }
}

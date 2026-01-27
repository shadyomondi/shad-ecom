<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing email configuration...\n\n";
echo "Current mail driver: " . config('mail.default') . "\n";
echo "Mail host: " . config('mail.mailers.smtp.host') . "\n";
echo "Mail port: " . config('mail.mailers.smtp.port') . "\n";
echo "Mail from: " . config('mail.from.address') . "\n\n";

try {
    Mail::raw('This is a test email from your Laravel shop application!', function ($message) {
        $message->to('test@example.com')
                ->subject('Test Email - ShopModern');
    });

    echo "✅ Email sent successfully!\n";
    echo "\nIf using Mailtrap: Check your Mailtrap inbox\n";
    echo "If using Gmail: Check test@example.com (or your email)\n";

} catch (Exception $e) {
    echo "❌ Error sending email:\n";
    echo $e->getMessage() . "\n\n";
    echo "Please check your .env mail configuration.\n";
}

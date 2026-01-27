# Email Configuration Guide

## Current Setup
Your password reset feature is **fully functional** but emails are currently being logged to `storage/logs/laravel.log` instead of being sent.

## Development Helper
Visit `/dev/reset-link` to view the latest password reset link from the logs.

## Email Configuration Options

### Option 1: Mailtrap (Recommended for Development)
Mailtrap is a fake SMTP server for testing emails. All emails are captured without being sent.

1. Sign up at https://mailtrap.io (free account)
2. Get your SMTP credentials
3. Update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@shopmodern.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 2: Gmail SMTP
Use Gmail to send real emails (requires app password if 2FA is enabled).

1. Enable 2-Step Verification in your Google Account
2. Generate an App Password: https://myaccount.google.com/apppasswords
3. Update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 3: Keep Using Log (Current Setup)
Continue logging emails for development.

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@shopmodern.com
MAIL_FROM_NAME="${APP_NAME}"
```

Use the dev helper at `/dev/reset-link` to access reset links.

## Testing

After configuring email, test the password reset:

1. Visit `/forgot-password`
2. Enter a registered email address
3. Check your email inbox (or Mailtrap/logs)
4. Click the reset link
5. Set a new password

## Production Setup

For production, use a professional email service:
- **SendGrid** (free tier available)
- **Amazon SES** (pay per email)
- **Mailgun** (free tier available)
- **Postmark** (reliable transactional emails)

## Troubleshooting

### Password reset link not working?
- Ensure `APP_URL` in `.env` matches your local server URL
- Clear cache: `php artisan config:clear`

### Email not sending?
- Check `storage/logs/laravel.log` for errors
- Verify SMTP credentials
- Test connection: `php artisan tinker` then `Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });`

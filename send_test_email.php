<?php
use App\Mail\ExpiryReminderMail;
use App\Models\Wedding;

$w = new Wedding();
$w->id               = 0;
$w->slug             = 'anny-farhan';
$w->bride_name       = 'Anny';
$w->groom_name       = 'Farhan';
$w->template         = 'wedding-luxury';
$w->package          = 'premium';
$w->event_date       = now()->addMonths(1);
$w->trial_expires_at = now()->addDays(2);
$w->notify_email     = null;

$testTo = env('MAIL_TEST_TO', 'test@example.com');
Mail::to($testTo)->send(new ExpiryReminderMail($w, 'Anny'));

echo "✅ Email terkirim ke {$testTo}\n";

$to = $testTo;


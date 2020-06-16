<?php

namespace App\Http\Controllers;

// use Illuminate\Mail\Mailer;
use App\Models\User;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Boolean;

class EmailSender extends Controller {

    public static function sendEmail($id) {

        $user = User::findOrFail($id);
        $data = ['name' => 'Medlog password reset. ($user->firstname) ($user->lastname)', 'body' => 'Your password reset.'];

        // Mailer::send('emails.reset', $data, function ($m) use ($user) {
        //     $m->from('accounts@nativebyte.co.za', 'Medlog Accounts');
        //     // $user->email
        //     $m->to('ngcobox@gmail.com', $user->name)->subject('Your Reminder!');
        // });

        $to      = 'ngcobox@gmail.com';
        $subject = 'Test Email';
        $message = 'This is a test email';
        $headers = 'From: accounts@nativebyte.co.za' . "\r\n" .
        'Reply-To: accounts@nativebyte.co.za' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

        return mail($to, $subject, $message, $headers);
    }
}

<?php

namespace App\Http\Controllers;

// use Illuminate\Mail\Mailer;

class EmailSender extends Controller {

    public static function sendEmail($email, $subject, $message) {

        // Mailer::send('emails.reset', $data, function ($m) use ($user) {
        //     $m->from('accounts@nativebyte.co.za', 'Medlog Accounts');
        //     // $user->email
        //     $m->to('ngcobox@gmail.com', $user->name)->subject('Your Reminder!');
        // });

        $to      = $email;
        $subject = $subject;
        $message = $message;
        $headers = 'From: accounts@nativebyte.co.za' . "\r\n" .
        'Reply-To: accounts@nativebyte.co.za' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

        return mail($to, $subject, $message, $headers);
    }
}

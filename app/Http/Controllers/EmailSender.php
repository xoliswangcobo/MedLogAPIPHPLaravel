<?php

namespace App\Http\Controllers;

use Illuminate\Mail\Mailer;
use App\Models\User;
use Illuminate\Http\Request;

class EmailSender extends Controller {

    public function sendEmailReminder(Request $request, $id) {

        $user = User::findOrFail($id);

        Mailer::send('emails.reminder', ['user' => $user], function ($m) use ($user) {
            $m->from('hello@app.com', 'Your Application');

            $m->to($user->email, $user->name)->subject('Your Reminder!');
        });
    }
}

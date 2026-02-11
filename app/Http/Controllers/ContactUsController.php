<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUsRequest;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;

class ContactUsController extends Controller
{
    /**
     * Show the contact form.
     */
    public function index()
    {
        // If user is not logged in, show a message instead of the form
        if (!Auth::check()) {
            return view('layouts.partials.guest');
        }

        return view('seccion.contactUs', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Store a new contact message.
     */
    public function store(ContactUsRequest $request)
    {
        ContactMessage::create([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Your message has been sent successfully.');
    }

}

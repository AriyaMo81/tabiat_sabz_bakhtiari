<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ContactUs;

class ContactUsController extends Controller
{
    public function index()
    {
        return view('contact_us');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'body' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->withFragment('contact');
        }

        ContactUs::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return redirect()
            ->back()
            ->with('success', 'پیام شما با موفقیت ثبت شد')
            ->withFragment('contact');
    }
}

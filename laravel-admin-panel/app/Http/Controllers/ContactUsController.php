<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUs;

class ContactUsController extends Controller
{
  public function index()
  {
    $messages = ContactUs::all();
    return view('contact.index', compact('messages'));
  }
  public function show(ContactUs $contact)
  {
    $message = $contact;
    return view('contact.show', compact('message'));
  }
  public function destroy(ContactUs $contact)
  {
    $contact->delete();
    return redirect()->route('contact.index')->with('warning', 'اطلاعات مربوط به این کاربری حذف شد');
  }
}

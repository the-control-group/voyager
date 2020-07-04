<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class VoyagerForgottenPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('voyager.guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('voyager::auth.request');
    }

    protected function sendResetLinkResponse($response)
    {
        return back()->with('status', trans('voyager::auth.password_sent'));
    }
}

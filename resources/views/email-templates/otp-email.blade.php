@extends('email-templates.layout')

@section('content')
    <h4 class="mb-4">OTP Verification</h4>

    <p>Hello <strong>{{ $data['user'] }}</strong>,</p>

    <p>Please use the following One-Time Password (OTP) to verify:</p>

    <h2 class="display-4 text-primary my-4">{{ $data['otp'] }}</h2>

    <p class="text-muted">This code will expire in 5 minutes.</p>

    <p>Thank you!</p>
@endsection

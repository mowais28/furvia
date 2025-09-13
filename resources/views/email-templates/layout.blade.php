<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f0f9;
            font-family: 'Arial', sans-serif;
            color: #212529;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(128, 0, 128, 0.2);
        }

        .header {
            background: linear-gradient(135deg, #6a0dad, #a44cd1);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }

        .header img {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px 20px;
            font-size: 16px;
            line-height: 1.6;
        }

        .footer {
            background-color: #f0e6f6;
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #888;
        }

        .footer a {
            color: #6a0dad;
            text-decoration: none;
        }

        /* Typography */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #4b0c77;
            margin-top: 0;
        }

        p {
            margin: 0 0 15px;
        }

        b,
        strong {
            font-weight: bold;
            color: #4b0c77;
        }

        a {
            color: #6f42c1;
            text-decoration: underline;
        }

        a:hover {
            color: #5a32a3;
        }

        .btn {
            display: inline-block;
            font-weight: 500;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            background-color: #6f42c1;
            border: 1px solid #6f42c1;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        /* Responsive tweaks */
        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
                margin: 10px;
            }

            .content,
            .footer,
            .header {
                padding: 20px !important;
            }

            .btn {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>

    <div class="email-container">
        <div class="header">
            {{-- <img src="{{ asset('app-assets/logo.png') }}" alt="Logo"> --}}
            <img src="https://i.ibb.co/xKPTScvk/logo-fotor-bg-remover-202509135264.png" alt="Logo">
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.<br>
        </div>
    </div>

</body>

</html>

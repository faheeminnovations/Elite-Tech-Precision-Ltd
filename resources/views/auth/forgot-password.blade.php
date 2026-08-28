<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EliteFlow — Forgot Password | Elite Tech Precision Ltd</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:#0B2545;
            --steel:#13315C;
            --steel-light:#2E4C7A;
            --orange:#FF6B35;
            --orange-dim:#FFE4D6;
            --paper:#F3F5F8;
            --card:#FFFFFF;
            --line:#E2E7EF;
            --ink:#0B2545;
            --ink-soft:#5B6B85;
            --danger:#C0392B;
            --danger-bg:#FCE7E4;
            --success:#1E8E5A;
            --mono:'JetBrains Mono', monospace;
            --disp:'Barlow Condensed', sans-serif;
            --body:'Inter', sans-serif;
        }
        * { box-sizing: border-box; }
        body {
            margin:0;
            font-family:var(--body);
            background: url('/images/background.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            color:var(--ink);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(11, 37, 69, 0.9), rgba(255, 107, 53, 0.25));
            z-index: -1;
        }
        .login-card {
            width:min(430px,100%);
            background:#fff;
            border-radius:18px;
            padding:34px;
            box-shadow:0 24px 70px rgba(0,0,0,.25);
        }
        .login-logo {
            display:flex;
            align-items:center;
            gap:12px;
            margin-bottom:24px;
        }
        .brand-mark {
            width:44px;
            height:44px;
            background:var(--orange);
            border-radius:6px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-family:var(--disp);
            font-weight:700;
            color:var(--navy);
            font-size:21px;
        }
        .login-title {
            font-family:var(--disp);
            font-size:29px;
            font-weight:700;
            color:var(--navy);
        }
        .login-sub {
            font-size:12px;
            color:var(--ink-soft);
            margin-top:-4px;
        }
        .form-label {
            font-size:12px;
            font-weight:600;
            color:var(--ink-soft);
        }
        .form-control {
            padding:11px 12px;
            border-radius:9px;
            border:1px solid var(--line);
        }
        .form-control:focus {
            border-color: rgba(255, 107, 53, 0.7);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.15);
        }
        .btn-ef-primary {
            background:var(--orange);
            border:none;
            color:#1a1300;
            font-weight:600;
            font-size:13px;
            padding:8px 16px;
            border-radius:8px;
            width:100%;
            padding:0.9rem 1rem;
        }
        .btn-ef-primary:hover {
            background:#ff5b1f;
            color:#1a1300;
        }
        .login-error {
            display:none;
            font-size:12px;
            color:var(--danger);
            background:var(--danger-bg);
            padding:9px 11px;
            border-radius:8px;
            margin-bottom:12px;
        }
        .success-message {
            font-size:12px;
            color:var(--success);
            background:#e8f5e9;
            padding:9px 11px;
            border-radius:8px;
            margin-bottom:12px;
        }
        .field-wrap { margin-bottom: 1rem; }
        .muted-link {
            color:var(--steel);
            text-decoration:none;
        }
        .muted-link:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="brand-mark">ET</div>
            <div>
                <div class="login-title">EliteFlow</div>
                <div class="login-sub">Precision Engineering & Maintenance Solutions</div>
            </div>
        </div>

        @if ($errors->any())
            <div class="login-error" style="display:block;">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="success-message">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-4 text-sm" style="color:var(--ink-soft);">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </div>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field-wrap">
                <label class="form-label" for="email">Email address</label>
                <input class="form-control" id="email" type="email" name="email" :value="old('email')" required autofocus>
            </div>

            <button class="btn btn-ef-primary" type="submit"><i class="bi bi-envelope me-1"></i> Email Password Reset Link</button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="muted-link">Back to login</a>
        </div>
    </div>
</body>
</html>

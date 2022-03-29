<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="author" content="株式会社日比谷花壇">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta name="format-detection" content="telephone=no,address=no,email=no" />
    <meta
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"
        id="viewport"
        name="viewport"
    />
    <title>@yield('title')</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/web-view/documents/policies.css') }}" />
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
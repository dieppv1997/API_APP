<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/web-view/picture-book/reset.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/web-view/picture-book/flickity.min.css') }}" />
    <link rel="stylesheet" href="http://mplus-webfonts.sourceforge.jp/mplus_webfonts.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/web-view/picture-book/common.css')  }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/web-view/picture-book/picturebook.css') }}" />

    <script type="text/javascript" src="{{ asset('assets/js/web-view/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/web-view/pickturebook.js') }}"></script>
</head>
<body>
    @yield('content')
</body>
</html>
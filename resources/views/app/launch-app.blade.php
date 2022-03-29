<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HANANOHI</title>
    <script>
        window.location = "{{ $rootUrl }}?token={{$token}}&email={{$email}}";
    </script>
</head>
<body style="margin: 0; height: 100vh;">
    <div style="text-align: center; padding-top: 150px;">
        <p style="margin-top: 0; padding: 0 20px;">The Hananohi App now available in Appstore and Google Play</p>
        <p>
            <a href="{{ $iosAppUrl }}">
                <img src="{{asset('/assets/images/appstore-button.png')}}"  alt="Hananohi" style="width: 200px;" />
            </a>
        </p>
        <p>
            <a href="{{ $androidAppUrl }}">
                <img src="{{asset('/assets/images/google-play-button.png')}}"  alt="Hananohi" style="width: 233px;" />
            </a>
        </p>
    </div>
</body>
</html>

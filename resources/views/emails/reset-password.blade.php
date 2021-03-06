@extends('emails.template')

@section('content')
    <hr>
    <p>
        このメッセージはハナノヒの登録システムより自動送信されています。
        <br>このままご返信いただいてもお答えできませんのでご了承ください。
    </p>
    <hr>
    <p>{{ $mailData['nickname'] }}様</p>

    <p>パスワード変更を受け付けました。
    <br>下記URLへ「1時間以内」にアクセスしパスワードの変更を完了させて下さい。</p>

    <p><a href="{{ $mailData['urlEncode'] }}">{{ $mailData['urlPlaintext'] }}</a></p>

    <p>※当メール送信後、1時間を超過しますと、セキュリティ保持のため有効期限切れとなります。
    <br>　その場合は再度、最初からお手続きをお願い致します。</p>

    <p>※お使いのメールソフトによってはURLが途中で改行されることがあります。
    <br>　その場合は、最初の「https://」から末尾の英数字までをブラウザに
    <br>　直接コピー＆ペーストしてアクセスしてください。</p>

    <p>※当メールに心当たりの無い場合は、誠に恐れ入りますが
    <br>　破棄して頂けますよう、よろしくお願い致します。</p>
@endsection

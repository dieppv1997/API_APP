@extends('web-view.picture-book_layout')
@section('content')
    <section class="contenthead">
        <img src="{{ asset('assets/images/web-view/picturebook/head.jpg') }}" alt="">
        <section class="flowerdetail">
            <div class="flowerdetail__name">アオモジ</div>
            <div class="flowerdetail__word">花言葉：無言の愛情</div>
            <div class="flowerdetail__birth">誕生花：12月27日</div>
            <div class="flowerdetail__image">
                <img src="{{ asset('assets/images/web-view/picturebook/f1.jpg') }}" alt="">
            </div>
            <div class="flowerdetail__text">
                同じクスノキ科のクロモジ属クロモジに対し、枝が緑色を帯びているところから「アオモジ」と呼ばれ、爪楊枝の材料とされます。成熟した果実は、レモンのような香りと辛味があることから「ショウガノキ」の呼び名もあります。
            </div>
            <div id="post_del" class="flowerdetail__detail">
                <a class="btn pk">お気に入りに登録</a>
            </div>
            <div class="aiueo-btn">
                <a class="btn wh">花図鑑TOPに戻る</a>
            </div>
        </section>
    </section>
@endsection
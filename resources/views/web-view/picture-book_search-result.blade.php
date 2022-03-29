@extends('web-view.picture-book_layout')
@section('content')
    <section class="contenthead">
        <img src="{{ asset('assets/images/web-view/picturebook/head.jpg') }}" alt="">
        <section class="flowertable">
            <div class="flowertable__title">
                <span>あ</span>
                <span class="sub">検索結果10件</span>
            </div>
            <div class="flowertable__content">
                <div class="flowertable__left">
                    <ul class="flowertable__list">
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a href="{{ route('picture-book-detail') }}">
                                <img src="{{ asset('assets/images/web-view/picturebook/f1.jpg') }}" alt="アオモジ">
                                <span>アオモジ</span>
                            </a>
                        </li>
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f3.jpg') }}" alt="アキイロアジサイ">
                                <span>アキイロアジサイ</span>
                            </a>
                        </li>
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f5.jpg') }}" alt="アジサイ　ブルー">
                                <span>アジサイ　ブルー</span>
                            </a>
                        </li>
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f7.jpg') }}" alt="アジサイ　ムラサキ">
                                <span>アジサイ　ムラサキ</span>
                            </a>
                        </li>
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f9.jpg') }}" alt="アスター　ブルー">
                                <span>アスター　ブルー</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="flowertable__right space">
                    <ul class="flowertable__list">
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f2.jpg') }}" alt="アガパンサス">
                                <span>アガパンサス</span>
                            </a>
                        </li>
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f4.jpg') }}" alt="アジサイ　ピンク">
                                <span>アジサイ　ピンク</span>
                            </a>
                        </li>
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f6.jpg') }}" alt="アジサイ　ホワイト">
                                <span>アジサイ　ホワイト</span>
                            </a>
                        </li>
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f8.jpg') }}" alt="アスター 　ピンク">
                                <span>アスター 　ピンク</span>
                            </a>
                        </li>
                        <li class="flowertable__list-item">
                            <a class="heart"></a>
                            <a>
                                <img src="{{ asset('assets/images/web-view/picturebook/f10.jpg') }}" alt="アスター　ホワイト">
                                <span>アスター　ホワイト</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="pager">
                <ul class="pagination">
                    <li class="active"><a>1</a></li>
                </ul>
            </div>
            <div class="flowertable-btn"><a class="btn wh">花図鑑TOPに戻る</a></div>
        </section>
    </section>
@endsection
@extends('web-view.picture-book_layout')
@section('content')
    <section class="contenthead">
        <img src="{{ asset('assets/images/web-view/picturebook/head.jpg') }}" alt="">
        <div class="content_description-head">
            <img src="{{ asset('assets/images/web-view/picturebook/howtouse.svg') }}">
        </div>
        <div class="content_description">季節に紐づいた365日の誕生花と花言葉を、イラストレーター・鈴野麻衣さん描き下ろしのイラストとともに日替わりでご紹介。<br>
            お店で見かけて気になったお花について、名前や色、シーン、シーズンなどから検索して調べることができます。</div>
    </section>
    <section class="search">
        <div class="flowerdetail__title">
            <img src="{{ asset('assets/images/web-view/picturebook/content_head_searchname.svg') }}">
        </div>
        <div class="search__info-text">「バラ」「ガーベラ」など調べたいお花の名前を入力して検索できます。<br>
            またお花の名前の50音からも調べることができます。</div>
        <div class="search__name">
            <form action="">
                <label class="search__name-plant" for="plant">植物名で検索</label>
                <div class="search__name-form">
                    <input title="serachform" id="plant" name="plant" type="text" placeholder="植物名を入力（カタカナ２文字）">
                    <button>
                        <img src="{{ asset('assets/images/web-view/common/i_search.svg') }}" alt="">
                    </button>
                </div>
            </form>
        </div>
        <div class="search__aiueo">
            <div class="search__name-aiueo">50音で検索</div>
            <ul class="search__aiueo-list">
                <li class="search__aiueo-item"><a href="{{ route('picture-book-search') }}">あ</a></li>
                <li class="search__aiueo-item"><a>い</a></li>
                <li class="search__aiueo-item"><a>う</a></li>
                <li class="search__aiueo-item"><a>え</a></li>
                <li class="search__aiueo-item"><a>お</a></li>
                <li class="search__aiueo-item"><a>か</a></li>
                <li class="search__aiueo-item"><a>き</a></li>
                <li class="search__aiueo-item"><a>く</a></li>
                <li class="search__aiueo-item"><a>け</a></li>
                <li class="search__aiueo-item"><a>こ</a></li>
                <li class="search__aiueo-item"><a>さ</a></li>
                <li class="search__aiueo-item"><a>し</a></li>
                <li class="search__aiueo-item"><a>す</a></li>
                <li class="search__aiueo-item"><a>せ</a></li>
                <li class="search__aiueo-item"><a>そ</a></li>
                <li class="search__aiueo-item"><a>た</a></li>
                <li class="search__aiueo-item"><a>ち</a></li>
                <li class="search__aiueo-item"><a>つ</a></li>
                <li class="search__aiueo-item"><a>て</a></li>
                <li class="search__aiueo-item"><a>と</a></li>
                <li class="search__aiueo-item"><a>な</a></li>
                <li class="search__aiueo-item"><a>に</a></li>
                <li class="search__aiueo-item"><a>ぬ</a></li>
                <li class="search__aiueo-item"><a>ね</a></li>
                <li class="search__aiueo-item"><a>の</a></li>
                <li class="search__aiueo-item"><a>は</a></li>
                <li class="search__aiueo-item"><a>ひ</a></li>
                <li class="search__aiueo-item"><a>ふ</a></li>
                <li class="search__aiueo-item"><a>へ</a></li>
                <li class="search__aiueo-item"><a>ほ</a></li>
                <li class="search__aiueo-item"><a>ま</a></li>
                <li class="search__aiueo-item"><a>み</a></li>
                <li class="search__aiueo-item"><a>む</a></li>
                <li class="search__aiueo-item"><a>め</a></li>
                <li class="search__aiueo-item"><a>も</a></li>
                <li class="search__aiueo-item"><a>や</a></li>
                <li class="search__aiueo-item"></li>
                <li class="search__aiueo-item"><a>ゆ</a></li>
                <li class="search__aiueo-item"></li>
                <li class="search__aiueo-item"><a>よ</a></li>
                <li class="search__aiueo-item"><a>ら</a></li>
                <li class="search__aiueo-item"><a>り</a></li>
                <li class="search__aiueo-item"><a>る</a></li>
                <li class="search__aiueo-item"><a>れ</a></li>
                <li class="search__aiueo-item"><a>ろ</a></li>
                <li class="search__aiueo-item"><a>わ</a></li>
            </ul>
            <div class="search__show-more">さらに表示</div>
        </div>
        <div class="search__day">

            <div class="flowerdetail__title">
                <img src="{{ asset('assets/images/web-view/picturebook/content_head_searchdate.svg') }}">
            </div>
            <div class="search__info-text">大切な方の誕生日や記念日などからその日のお花を検索できます。</div>
            <div class="search__select">
                <select class="search__select-month" name="month">
                    <option value="1">1月</option>
                    <option value="2">2月</option>
                    <option value="3">3月</option>
                    <option value="1">4月</option>
                    <option value="2">5月</option>
                    <option value="3">6月</option>
                    <option value="1">7月</option>
                    <option value="2">8月</option>
                    <option value="3">9月</option>
                    <option value="1">10月</option>
                    <option value="2">11月</option>
                    <option value="3">12月</option>
                </select>
                <select class="search__select-day" name="day">
                    <option value="1">1日</option>
                    <option value="2">2日</option>
                    <option value="3">3日</option>
                    <option value="4">4日</option>
                    <option value="5">5日</option>
                    <option value="6">6日</option>
                    <option value="7">7日</option>
                    <option value="8">8日</option>
                    <option value="9">9日</option>
                    <option value="10">10日</option>
                    <option value="11">11日</option>
                    <option value="12">12日</option>
                    <option value="13">13日</option>
                    <option value="14">14日</option>
                    <option value="15">15日</option>
                    <option value="16">16日</option>
                    <option value="17">17日</option>
                    <option value="18">18日</option>
                    <option value="19">19日</option>
                    <option value="20">20日</option>
                    <option value="21">21日</option>
                    <option value="22">22日</option>
                    <option value="23">23日</option>
                    <option value="24">24日</option>
                    <option value="25">25日</option>
                    <option value="26">26日</option>
                    <option value="27">27日</option>
                    <option value="28">28日</option>
                    <option value="29">29日</option>
                    <option value="30">30日</option>
                    <option value="31">31日</option>
                </select>
            </div>
            <div class="search__select-btn"><a class="btn pk" href="#">探す</a></div>
        </div>
    </section>

    <section class="search__color">
        <div class="flowerdetail__title">
            <img src="{{ asset('assets/images/web-view/picturebook/content_head_searchcolor.svg') }}">
        </div>
        <div class="search__info-text">お好きな色からお花を探すことができます。</div>
        <ul class="search__color-list">
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_re.png') }}" alt="">レッド系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_or.png') }}" alt="">オレンジ系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_ye.png') }}" alt="">イエロー系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_gr.png') }}" alt="">グリーン系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_blu.png') }}" alt="">ブルー系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_pu.png') }}" alt="">ムラサキ系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_pk.png') }}" alt="">ピンク系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_wh.png') }}" alt="">ホワイト系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_bla.png') }}" alt="">ブラウン系
                </a>
            </li>
            <li class="search__color-item">
                <a class="btn">
                    <img src="{{ asset('assets/images/web-view/picturebook/i_oth.png') }}" alt="">その他
                </a>
            </li>
        </ul>
    </section>

    <section class="search__scene">
        <div class="flowerdetail__title">
            <img src="{{ asset('assets/images/web-view/picturebook/content_head_searchmean.svg') }}">
        </div>
        <div class="search__info-text">いまの気分やお花を買うシチュエーションからお花を探すことができます。</div>
        <ul class="search__scene-list">
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_love.svg') }}" alt="">
                    <span>恋愛</span>
                </a>
            </li>
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_thanks.svg') }}" alt="">
                    <span>希望・成就</span>
                </a>
            </li>
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_visit.svg') }}" alt="">
                    <span>お祝い</span>
                </a>
            </li>
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_cheer.svg') }}" alt="">
                    <span>なりたい姿</span>
                </a>
            </li>
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_thanks.svg') }}" alt="">
                    <span>お礼</span>
                </a>
            </li>
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_love.svg') }}" alt="">
                    <span>癒やし</span>
                </a>
            </li>
        </ul>
    </section>

    <section class="search__scene">
        <div class="flowerdetail__title">
            <img src="{{ asset('assets/images/web-view/picturebook/content_head_searchseason.svg') }}">
        </div>
        <div class="search__info-text">切り花がお花屋さんに出回る季節から探すことができます。</div>
        <ul class="search__scene-list">
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_spring.svg') }}" alt="">
                    <span class="season">春</span>
                </a>
            </li>
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_summer.svg') }}" alt="">
                    <span class="season">夏</span>
                </a>
            </li>
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_fall.svg') }}" alt="">
                    <span class="season">秋</span>
                </a>
            </li>
            <li class="search__scene-item">
                <a class="btn pk">
                    <img src="{{ asset('assets/images/web-view/picturebook/btn_winter.svg') }}" alt="">
                    <span class="season">冬</span>
                </a>
            </li>
        </ul>
    </section>
@endsection
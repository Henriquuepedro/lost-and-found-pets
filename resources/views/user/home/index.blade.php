@extends('user.welcome')

@section('title', $settings['name_store'])

@section('js')
    <script type="text/javascript">
        $(function() {
            $('.carousel').carousel()
        });
    </script>
@endsection

@section('css')
@endsection

@section('body')
    <div class="content">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @foreach($arrBanners as $key => $_)
                    <li data-target="#carouselExampleIndicators" data-slide-to="{{ $key }}" class="{{ $key === 0 ? 'active' : '' }}"></li>
                @endforeach
            </ol>
            <div class="carousel-inner">
                @foreach($arrBanners as $key => $banner)
                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                        <img class="d-block w-100" src="{{ $banner['path'] }}" alt="" />
                    </div>
                @endforeach
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
    <div class="main">
        <div class="wrap">
            <div class="content-top">
                <div class="section group">
                    <div class="col_1_of_3 span_1_of_3">
                        <div class="thumb-pad2">
                            <div class="thumbnail">
                                <h4>Lorem ipsum dolor sit</h4>
                                <figure><img src="{{ asset('user/images/pic.jpg') }}" alt=""></figure>
                                <div class="caption">
                                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis</p>
                                    <a href="#" class="btn-default btn1">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col_1_of_3 span_1_of_3">
                        <div class="thumb-pad2">
                            <div class="thumbnail">
                                <h4>Lorem ipsum dolor sit</h4>
                                <figure><img src="{{ asset('user/images/pic1.jpg') }}" alt=""></figure>
                                <div class="caption">
                                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis</p>
                                    <a href="#" class="btn-default btn1">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col_1_of_3 span_1_of_3">
                        <div class="thumb-pad2">
                            <div class="thumbnail">
                                <h4>Lorem ipsum dolor sit</h4>
                                <figure><img src="{{ asset('user/images/pic2.jpg') }}" alt=""></figure>
                                <div class="caption">
                                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis</p>
                                    <a href="#" class="btn-default btn1">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="content-middle">
                <div class="section group example">
                    <div class="col_1_of_2 span_1_of_2">
                        <img src="{{ asset('user/images/pic4.jpg') }}" alt=""/>
                        <h4>Lorem Ipsum is simply dummy text </h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur sed do eiusmod tempor incididunt ut labore et dolore magna aliqua sed do eiusmod tempor incididunt ut labore et dolore magna aliqua velit .</p>
                        <a href="#" class="btn-default btn1">Read more</a>
                    </div>
                    <div class="col_1_of_2 span_1_of_2">
                        <div class="listview_1_of_2 images_1_of_2">
                            <div class="listimg listimg_2_of_1">
                                <img src="{{ asset('user/images/pic5.jpg') }}">
                            </div>
                            <div class="text list_2_of_1">
                                <h3><span>Consectetur</span>  adipisicing elit</h3>
                                <h4>Sed ut perspiciatis undeaccusantium .</h4>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed.</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="listview_1_of_2 images_1_of_2">
                            <div class="listimg listimg_2_of_1">
                                <img src="{{ asset('user/images/pic6.jpg') }}">
                            </div>
                            <div class="text list_2_of_1">
                                <h3><span>Consectetur</span>  adipisicing elit</h3>
                                <h4>Sed ut perspiciatis undeaccusantium .</h4>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed.</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="listview_1_of_2 images_1_of_2">
                            <div class="listimg listimg_2_of_1">
                                <img src="{{ asset('user/images/pic7.jpg') }}">
                            </div>
                            <div class="text list_2_of_1">
                                <h3><span>Consectetur</span>  adipisicing elit</h3>
                                <h4>Sed ut perspiciatis undeaccusantium .</h4>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed.</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="listview_1_of_last images_1_of_2">
                            <div class="listimg listimg_2_of_1">
                                <img src="{{ asset('user/images/pic3.jpg') }}">
                            </div>
                            <div class="text list_2_of_1">
                                <h3><span>Consectetur</span>  adipisicing elit</h3>
                                <h4>Sed ut perspiciatis undeaccusantium .</h4>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed.</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

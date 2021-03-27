@extends('user.welcome')

@section('title', 'Sobre nós')

@section('js')
@endsection

@section('css')
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="about">
                <div class="about-top row">
                    <div class="col-md-9">
                        <h3>About</h3>
                        <div class="about-img">
                            <img src="{{ asset('user/images/pic8.jpg') }}" alt="">
                        </div>
                        <div class="about-desc">
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate.,</p>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="col-md-3">
                        <h3>Recent Comments</h3>
                        <ul class="comments-custom unstyled">
                            <li class="comments-custom_li">
                                <div class="icon"></div>
                                <div class="right-text">
                                    <h4 class="comments-custom_h">admin:</h4>
                                    <div class="comments-custom_txt">
                                        <a href="#" title="Go to this comment">Sed ut perspiciatis magna ...</a>
                                    </div>
                                    <time>November 16,2013</time>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <li class="comments-custom_li">
                                <div class="icon"></div>
                                <div class="right-text">
                                    <h4 class="comments-custom_h">admin:</h4>
                                    <div class="comments-custom_txt">
                                        <a href="#" title="Go to this comment">Sed ut perspiciatis magna ...</a>
                                    </div>
                                    <time>November 16,2013</time>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <li class="comments-custom_li">
                                <div class="icon"></div>
                                <div class="right-text">
                                    <h4 class="comments-custom_h">admin:</h4>
                                    <div class="comments-custom_txt">
                                        <a href="#" title="Go to this comment">Sed ut perspiciatis magna ...</a>
                                    </div>
                                    <time>November 16,2013</time>
                                </div>
                                <div class="clear"></div>
                            </li>
                        </ul>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="about-bottom">
                    <div class="about-topgrids">
                        <div class="about-topgrid1">
                            <h3>Who We Are</h3>
                            <img src="{{ asset('user/images/pic9.jpg') }}" title="name">
                            <h5>LOREM IPM DOLOR SIT AMET, CONSECTETUER ADIPISCING ELIT. PRAESENT VESTIBULUM.</h5>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. Aeonummy hendrerit mauris..</p>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. Aeonummy hendrerit mauris.Lorem ipsum</p>
                        </div>
                    </div>
                    <div class="about-histore">
                        <h3>History</h3>
                        <div class="historey-lines">
                            <ul>
                                <li><span>2010 &nbsp;-</span></li>
                                <li><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. Aeonummy hendrerit..</p></li>
                                <div class="clear"> </div>
                            </ul>
                        </div>
                        <div class="historey-lines">
                            <ul>
                                <li><span>2010 &nbsp;-</span></li>
                                <li><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. Aeonummy hendreri.</p></li>
                                <div class="clear"> </div>
                            </ul>
                        </div>
                        <div class="historey-lines">
                            <ul>
                                <li><span>2010 &nbsp;-</span></li>
                                <li><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. Aeonummy hendreri..</p></li>
                                <div class="clear"> </div>
                            </ul>
                        </div>
                        <div class="historey-lines">
                            <ul>
                                <li><span>2010 &nbsp;-</span></li>
                                <li><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. Aeonummy hendrerit mauris. Phasellus porta. Fusce suscipit.</p></li>
                                <div class="clear"> </div>
                            </ul>
                        </div>
                        <div class="clear"> </div>
                    </div>
                    <div class="about-services">
                        <h3>Why Choose Us</h3>
                        <div class="questions">
                            <h4><img src="{{ asset('user/images/marker2.png') }}" alt=""/>&nbsp;What is Lorem Ipsum?</h4>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 150 Phasellus porta. Fusce suscipit.</p>
                        </div>
                        <div class="questions">
                            <h4><img src="{{ asset('user/images/marker2.png') }}" alt=""/>&nbsp;What is Lorem Ipsum?</h4>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                        </div>
                        <div class="questions">
                            <h4><img src="{{ asset('user/images/marker2.png') }}" alt=""/>&nbsp;What is Lorem Ipsum?</h4>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry..</p>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

@endsection

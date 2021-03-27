@extends('user.welcome')

@section('title', 'Contato')

@section('js')
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
    <!-- MarkerCluster -->
    <script>
        // const address = $('#address_get_location').val();
        //
        // $.get(`https://dev.virtualearth.net/REST/v1/Locations?query=${address}&key=AjbvryntfGTZPmRNwuoASXpWwlXemnx7MUE3p4ICaCOmGCrTVf07iWw20Ad-t2oR`, latLng => {
        //     latLng = latLng.resourceSets[0].resources[0].geocodePoints[0].coordinates;
        //     const lat = latLng[0];
        //     const lng = latLng[1];
        //
        //     const mymap = L.map('map').setView([lat, lng], 13);
        //
        //     L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        //         maxZoom: 18,
        //         attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
        //         id: 'mapbox/streets-v11',
        //         tileSize: 512,
        //         zoomOffset: -1
        //     }).addTo(mymap);
        //
        //     L.marker([lat, lng]).addTo(mymap)
        //         .bindPopup("<div style='width: 100%; text-align:center'><b style='font-size: 18px'>SoLove!</b><br /><span style='font-size: 17px'>Venha conhecer nossa loja!</span> <br/> <br/> <a href='https://www.google.com/maps?q="+address+"' target='_blank'>ABRIR LOCALIZAÇÃO</a></div>", {
        //             maxWidth : 400
        //         }).openPopup();
        // });



    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="">
@endsection

@section('body')


    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0"><span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span> <span>Contato <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Contato</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="wrapper px-md-4">
                        <div class="row mb-5">
{{--                            <div class="col-md-3">--}}
{{--                                <div class="dbox w-100 text-center">--}}
{{--                                    <div class="icon d-flex align-items-center justify-content-center">--}}
{{--                                        <span class="fa fa-map-marker"></span>--}}
{{--                                    </div>--}}
{{--                                    <div class="text">--}}
{{--                                        <p><span>Endereço:</span> <br/> <a href="https://www.google.com/maps?q={{$addressSearch}}" target="_blank">{!! $addressView !!}</a></p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="col-md-4">
                                <div class="dbox w-100 text-center">
                                    <div class="icon d-flex align-items-center justify-content-center">
                                        <span class="fa fa-whatsapp"></span>
                                    </div>
                                    <div class="text">
                                        <p><span>Telefone:</span> <a href="tel://{{$telView}}">{{$telView}}</a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="dbox w-100 text-center">
                                    <div class="icon d-flex align-items-center justify-content-center">
                                        <span class="fa fa-paper-plane"></span>
                                    </div>
                                    <div class="text">
                                        <p><span>Email:</span> <a href="mailto:{{$addressEmail}}">{{$addressEmail}}</a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="dbox w-100 text-center">
                                    <div class="icon d-flex align-items-center justify-content-center">
                                        <span class="fa fa-globe"></span>
                                    </div>
                                    <div class="text">
                                        <p><span>Website</span> <a href="{{ URL::to('/') }}" target="_blank">{{ str_replace("https://", "www.", str_replace("https://www", "www.", URL::to('/'))) }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                            @if(session('success'))
                                <div class="alert alert-success mt-3 col-md-12">{{session('success')}}</div>
                            @endif
                            @if(session('warning'))
                                <div class="alert alert-danger mt-3 col-md-12">{{session('warning')}}</div>
                            @endif
                            @if(isset($errors) && count($errors) > 0)
                                <div class="alert alert-danger">
                                    <h4>Existem erros no envio, veja abaixo para corrigi-los.</h4>
                                    <ol>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="col-md-12">
                                <div class="contact-wrap w-100 p-md-5 p-4">
                                    <h3 class="mb-4">Entre em Contato</h3>
                                    <form class="contactForm" action="{{ route('user.mail.contact') }}" method="POST" id="contactForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="label" for="name">Nome Completo</label>
                                                    <input type="text" class="form-control" name="name" id="name" placeholder="Nome Completo" value="{{ old('name') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="label" for="email">Endereço de E-mail</label>
                                                    <input type="email" class="form-control" name="email" id="email" placeholder="Endereço de E-mail" value="{{ old('email') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="label" for="subject">Assunto</label>
                                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Assunto" value="{{ old('subject') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="label" for="#">Mensagem</label>
                                                    <textarea name="message" class="form-control" id="message" cols="30" rows="4" placeholder="Mensagem" required>{{ old('message') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group d-flex justify-content-end flex-wrap">
                                                    <input type="submit" value="Enviar Mensagem" class="btn btn-primary py-3 col-md-3">
                                                    <div class="submitting"></div>
                                                </div>
                                            </div>
                                        </div>
                                        {!! csrf_field() !!}
                                    </form>
                                </div>
                            </div>
{{--                            <div class="col-md-5 animal-md-first d-flex align-items-stretch">--}}
{{--                                <div id="map" class="map"></div>--}}
{{--                            </div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{{--    <input type="hidden" id="address_get_location" value="{{ $addressSearch }}">--}}

@endsection

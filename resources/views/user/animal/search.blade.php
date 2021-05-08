@extends('user.welcome')

@section('title', 'Sobre nós')

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $(function(){
            $('.filters-search select').select2();
            getCities({{ $filter['city'] }}, {{ $filter['neigh'] }});
        });

        const getCities = (city, neighSelect = 0) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ route('queries.ajax.getNeighsCity') }}",
                data: { city },
                dataType: 'json',
                success: response => {

                    getNeighs(response, neighSelect);

                }, error: (e) => {
                    console.log(e);
                }
            });
        }

        const getNeighs = (response, neighSelect = 0) => {
            let options = '';
            let selected = '';
            $(response).each(function (key, value) {
                selected = neighSelect == value.id ? 'selected' : '';
                options += `<option value="${value.id}" ${selected}>${value.name}</option>`;
            });

            $('#neigh').append(options).attr('disabled', false);
        }

        $('#city').change(function (){
            const city = parseInt($(this).val());

            $('#neigh').empty().append('<option value="">Selecione a bairro</option>').attr('disabled', true);

            if (city === 0) return false;

            getCities(city);
        });
    </script>
@endsection

@section('css')
    <style>
        .filters-search  div.col-md-3:nth-child(1) .select2-container .select2-selection--single {
            border-top: 0;
            border-radius: 5px 0 0 5px;
            border-left: 0;
        }
        .filters-search  div.col-md-3:nth-child(2) .select2-container .select2-selection--single {
            border-radius: 5px;
        }
        .filters-search .select2-container {
            border: 1px solid #c0392b;
            background: #fff;
            color: #c0392b;
            height: 40px;
            border-radius: 5px;
            width: 100% !important;
        }
        .filters-search input{
            border: 1px solid #c0392b;
            background: #fff;
            color: #c0392b;
            height: 40px;
            border-radius: 0 5px 5px 0;
        }
        .filters-search div.col-md-3:nth-child(1) .select2-container{
            border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
        }
        .filters-search div.col-md-3:nth-child(3) .select2-container{
            border-bottom-right-radius: 5px;
            border-top-right-radius: 5px;
        }
        .filters-search h2 {
            font-size: 25px;
            font-weight: bold;
            color: #c0392b;
            text-transform: uppercase;
        }
        .filters-search .button-search {
            background-color: #c0392b;
            border: 1px solid #fff;
            color: #fff;
            height: 40px;
            border-radius: 5px;
        }
        .filters-search .button-search:hover {
            background-color: #9c291d;
            border-color: #fff;
            color: #fff;
        }
        .list-searchs {
            margin-top: 50px
        }
        .list-searchs .iten-search {
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 6px #000000;
        }

        @media (max-width:768px){
            .filters-search .button-search {
                margin-top:5px
            }
        }

        @media (min-width:768px){
            .main {
                padding: 0
            }
            .wrap-search {
                width: 100%;
            }
            .filters-search {
                /*background-color: #2c2f3c;*/
                /*border-top: 1px solid #fff;*/
                /*border-bottom: 1px solid #fff;*/
            }
        }
    </style>
@endsection

@section('body')

    <div class="main">
        <div class="wrap wrap-search">
            <div class="row" style="width: 100%">
                <div class="col-md-3 filters-search">
                    <div class="col-md-12 text-center pt-4">
                        <h2 class="mb-3">filtre a região</h2>
                    </div>
                    <form action="{{ route('user.animals.list') }}" method="GET">
                        <div class="col-md-12 form-group">
                            <select id="city" name="cidade">
                                <option value="0">Selecione a cidade</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city['id'] }}" {{ $filter['city'] == $city['id'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <select id="neigh" name="bairro" disabled>
                                <option value="0">Selecione o bairro</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <select id="ordem" name="ordem">
                                <option value="date_desc" {{ $filter['order'][0] == 'animals.created_at' && $filter['order'][1] == 'DESC' ? 'selected' : '' }}>Mais Recente</option>
                                <option value="date_asc" {{ $filter['order'][0] == 'animals.created_at' && $filter['order'][1] == 'ASC' ? 'selected' : '' }}>Mais Antigo</option>
                            </select>
                        </div>
    {{--                    <div class="col-md-2 no-padding">--}}
    {{--                        <input type="date" class="col-md-12" name="data" value="{{ $filter['date'] ?? '' }}">--}}
    {{--                    </div>--}}
                        <div class="col-md-12 form-group">
                            <button type="submit" class="button-search col-md-12">Aplicar Filtro</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-9 pl-md-5 pr-md-5 list-searchs">
                    <div class="col_1_of_2 col-md-12">
                        @if (count($dataAnimals))
                            @foreach($dataAnimals as $animal)
                            <div class="mb-2 images_1_of_2 iten-search">
                                <a href="{{ route('user.animals.searchFind', ['id' => $animal['id']]) }}" class="d-flex align-items-center">
                                    <div class="listimg">
                                        <img src="{{ asset($animal['path'] ? "user/img/animals/{$animal['id']}/thumbnail_{$animal['path']}" : 'user/img/animals/sem_foto.png') }}" width="100px" alt="{{ $animal['name'] }}">
                                    </div>
                                    <div class="text list_2_of_1">
                                        <h3><span>{{ date('d/m/Y H:i', strtotime($animal['created_at'])) }}</span> {{ $animal['name'] }}</h3>
                                        <p>
                                            <b>Bairro:</b> {{ $animal['neigh_name'] }}
                                            <b class="pl-md-4">Espécie:</b> {{ $animal['species'] }}
                                            <b class="pl-md-4">Cor:</b> {{ $animal['color'] }}
                                            <b class="pl-md-4">Porte:</b> {{ $animal['size'] }}
                                        </p>
                                        <h4 class="pt-1">{{ $animal['place'] }}</h4>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                            <div class="col-md-12 mb-5 no-padding">
                                @if (count($dataAnimals))
                                    <div class="col-md-12">
                                        <hr>
                                    </div>

                                    <div class="col-md-12 d-flex justify-content-between flex-wrap align-items-center no-padding">
                                        <a href="{{ $page == 1 ? '#' : route('user.animals.list', ['page' => $page - 1]) }}" class="btn {{ $page == 1 ? 'disabled' : '' }}">Página Anterior</a>
                                        <span class="h5 font-weight-bold">Página: {{ $page }}</span>
                                        <a href="{{ $page == $maxPage ? '#' : route('user.animals.list', ['page' => $page + 1]) }}" class="btn {{ $page == $maxPage ? 'disabled' : '' }}">Próxima Página</a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="col-md-12 text-center">
                                <h2 class="h3">Não foram encontrados resultados. 😪</h2>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
            </div>
        </div>
    </div>

@endsection

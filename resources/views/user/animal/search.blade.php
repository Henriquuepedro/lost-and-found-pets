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
            border-radius: 0;
        }
        .filters-search .select2-container {
            border: 1px solid #c0392b;
            background: #fff;
            color: #c0392b;
            height: 40px;
            border-radius: 0;
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
        .filters-search h2 {
            font-size: 25px;
            font-weight: bold;
            color: #c0392b;
            text-transform: uppercase;
        }
        .filters-search .button-search {
            background-color: #fff;
            border: 1px solid #c0392b;
            color: #c0392b;
            height: 40px;
            border-radius: 5px;
        }
        .filters-search .button-search:hover {
            background-color: #c0392b;
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

            .filters-search input,
            .filters-search select {
                border-radius: 0 !important;;
            }
            .filters-search .select2-container .select2-selection--single {
                border-radius: 0 !important;
            }
            .filters-search div.col-md-3:nth-child(1) .select2-container{
                border-radius: 0 !important;
            }
            .filters-search .button-search {
                margin-top:5px
            }
        }
    </style>
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="row filters-search">
                <div class="col-md-12 d-flex justify-content-center flex-wrap">
                    <h2 class="mb-3">Filtre a região e data do anúncio</h2>
                </div>
                <form action="{{ route('user.animals.list') }}" method="GET" class="col-md-12 d-flex justify-content-center flex-wrap">
                    <div class="col-md-3 no-padding">
                        <select class="col-md-12" id="city" name="cidade">
                            <option value="">Selecione a cidade</option>
                            @foreach($cities as $city)
                                <option value="{{ $city['id'] }}" {{ $filter['city'] == $city['id'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 no-padding">
                        <select class="col-md-12" id="neigh" name="bairro" disabled>
                            <option>Selecione o bairro</option>
                        </select>
                    </div>
                    <div class="col-md-2 no-padding">
                        <input type="date" class="col-md-12" name="data" value="{{ $filter['date'] ?? '' }}">
                    </div>
                    <div class="col-md-2 pl-md-3">
                        <button type="submit" class="button-search col-md-12">Aplicar Filtro</button>
                    </div>
                </form>
            </div>
            <div class="row pl-md-5 pr-md-5 list-searchs">
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
                                        <b>Espécie:</b> {{ $animal['species'] }}
                                        <b class="pl-md-4">Cor:</b> {{ $animal['color'] }}
                                        <b class="pl-md-4">Porte:</b> {{ $animal['size'] }}
                                    </p>
                                    <h4 class="pt-1">{{ $animal['place'] }}</h4>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    @else
                        <div class="col-md-12 text-center">
                            <h2 class="h3">Não foram encontrados resultados. 😪</h2>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@extends('user.welcome')

@section('title', 'Pedidos')

@section('js')
    <script>

        $('.acc-animal-header').click(function () {
            $(this).parent().toggleClass('acc-delivery-cont-close acc-delivery-cont-open');
        });

        $('.remove-animal').on('click', function() {
            const animal_id = $(this).attr('animal-id');
            const animal_name = $(this).attr('animal-name');
            const elCard = $(this).closest('.acc-order-card');

            Swal.fire({
                title: 'Exclusão de Anúncio',
                html: "Você está prestes a excluir definitivamente o anúncio do animal <br><b>"+animal_name+"</b><br>Deseja continuar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#bbb',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: "{{ route('user.account.animals.delete') }}",
                        data: { animal_id },
                        dataType: 'json',
                        success: response => {
                            console.log(response);
                            if (response.success) {
                                elCard.slideUp(500);
                                setTimeout(() => {
                                    elCard.remove();
                                }, 750);
                            }

                            Toast.fire({
                                icon: response.success ? 'success' : 'error',
                                title: response.message
                            });
                        }, error: e => {
                            if (e.status !== 403 && e.status !== 422)
                                console.log(e);
                        },
                        complete: function(xhr) {
                            if (xhr.status === 403) {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Você não tem permissão para fazer essa operação!'
                                });
                                $(`button[equipment-id="${equipment_id}"]`).trigger('blur');
                            }
                            if (xhr.status === 422) {

                                let arrErrors = [];

                                $.each(xhr.responseJSON.errors, function( index, value ) {
                                    arrErrors.push(value);
                                });

                                if (!arrErrors.length && xhr.responseJSON.message !== undefined)
                                    arrErrors.push('Você não tem permissão para fazer essa operação!');

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Atenção',
                                    html: '<ol><li>'+arrErrors.join('</li><li>')+'</li></ol>'
                                });
                            }
                        }
                    });
                }
            });
        });

    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="row">
                <div class="col-md-3 float-left">
                    <div class="acc-container-content col-md-12">
                        @include('user.account.menu')
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="acc-container">
                        <div class="acc-container-content col-md-12">
                            <div class="acc-content-column">

                                @if(count($dataAnimals) > 0)

                                <div class="acc-content-wrapper">
                                    <div class="acc-order-container">
                                        <div class="row">
                                            <div class="col-md-12 d-flex justify-content-between flex-wrap align-items-center">
                                                <h1 class="mt-0">Registros Realizados</h1>
                                                <a href="{{ route('user.account.animals.new') }}" class="btn btn-primary mr-0">Novo Registro</a>
                                            </div>
                                        </div>

                                        @foreach($dataAnimals as $animal)
                                        <div class="acc-order-card">
                                            <div class="acc-delivery-cont acc-delivery-cont-delivered  acc-delivery-cont-close">
                                                <div class="acc-order-header danger acc-animal-header">
                                                    <span class="acc-order-header-icon"></span>
                                                    <div class="acc-order-header-info">
                                                        <span class="acc-order-header-info-status">
                                                            {{ $animal['name'] }}
                                                        </span>

                                                        <span>
                                                            Registrado em:
                                                            <strong class="acc-delivery-prevision-days delivered">{{ date('d/m/Y H:i', strtotime($animal['created_at'])) }}</strong>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="acc-delivery-body acc-delivery-body-prod-open danger">
                                                    <div class="acc-order-info-cont acc-order-info-cont-delivered">
                                                        <ul class="acc-delivery-list">
                                                            <li class="acc-order-item-cont">
                                                                <div class="acc-order-product">
                                                                    <a target="_blank" rel="noopener noreferrer" class="img-product" href="">
                                                                        <figure><img class="acc-order-product-image" src="{{ asset($animal['path'] ? "user/img/animals/{$animal['id']}/{$animal['path']}" : 'user/img/animals/sem_foto.png') }}" alt="Sem imagem"></figure>
                                                                    </a>
                                                                    <div class="acc-order-product-truncate">
                                                                    <span class="acc-order-product-info" alt="" title="">
                                                                        <a class="acc-order-product-link" target="_blank" rel="noopener noreferrer" href="">Data do desaparecimento: {{ $animal['disappearance_date'] ? date('d/m/Y', strtotime($animal['disappearance_date'])) : '' }}</a>
                                                                    </span>
                                                                        <p class="acc-order-product-info">
                                                                            <strong>Espécie:</strong> {{$animal['species']}}<br>
                                                                            <strong>Cor:</strong> {{$animal['color']}}<br>
                                                                            <strong>Tamanho:</strong> {{$animal['size']}}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                        <div class="boxBtnAction">
                                                            <a class="btn btn-primary col-md-4 py-1" href="{{ route('user.account.animal', array('id' => $animal['id'])) }}">
                                                                <i class="fas fa-file-alt"></i> Ver Detalhes
                                                            </a>
                                                            <button class="btn btn-danger col-md-4 py-1 remove-animal" animal-name="{{ $animal['name'] }}" animal-id="{{ $animal['id'] }}">
                                                                <i class="fas fa-trash"></i> Excluir Anúncio
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @else
                                <div class="acc-content-wrapper">
                                    <div class="acc-order-container">
                                        <h3 class="text-center">Você ainda cadastrou nenhum animal!</h3>
                                        <p class="text-center mt-4">
                                            <a href="{{route('user.home')}}" class="link-primary cursor-pointer">Voltar para página inicial</a>
                                            <span> ou </span>
                                            <a href="" class="link-primary cursor-pointer">registrar animal perdido</a>.
                                        </p>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

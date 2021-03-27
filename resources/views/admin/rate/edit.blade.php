@extends('adminlte::page')

@section('title', 'Alteração Avaliação')

@section('content_header')
    <h1 class="m-0 text-dark">Alteração Avaliação</h1>
@stop

@section('css')
    <style>
        div[class^="star-rating"] label {
            cursor:pointer;
        }
        div[class^="star-rating"] label input{
            display:none;
        }
        div[class^="star-rating"] label i {
            font-size:25px;
            -webkit-transition-property:color, text;
            -webkit-transition-duration: .2s, .2s;
            -webkit-transition-timing-function: linear, ease-in;
            -moz-transition-property:color, text;
            -moz-transition-duration:.2s;
            -moz-transition-timing-function: linear, ease-in;
            -o-transition-property:color, text;
            -o-transition-duration:.2s;
            -o-transition-timing-function: linear, ease-in;
        }
        div[class^="star-rating"] label i:before {
            content:'\f005';
        }
        div[class^="star-rating"] label i.active {
            color:gold;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script>
        $(function () {
            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
            $('input[name="percentage"]').mask('#.##0,00', {
                reverse: true
            });
        });
        $('div[class^="star-rating"] label i.fa').on('click mouseover',function(){
            // remove classe ativa de todas as estrelas
            const el = $(this).closest('div[class^="star-rating"]');
            el.find('label i.fa').removeClass('active');
            // pegar o valor do input da estrela clicada
            var val = $(this).prev('input').val();
            //percorrer todas as estrelas
            el.find('label i.fa').each(function(){
                /* checar de o valor clicado é menor ou igual do input atual
                *  se sim, adicionar classe active
                */
                var $input = $(this).prev('input');
                if($input.val() <= val){
                    $(this).addClass('active');
                }
            });
        });
        //Ao sair da div star-rating
        $('div[class^="star-rating"]').mouseleave(function(){
            //pegar o valor clicado
            const el = $(this).closest('div[class^="star-rating"]');
            var val = $(this).find('input:checked').val();
            //se nenhum foi clicado remover classe de todos
            if(val == undefined ){
                el.find('label i.fa').removeClass('active');
            } else {
                //percorrer todas as estrelas
                el.find('label i.fa').each(function(){
                    /* Testar o input atual do laço com o valor clicado
                    *  se maior, remover classe, senão adicionar classe
                    */
                    var $input = $(this).prev('input');
                    if($input.val() > val){
                        $(this).removeClass('active');
                    } else {
                        $(this).addClass('active');
                    }
                });
            }
        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="error-form alert alert-warning {{ count($errors) == 0 ? 'display-none' : '' }}">
                <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                <ol>
                    @foreach($errors->all() as $error)
                        <li><label id="name-error" class="error">{{ $error }}</label></li>
                    @endforeach
                </ol>
            </div>

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Avaliação</h3><br>
                    <small class="text-danger">Essa avaliação não está vinculada a nenhum pedido</small>
                </div>
                <form action="{{ route('admin.rate.update') }}" enctype="multipart/form-data" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name_user" name="name_user" value="{{ old('name_user') ?? $rate->name_user  }}" placeholder="Digite o nome do avaliador" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Título</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') ?? $rate->title  }}" placeholder="Digite o título da avaliação" required>
                            </div>
                            <div class="form-group col-md-3 text-center">
                                <label for="name">Avaliação de 1 à 5</label>

                                <div class="star-rating">
                                    <label>
                                        <input type="radio" name="rate" value="1" required {{$rate->rate == 1 ? 'checked' : ''}}/>
                                        <i class="fa {{$rate->rate >= 1 ? 'active' : ''}}"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="2" required {{$rate->rate == 2 ? 'checked' : ''}}/>
                                        <i class="fa {{$rate->rate >= 2 ? 'active' : ''}}"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="3" required {{$rate->rate == 3 ? 'checked' : ''}}/>
                                        <i class="fa {{$rate->rate >= 3 ? 'active' : ''}}"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="4" required {{$rate->rate == 4 ? 'checked' : ''}}/>
                                        <i class="fa {{$rate->rate >= 4 ? 'active' : ''}}"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="5" required {{$rate->rate == 5 ? 'checked' : ''}}/>
                                        <i class="fa {{$rate->rate == 5 ? 'active' : ''}}"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="width">Ativo</label></br>
                                <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM" data-off-text="NÃO" name="active" value="1" {{  $rate->approved == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Produto</label>
                                <select class="form-control" name="product">
                                    @foreach($products as $product)
                                        <option {{$rate->product_id == $product->id ? 'selected' : ''}} value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">Descrição da Avaliação</label>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="5" id="description" name="description" required>{{$rate->description}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.rate') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="rate_id" value="{{$rate->id}}">
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop

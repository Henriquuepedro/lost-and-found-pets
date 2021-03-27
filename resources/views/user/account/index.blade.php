@extends('user.welcome')

@section('title', 'Minha Conta')

@section('js')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
@endsection

@section('body')
    <div class="main">
        <div class="wrap">
            <div class="row">
                <div class="col-md-12">
                    @if(isset($errors) && count($errors) > 0)
                        @foreach($errors->all() as $error)
                            <div class="alert alert-warning col-md-12 mt-2">{{ $error }}</div>
                        @endforeach
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success col-md-12 mt-2">{{session('success')}}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-danger col-md-12 mt-2">{{session('warning')}}</div>
                    @endif
                </div>
                <div class="col-md-3 float-left">
                    <div class="acc-container-content col-md-12">
                        @include('user.account.menu')
                    </div>
                </div>
                <div class="col-md-9 float-right">
                    <div class="col-md-12 mb-5">
                        <h4>Bem-vindo(a) novamente, {{ auth()->guard('client')->user()->name }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

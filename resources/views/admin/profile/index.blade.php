@extends('adminlte::page')

@section('title', 'Perfil do Usuário')

@section('content_header')
    <h1 class="m-0 text-dark">Perfil do Usuário</h1>
@stop

@section('css')
    <style>
        #viewLogo{
            border-radius: 50%;
            border: 1px solid #000;
            padding: 3px;
        }
        .warning-gmail p a{
            text-decoration: none;
        }
        @media (min-width: 768px) {
            .warning-gmail p a{
                margin-left: 25px;
            }
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        $(function () {
            $('input[name="cep"]').mask('00000-000');
            $('input[name="tel"]').trigger('blur').mask('(00) 0000-00009');
            $('[data-toggle="tooltip"').tooltip();
        })

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewLogo').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $("#picture").change(function() {
            readURL(this);
        });


        $('input[name="cep"]').blur(function () {
            const cep = $(this).val().replace(/[^\d]+/g, '');

            if(cep.length != 8) return false;

            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, resultado => {
                console.log(resultado);
                if(!resultado.erro){
                    const endereco = resultado.logradouro;
                    const bairro = resultado.bairro;
                    const estado = resultado.uf;
                    const cidade = resultado.localidade;

                    $('input[name="address"]').val(endereco);
                    $('input[name="neighborhood"]').val(bairro);
                    $('input[name="city"]').val(cidade);
                    $('input[name="state"]').val(estado);
                }
                if(resultado.erro){
                    alert( "CEP inválido ou inexistente!");
                }
            });
        });

        $('#teste-send-mail').on('click', function () {

            const btn       = $(this);
            const email     = $('#email_noreplay').val();
            const password  = $('#password_noreplay').val();
            const smtp      = $('#smtp_noreplay').val();
            const port      = $('#port_noreplay').val();
            const secure    = $('#secure_noreplay').val();

            btn.attr('disabled', true);

            console.log( email, password, smtp, port, secure );

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/admin/queries/ajax/testMailSend",
                data: { email, password, smtp, port, secure },
                dataType: 'json',
                success: result => {
                    if (result)
                        alert('Seus dados são válidos para envio de e-mail, salve suas configurações!');
                    else
                        alert('Os dados informados não são válidos, corrija-os!');

                    btn.attr('disabled', false);

                }, error: e => {
                    console.log(e);
                    btn.attr('disabled', false);
                }
            });
        })
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success mt-2">{{session('success')}}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-danger mt-2">{{session('warning')}}</div>
            @endif
            <div class="error-form alert alert-warning {{ count($errors) == 0 ? 'display-none' : '' }}">
                <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                <ol>
                    @foreach($errors->all() as $error)
                        <li><label id="name-error" class="error">{{ $error }}</label></li>
                    @endforeach
                </ol>
            </div>
            <form action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" method="POST">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Alterar Perfil</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Nome da Loja</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $arrAdmin['name']  }}" placeholder="Digite o nome da loja" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="picture">Alterar Logo</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="picture" name="picture">
                                        <label class="custom-file-label" for="exampleInputFile">Alterar</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3 text-center">
                                <label>Visualização Logo</label><br>
                                <img src="{{ asset("user/img/admin/{$arrAdmin['picture']}") }}" id="viewLogo" height="80">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="email">E-mail Acesso</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') ?? $arrAdmin['email']  }}" placeholder="Digite o e-mail para acesso ao sistema" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email_contact">E-mail Contato</label>
                                <input type="email" class="form-control" id="email_contact" name="email_contact" value="{{ old('email_contact') ?? $arrAdmin['email_contact']  }}" placeholder="Digite o e-mail de contato da loja" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="tel">Telefone</label>
                                <input type="tel" class="form-control" id="tel" name="tel" value="{{ old('tel') ?? $arrAdmin['tel']  }}" placeholder="Digite o número de telefone da loja" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Dados Envio de E-mail</h3><br>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="callout callout-warning warning-gmail">
                                    <h5>Usa G-MAIL?</h5>
                                    <p class="d-flex justify-content-start flex-wrap">
                                        Não deixe de ler em como configurar sua conta para uso de disparos de e-mails!
                                        <a href="https://www.hostinger.com.br/tutoriais/aprenda-a-utilizar-o-smtp-google/" target="_blank" class="btn btn-block btn-outline-info btn-xs col-md-3">Visualizar configuração</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="email_noreplay">E-mail</label>
                                <input type="email" class="form-control" id="email_noreplay" name="email_noreplay" value="{{ old('email_noreplay') ?? $arrAdmin['email_noreplay']  }}" placeholder="Digite o e-mail para envio de e-mail" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="password_noreplay">Senha</label>
                                <input type="text" class="form-control" id="password_noreplay" name="password_noreplay" value="{{ old('password_noreplay') ?? $arrAdmin['password_noreplay']  }}" placeholder="Digite a senha do e-mail para envios" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="smtp_noreplay">SMTP</label>
                                <input type="text" class="form-control" id="smtp_noreplay" name="smtp_noreplay" value="{{ old('smtp_noreplay') ?? $arrAdmin['smtp_noreplay']  }}" placeholder="Digite o SMTP do e-mail para envios" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="port_noreplay">Porta</label>
                                <input type="text" class="form-control" id="port_noreplay" name="port_noreplay" value="{{ old('port_noreplay') ?? $arrAdmin['port_noreplay']  }}" placeholder="Digite a porta do e-mail para envios" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="secure_noreplay">Segurança</label>
                                <select class="form-control" id="secure_noreplay" name="secure_noreplay" required>
                                    <option value="tls" {{ old() ? old('secure_noreplay') == "tls" ? "selected" : "" : $arrAdmin['port_noreplay'] == "tls" ? "selected" : "" }}>TLS</option>
                                    <option value="ssl" {{ old() ? old('secure_noreplay') == "ssl" ? "selected" : "" : $arrAdmin['port_noreplay'] == "ssl" ? "selected" : "" }}>SSL</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-center form-group">
                                <button type="button" class="btn btn-info col-md-4" id="teste-send-mail">Testar Configuração de E-mail</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Dados do Remetente</h3><br>
{{--                        <p class="text-danger no-margin">Esse endereço não ficará visível, será utilizado para cálculo do frete e etiquetas</p>--}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="name">Nome do Remetente</label>
                                <input type="text" class="form-control" id="name_user" name="name_user" value="{{ old('name_user') ?? $arrAdmin['name_user']  }}" placeholder="Digite o nome do remetente" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="name">CEP</label>
                                <input type="tel" class="form-control" id="cep" name="cep" value="{{ old('cep') ?? $arrAdmin['cep']  }}" placeholder="Digite o CEP da loja" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Endereço</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') ?? $arrAdmin['address']  }}" placeholder="Digite o endereço da loja" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="name">Número</label>
                                <input type="text" class="form-control" id="number" name="number" value="{{ old('number') ?? $arrAdmin['number']  }}" placeholder="Digite o número da loja" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="name">Complemento</label>
                                <input type="text" class="form-control" id="complement" name="complement" value="{{ old('complement') ?? $arrAdmin['complement']  }}" placeholder="Digite o complemento da loja">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name">Bairro</label>
                                <input type="text" class="form-control" id="neighborhood" name="neighborhood" value="{{ old('neighborhood') ?? $arrAdmin['neighborhood']  }}" placeholder="Digite o bairro da loja" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Cidade</label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city') ?? $arrAdmin['city']  }}" placeholder="Digite a cidade da loja" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Estado</label>
                                <input type="text" class="form-control" id="state" name="state" value="{{ old('state') ?? $arrAdmin['state']  }}" placeholder="Digite o estado da loja" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Alterar Senha</h3><br>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 alert alert-info">
                                Caso não queira alterar a senha, não preencha os campos abaixo!
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="name">Senha Atual</label>
                                <input type="password" class="form-control" id="password_current" name="password_current" placeholder="Senha atual do usuário">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name">Nova Senha</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Nova senha do usuário">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name">Confirmação Nova Senha</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirme a nova senha do usuário">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.home') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                </div>
            </form>
        </div>
    </div>
@stop

const somenteNumeros = num => {
    var er = /[^0-9.]/;
    er.lastIndex = 0;
    var campo = num;
    if (er.test(campo.value)) {
        campo.value = "";
    }
}

var Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

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

var input = document.getElementById("picture");

$('#insert-testimony #picture').on("change", function(){
    var nome = "Selecione uma imagem sobre o depoimento";
    if($('#insert-testimony #picture').prop('files')[0] !== undefined) nome = $('#insert-testimony #picture').prop('files')[0].name;
    $('#insert-testimony label[for="exampleInputFile"]').text(nome);
});

const validCpf = cpf => {
    let soma = 0
    let resto;
    for (let i = 1; i <= 9; i++) soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i)

    resto = (soma * 10) % 11
    if ((resto == 10) || (resto == 11)) resto = 0
    if (resto != parseInt(cpf.substring(9, 10)) ) return false

    soma = 0
    for (let i = 1; i <= 10; i++) soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i)

    resto = (soma * 10) % 11
    if ((resto == 10) || (resto == 11))  resto = 0
    if (resto != parseInt(cpf.substring(10, 11) ) ) return false

    return true
}
// TRANSFORMAR NUMERO EM MOEDA REAL
const numberToReal = numero => {
    numero = parseFloat(numero);
    numero = numero.toFixed(2).split('.');
    numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.');
    return numero.join(',');
}

// TRANSFORMAR MOEDA REAL EM NUMERO COMPUTÁVEL
const realToNumber = numero => {
    if(numero === undefined) return false;
    numero = numero.toString();
    numero = numero.replace(".", "").replace(",", ".");
    return parseFloat(numero);
}

const validDate = (date) => {
    var RegExPattern = /^((((0?[1-9]|[12]\d|3[01])[\.\-\/](0?[13578]|1[02])      [\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|[12]\d|30)[\.\-\/](0?[13456789]|1[012])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|1\d|2[0-8])[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|(29[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00)))|(((0[1-9]|[12]\d|3[01])(0[13578]|1[02])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|[12]\d|30)(0[13456789]|1[012])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|1\d|2[0-8])02((1[6-9]|[2-9]\d)?\d{2}))|(2902((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00))))$/;

    if (!((date.match(RegExPattern)) && (date != '')))
        return false;
    else
        return true
}

$('#insert-testimony form').on('submit', function (){
    $('button[type="submit"]', this).attr('disabled', true);
})

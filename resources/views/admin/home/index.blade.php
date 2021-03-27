@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="m-0 text-dark">Dashboard</h1>
@stop

@section('css')
    <style>
        canvas{
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
        .chartjs-tooltip,
        .chartjs-week-tooltip,
        .chartjs-year-tooltip {
            opacity: 1;
            position: absolute;
            background: rgba(0, 0, 0, .7);
            color: white;
            border-radius: 3px;
            -webkit-transition: all .1s ease;
            transition: all .1s ease;
            pointer-events: none;
            -webkit-transform: translate(-50%, 0);
            transform: translate(-50%, 0);
            padding: 4px;
        }

        .chartjs-tooltip-key,
        .chartjs-week-tooltip-ke,
        .chartjs-year-tooltip-key {
            display: inline-block;
            width: 10px;
            height: 10px;
        }
        [style="cursor: pointer;"][opacity="0.3"][transform^="translate"] {
            display: none;
        }
        .dropdown-menu.palet-color a{
            cursor: pointer;
            width: 20px;
            height: 40px;
            text-indent: -9999px;
            border-radius: 19px;
            border: 4px solid #fff;
            box-shadow: 0px 2px 10px -5px #000000;
            background-size: 38px 38px
        }
        .dropdown-menu.show {
            display: flex
        }
        .chartjs-tooltip-year {
            opacity: 1;
            position: absolute;
            background: rgba(0, 0, 0, .7);
            color: white;
            border-radius: 3px;
            -webkit-transition: all .1s ease;
            transition: all .1s ease;
            pointer-events: none;
            -webkit-transform: translate(-50%, 0);
            transform: translate(-50%, 0);
            padding: 3px 8px;
        }
        #chartjs-tooltip-year {
            opacity: 1;
            position: absolute;
            background: rgba(0, 0, 0, .7);
            color: white;
            border-radius: 3px;
            -webkit-transition: all .1s ease;
            transition: all .1s ease;
            pointer-events: none;
            -webkit-transform: translate(-50%, 0);
            transform: translate(-50%, 0);
            z-index:1
        }

        .chartjs-tooltip-year-key {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin-right: 10px;
        }
        #tableClientsForState_paginate .pagination,
        #tableLastAccessClients_paginate .pagination,
        #tableLastEvents_paginate .pagination,
        #tableLastOrders_paginate .pagination{
            justify-content: flex-end;
        }
        .dataTable thead tr th, .dataTable tfoot tr th {
             padding: 4px;
             background: #eee;
        }
        .dataTable tbody tr td {
            padding: 5px;
        }

        a:focus,
        a:active {
            outline: 0;
            -webkit-box-shadow: none !important;
            box-shadow: none;
        }
        .card-body{
            padding: 10px;
        }
        .dataTable thead tr th, .dataTable tfoot tr th {
            padding: 4px;
            background: #ddd;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="https://www.chartjs.org/dist/2.9.3/Chart.min.js"></script>
    <script src="https://cdnjs.com/libraries/Chart.js"></script>

    <script src="https://www.amcharts.com/lib/4/core.js"></script>
    <script src="https://www.amcharts.com/lib/4/maps.js"></script>
    <script src="https://www.amcharts.com/lib/4/geodata/brazilLow.js"></script>

    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

    <script>
        const loadPieChart = () => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: window.location.origin + "/admin/queries/ajax/pieChartClient",
                dataType: 'json',
                success: response => {
                    if (response.name.length == 0) return;

                    let backgroundColor = [];


                    for(let c = 0; c < response.name.length; c++){
                        backgroundColor.push(getColorRandom());
                    }

                    const optionsClient = {
                        "type": "doughnut",
                        "data": {
                            "labels": response.name,
                            "datasets": [
                                {
                                    "label": "My First Dataset",
                                    "data": response.qty,
                                    "backgroundColor": backgroundColor
                                }
                            ]
                        }
                    }
                    new Chart(document.getElementById("pieChartClient"), optionsClient);
                }, error: () => {
                    alert("Acorreu um problema, caso o problema persistir contate o suporte");
                }
            });


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: window.location.origin + "/admin/queries/ajax/pieChartIten",
                dataType: 'json',
                success: response => {
                    if(response.name.length == 0) return ;

                    let backgroundColor = [];

                    for(let c = 0; c < response.name.length; c++){
                        backgroundColor.push(getColorRandom());
                    }

                    const optionsIten = {
                        "type": "doughnut",
                        "data": {
                            "labels": response.name,
                            "datasets": [
                                {
                                    "label": "My First Dataset",
                                    "data": response.qty,
                                    "backgroundColor": backgroundColor
                                }
                            ]
                        }
                    }
                    new Chart(document.getElementById("pieChartIten"), optionsIten);
                }, error: () => {
                    alert("Acorreu um problema, caso o problema persistir contate o suporte");
                }
            });
        }

        const loadSalesChart = (type = 6) => {
            let months = [];
            let month = "";
            let totalsRecebido = [];
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: window.location.origin + "/admin/queries/ajax/salesChart",
                dataType: 'json',
                success: response => {
                    for(let m = 0; m < type; m++){
                        month = response.months[m];
                        months.push(month);
                        totalsRecebido.push(response.totals[month]);
                    }

                    var lineChartData = {
                        labels: months,
                        datasets: [
                            {
                                label: 'Total Recebido',
                                borderColor: "rgb(235, 89, 45)",
                                borderWidth: 2,
                                backgroundColor: "rgb(235, 89, 45, .6)",
                                pointBackgroundColor: "rgb(235, 89, 45)",
                                data: totalsRecebido,
                            }
                        ],
                        fill: 'start'
                    };


                    const customTooltips = function(tooltip) {
                        $(this._chart.canvas).css('cursor', 'pointer');

                        var positionY = this._chart.canvas.offsetTop;
                        var positionX = this._chart.canvas.offsetLeft;

                        $('.chartjs-tooltip').css({
                            opacity: 0,
                        });

                        if (!tooltip || !tooltip.opacity) {
                            return;
                        }

                        if (tooltip.dataPoints.length > 0) {
                            tooltip.dataPoints[0].yLabel = tooltip.dataPoints[0].yLabel.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}); //recebido
                            tooltip.dataPoints[0].xLabel = ""; //recebido

                            tooltip.dataPoints.forEach(function(dataPoint) {
                                var content = [dataPoint.xLabel, dataPoint.yLabel].join('');
                                var $tooltip = $('#tooltip');

                                $tooltip.html(content);
                                // if(dataPoint.x > 900) dataPoint.x = dataPoint.x - 30;
                                // if(dataPoint.x < 50) dataPoint.x = dataPoint.x + 30;
                                $tooltip.css({
                                    opacity: 1,
                                    top: positionY + dataPoint.y + 'px',
                                    left: (positionX + dataPoint.x) + 'px',
                                });
                            });
                        }
                    };


                    const ctx = $('#salesChart');
                    new Chart(ctx, {
                        type: 'bar',
                        data: lineChartData,
                        options: {
                            title: {
                                display: true,
                                text: 'Faturamento Semestral'
                            },
                            tooltips: {
                                enabled: false,
                                mode: 'index',
                                intersect: false,
                                custom: customTooltips
                            },
                            elements: {
                                line: {
                                    tension: 0.000001
                                }
                            }
                        }
                    });
                }, error: () => {
                    alert("Acorreu um problema, caso o problema persistir contate o suporte");
                }
            });
        }

        const loadSalesChartWeek = () => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: window.location.origin + "/admin/queries/ajax/salesChartWeek",
                dataType: 'json',
                success: response => {
                    var lineChartData = {
                        labels: ["Segunda","Terça","Quarta","Quinta","Sexta","Sábado","Domingo"],
                        datasets: [
                            {
                                label: 'Total Recebido',
                                borderColor: "rgb(235, 89, 45)",
                                borderWidth: 2,
                                backgroundColor: "rgb(235, 89, 45, .6)",
                                pointBackgroundColor: "rgb(235, 89, 45)",
                                data: [
                                    response.totals.Monday,
                                    response.totals.Tuesday,
                                    response.totals.Wednesday,
                                    response.totals.Thursday,
                                    response.totals.Friday,
                                    response.totals.Saturday,
                                    response.totals.Sunday
                                ],
                            }
                        ],
                        fill: 'start'
                    }


                    const customTooltips = function(tooltip) {
                        $(this._chart.canvas).css('cursor', 'pointer');

                        var positionY = this._chart.canvas.offsetTop;
                        var positionX = this._chart.canvas.offsetLeft;

                        $('.chartjs-week-tooltip').css({
                            opacity: 0,
                        });

                        if (!tooltip || !tooltip.opacity) {
                            return;
                        }

                        if (tooltip.dataPoints.length > 0) {
                            tooltip.dataPoints[0].yLabel = tooltip.dataPoints[0].yLabel.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}); //recebido
                            tooltip.dataPoints[0].xLabel = ""; //recebido

                            tooltip.dataPoints.forEach(function(dataPoint) {
                                var content = [dataPoint.xLabel, dataPoint.yLabel].join('');
                                var $tooltip = $('#tooltip-week');

                                $tooltip.html(content);
                                if(dataPoint.x > 900) dataPoint.x = dataPoint.x - 30;
                                if(dataPoint.x < 50) dataPoint.x = dataPoint.x + 30;
                                $tooltip.css({
                                    opacity: 1,
                                    top: positionY + dataPoint.y + 'px',
                                    left: (positionX + dataPoint.x) + 'px',
                                });
                            });
                        }
                    };


                    const ctx = $('#salesChartWeek');
                    new Chart(ctx, {
                        type: 'line',
                        data: lineChartData,
                        options: {
                            title: {
                                display: true,
                                text: 'Faturamento Semanal'
                            },
                            tooltips: {
                                enabled: false,
                                mode: 'index',
                                intersect: false,
                                custom: customTooltips
                            },
                            elements: {
                                line: {
                                    tension: 0.000001
                                }
                            }
                        }
                    });
                }, error: () => {
                    alert("Acorreu um problema, caso o problema persistir contate o suporte");
                }
            });
        }

        const loadSalesChartYear = (type = 2) => {
            let resultYear = [];

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: window.location.origin + "/admin/queries/ajax/salesChartYear",
                dataType: 'json',
                success: response => {

                    let countArr = 0;
                    $.each(response, function( index, value ) {
                        resultYear.push([]);
                        $.each(value, function( _index, _value ) {
                            resultYear[countArr].push(_value);
                        });
                        countArr++;
                    });
                    console.log(response);
                    console.log(resultYear);

                    var lineChartData = {
                        // labels: ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
                        labels: ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"],
                        datasets: [
                            {
                                label: new Date().getFullYear() - 1,
                                borderColor: "rgb(20,68,186)",
                                borderWidth: 2,
                                backgroundColor: "rgb(20,68,186,0.6)",
                                pointBackgroundColor: "rgb(20,68,186)",
                                data: resultYear[0],
                            },
                            {
                                label: new Date().getFullYear(),
                                borderColor: "rgb(6,155,25)",
                                borderWidth: 2,
                                backgroundColor: "rgba(6,155,25,0.6)",
                                pointBackgroundColor: "rgb(6,155,25)",
                                data: resultYear[1],
                            }
                        ],
                        fill: 'start'
                    }

                    var customTooltips = function(tooltip) {
                        // Tooltip Element
                        var tooltipEl = document.getElementById('chartjs-tooltip-year');

                        if (!tooltipEl) {
                            tooltipEl = document.createElement('div');
                            tooltipEl.id = 'chartjs-tooltip-year';
                            tooltipEl.innerHTML = '<table></table>';
                            this._chart.canvas.parentNode.appendChild(tooltipEl);
                        }

                        // Hide if no tooltip
                        if (tooltip.opacity === 0) {
                            tooltipEl.style.opacity = 0;
                            return;
                        }

                        // Set caret Position
                        tooltipEl.classList.remove('above', 'below', 'no-transform');
                        if (tooltip.yAlign) {
                            tooltipEl.classList.add(tooltip.yAlign);
                        } else {
                            tooltipEl.classList.add('no-transform');
                        }

                        function getBody(bodyItem) {
                            let bodySplit = bodyItem.lines[0].split(': ');
                            return [bodySplit[0] + ': ' +  parseFloat(bodySplit[1]).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'})];
                        }

                        // Set Text
                        if (tooltip.body) {
                            var titleLines = tooltip.title || [];
                            var bodyLines = tooltip.body.map(getBody);

                            var innerHtml = '<thead class="text-center">';

                            titleLines.forEach(function(title) {
                                innerHtml += '<tr><th>' + title + '</th></tr>';
                            });
                            innerHtml += '</thead><tbody>';

                            bodyLines.forEach(function(body, i) {
                                var colors = tooltip.labelColors[i];
                                var style = 'background:' + colors.backgroundColor;
                                style += '; border-color:' + colors.borderColor;
                                style += '; border-width: 2px';
                                var span = '<span class="chartjs-tooltip-year-key" style="' + style + '"></span>';
                                innerHtml += '<tr><td class="no-padding">' + span + body + '</td></tr>';
                            });
                            innerHtml += '</tbody>';

                            var tableRoot = tooltipEl.querySelector('table');
                            tableRoot.innerHTML = innerHtml;
                        }

                        var positionY = this._chart.canvas.offsetTop;
                        var positionX = this._chart.canvas.offsetLeft;

                        tooltipElStyleLeft = positionX + tooltip.caretX;
                        if (tooltipElStyleLeft > 1000) {
                            tooltipElStyleLeft -= 35;
                            tooltipEl.style.width = tooltip.width + 75 + 'px';
                        } else tooltipEl.style.width = 'auto';

                        // Display, position, and set styles for font
                        tooltipEl.style.opacity = 1;
                        tooltipEl.style.left = tooltipElStyleLeft + 'px';
                        tooltipEl.style.top = (positionY + tooltip.caretY - 30) + 'px';
                        tooltipEl.style.fontFamily = tooltip._bodyFontFamily;
                        tooltipEl.style.fontSize = tooltip.bodyFontSize + 'px';
                        tooltipEl.style.fontStyle = tooltip._bodyFontStyle;
                        tooltipEl.style.padding = tooltip.yPadding + 'px ' + tooltip.xPadding + 'px';
                    }
                    // var customTooltips = function(tooltip) {
                    //     $(this._chart.canvas).css('cursor', 'pointer');
                    //
                    //     var positionY = this._chart.canvas.offsetTop;
                    //     var positionX = this._chart.canvas.offsetLeft;
                    //
                    //     $('.chartjs-tooltip-year').css({
                    //         opacity: 0,
                    //     });
                    //
                    //     if (!tooltip || !tooltip.opacity) {
                    //         return;
                    //     }
                    //
                    //     if (tooltip.dataPoints.length > 0) {
                    //         tooltip.dataPoints.forEach(function(dataPoint) {
                    //             var content = [dataPoint.xLabel, dataPoint.yLabel.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'})].join(': ');
                    //             var $tooltip = $('#tooltip-year-' + dataPoint.datasetIndex);
                    //
                    //             // tooltip.dataPoints[0].yLabel.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                    //             $tooltip.html(content);
                    //             $tooltip.css({
                    //                 opacity: 1,
                    //                 top: positionY + dataPoint.y + 'px',
                    //                 left: positionX + dataPoint.x + 'px',
                    //             });
                    //         });
                    //     }
                    // };

                    const ctx = $('#salesChartYears');
                    new Chart(ctx, {
                        type: 'line',
                        data: lineChartData,
                        options: {
                            title: {
                                display: true,
                                text: 'Relatório Faturamento Comparativo Ano Passado'
                            },
                            // tooltips: {
                            //     position: 'average',
                            //     mode: 'index',
                            //     intersect: false,
                            // },
                            elements: {
                                line: {
                                    tension: 0.000001
                                }
                            },
                            tooltips: {
                                enabled: false,
                                position: 'average',
                                mode: 'index',
                                intersect: false,
                                custom: customTooltips
                            }
                        }
                    });
                }, error: e => {
                    console.log(e);
                    alert("Acorreu um problema, caso o problema persistir contate o suporte");
                }
            });
        }

        const getColorRandom = () => {
            var hexadecimais = '0123456789ABCDEF';
            var cor = '#';

            // Pega um número aleatório no array acima
            for (var i = 0; i < 6; i++ ) {
                //E concatena à variável cor
                cor += hexadecimais[Math.floor(Math.random() * 16)];
            }
            return cor;
        }

        const loadMapChartState = (colorMin, colorMax) => {

            if (typeof(Storage) !== "undefined")
                localStorage.setItem("colorGraphMap",  JSON.stringify({"min": colorMin, "max": colorMax}));


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: window.location.origin + "/admin/queries/ajax/mapChartstate",
                dataType: 'json',
                success: response => {
                    let dados = [];
                    let maxValue = 0;
                    $.each( response, function( key, value ) {
                        dados.push({
                            "id": `BR-${key}`,
                            "value": value
                        });
                        maxValue = value > maxValue ? value : maxValue;
                    });

                    // Define o local e tipo de gráfico
                    let chart = am4core.create("graphMap", am4maps.MapChart);
                    // Atribui o 'brazilLow' como o mapa
                    // Você pode optar pelo 'brazilHigh', basta alterar aqui e src do script no html
                    // Também define que as partes que montam o mapa serão com base no MapPolygonSeries
                    chart.geodata = am4geodata_brazilLow;

                    let polygonSeries = chart.series.push(new am4maps.MapPolygonSeries());
                    polygonSeries.data = dados;
                    polygonSeries.useGeodata = true;
                    // Preenche os dados para fazer o mapa de calor
                    // Faremos com que os menores valores sejam verdes e maiores sejam vermelhos
                    polygonSeries.heatRules.push({
                        property: "fill",
                        target: polygonSeries.mapPolygons.template,
                        min: am4core.color(colorMin),
                        max: am4core.color(colorMax)
                    });

                    // Define as legendas, posição e cores.
                    let heatLegend = chart.createChild(am4maps.HeatLegend);
                    heatLegend.series = polygonSeries;
                    heatLegend.align = "right";
                    heatLegend.width = am4core.percent(25);
                    heatLegend.marginRight = am4core.percent(4);
                    heatLegend.valign = "bottom";
                    heatLegend.height = am4core.percent(80);
                    heatLegend.orientation = "vertical";
                    heatLegend.valign = "middle";
                    heatLegend.marginRight = am4core.percent(4);
                    heatLegend.valueAxis.renderer.opposite = true;
                    heatLegend.valueAxis.renderer.dx = - 25;
                    heatLegend.valueAxis.strictMinMax = false;
                    heatLegend.valueAxis.fontSize = 10;

                    // Configuras os tooltips (texto ao passar o mouse)
                    let polygonTemplate = polygonSeries.mapPolygons.template;
                    polygonTemplate.tooltipText = "{name}: {value}";
                    polygonTemplate.nonScalingStroke = true;
                    polygonTemplate.strokeWidth = 0.5;
                    // Muda a cor do estado ao passar o mouse
                    let hs = polygonTemplate.states.create("hover");
                    hs.properties.fill = am4core.color(colorMin).brighten(-0.5);
                }
            });
        }

        $(function () {
        });

    </script>
@stop

@section('content')
    <div class="row d-flex justify-content-center flex-wrap">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <a href="{{ route('admin.clients') }}">
                <div class="small-box bg-warning">
                    <div class="inner text-white">
                        <h3>{{$return['counts']['client']}}</h3>
                        <p>Clientes Registrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Últimos acessos de clientes</h5>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <div class="btn-group">
                        </div>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover" id="tableLastAccessClients">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Acesso</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($return['counts']['report']['lastLoginsClients'] as $client)
                                    <tr>
                                        <td>{{ $client['name'] }}</td>
                                        <td>{{ $client['email'] }}</td>
                                        <td data-order="{{ $client['last_login_order'] }}">{{ $client['last_login'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

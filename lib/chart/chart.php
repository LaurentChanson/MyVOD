<?php

//demo chart sur :
//http://jsfiddle.net/gh/get/jquery/1.9.1/highslide-software/highcharts.com/tree/master/samples/highcharts/demo/pie-basic/

class ChartSeries {

    public $name;
    public $serie_name = 'pourcentage';
    public $data = array();

}

class ChartSeriesData {

    public $name;
    public $y;
    public $url;

}

class Chart {

    static $highcharts_imported = false;

    private static function init() {
        if (self::$highcharts_imported == false) {
            //var_dump(self::$highcharts_imported);
            ?>

            <script src="lib/chart/highcharts.js"></script>
            <script src="lib/chart/highcharts-3d.js"></script>
            <script src="lib/chart/exporting.js"></script>


            <script>
            //http://www.highcharts.com/demo/pie-basic/dark-unica


                /**
                 * Dark theme for Highcharts JS
                 * @author Torstein Honsi
                 */

                Highcharts.theme = {
                    colors: ["#2b908f", "#90ee7e", "#f45b5b", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
                        "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
                    chart: {
                        backgroundColor: {
                            linearGradient: {x1: 0, y1: 0, x2: 1, y2: 1},
                            stops: [
                                [0, '#2a2a2b'],
                                [1, '#3e3e40']
                            ]
                        },
                        style: {
                            fontFamily: "'Unica One', sans-serif"
                        },
                        plotBorderColor: '#606063'
                    },
                    title: {
                        style: {
                            color: '#E0E0E3',
                            textTransform: 'uppercase',
                            fontSize: '20px'
                        }
                    },
                    subtitle: {
                        style: {
                            color: '#E0E0E3',
                            textTransform: 'uppercase'
                        }
                    },
                    xAxis: {
                        gridLineColor: '#707073',
                        labels: {
                            style: {
                                color: '#E0E0E3'
                            }
                        },
                        lineColor: '#707073',
                        minorGridLineColor: '#505053',
                        tickColor: '#707073',
                        title: {
                            style: {
                                color: '#A0A0A3'

                            }
                        }
                    },
                    yAxis: {
                        gridLineColor: '#707073',
                        labels: {
                            style: {
                                color: '#E0E0E3'
                            }
                        },
                        lineColor: '#707073',
                        minorGridLineColor: '#505053',
                        tickColor: '#707073',
                        tickWidth: 1,
                        title: {
                            style: {
                                color: '#A0A0A3'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        style: {
                            color: '#F0F0F0'
                        }
                    },
                    plotOptions: {
                        series: {
                            dataLabels: {
                                color: '#B0B0B3'
                            },
                            marker: {
                                lineColor: '#333'
                            }
                        },
                        boxplot: {
                            fillColor: '#505053'
                        },
                        candlestick: {
                            lineColor: 'white'
                        },
                        errorbar: {
                            color: 'white'
                        }
                    },
                    legend: {
                        itemStyle: {
                            color: '#E0E0E3'
                        },
                        itemHoverStyle: {
                            color: '#FFF'
                        },
                        itemHiddenStyle: {
                            color: '#606063'
                        }
                    },
                    credits: {
                        style: {
                            color: '#666'
                        }
                    },
                    labels: {
                        style: {
                            color: '#707073'
                        }
                    },
                    drilldown: {
                        activeAxisLabelStyle: {
                            color: '#F0F0F3'
                        },
                        activeDataLabelStyle: {
                            color: '#F0F0F3'
                        }
                    },
                    navigation: {
                        buttonOptions: {
                            symbolStroke: '#DDDDDD',
                            theme: {
                                fill: '#505053'
                            }
                        }
                    },
            // scroll charts
                    rangeSelector: {
                        buttonTheme: {
                            fill: '#505053',
                            stroke: '#000000',
                            style: {
                                color: '#CCC'
                            },
                            states: {
                                hover: {
                                    fill: '#707073',
                                    stroke: '#000000',
                                    style: {
                                        color: 'white'
                                    }
                                },
                                select: {
                                    fill: '#000003',
                                    stroke: '#000000',
                                    style: {
                                        color: 'white'
                                    }
                                }
                            }
                        },
                        inputBoxBorderColor: '#505053',
                        inputStyle: {
                            backgroundColor: '#333',
                            color: 'silver'
                        },
                        labelStyle: {
                            color: 'silver'
                        }
                    },
                    navigator: {
                        handles: {
                            backgroundColor: '#666',
                            borderColor: '#AAA'
                        },
                        outlineColor: '#CCC',
                        maskFill: 'rgba(255,255,255,0.1)',
                        series: {
                            color: '#7798BF',
                            lineColor: '#A6C7ED'
                        },
                        xAxis: {
                            gridLineColor: '#505053'
                        }
                    },
                    scrollbar: {
                        barBackgroundColor: '#808083',
                        barBorderColor: '#808083',
                        buttonArrowColor: '#CCC',
                        buttonBackgroundColor: '#606063',
                        buttonBorderColor: '#606063',
                        rifleColor: '#FFF',
                        trackBackgroundColor: '#404043',
                        trackBorderColor: '#404043'
                    },
            // special colors for some of the
                    legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
                    background2: '#505053',
                    dataLabelsColor: '#B0B0B3',
                    textColor: '#C0C0C0',
                    contrastTextColor: '#F0F0F3',
                    maskColor: 'rgba(255,255,255,0.3)'
                };

            // Apply the theme
                Highcharts.setOptions(Highcharts.theme);


            </script>



            <?php
        }
        self::$highcharts_imported = true;
    }

    public static function render_pie($div_id, ChartSeries $serie, $affichage3d = false, $s_color="" , $showInLegend=false) {
        self::init();
        //var_dump($serie->data);
        //var_dump(json_encode($serie->data,JSON_PRETTY_PRINT));
        ?>

        <div id="<?= $div_id; ?>" class="chart"></div>
        <script>

            $(function () {
                var chart=Highcharts.chart('<?=$div_id;?>', {
                //$('#<?= $div_id; ?>').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        height: '450',
                        //animation : false,//marche pas ici, il faut aller ds 'plotOptions'
                        <?php
                        if($affichage3d){
                        ?>
                        options3d: {
                            enabled: true,
                            alpha: 65,
                            beta: 0
                        }            
                        <?php
                        }
                        ?>
                    },
                    title: {
                        text: '<?= $serie->name; ?>'
                    },
                    tooltip: {
                        useHTML: true,
                        headerFormat: '<b>{point.key}</b><br>',
                        pointFormat: '{point.y} / {series.total} (soit <b>{point.percentage:.1f}%</b>)'
                    },
                    navigation: {
                        buttonOptions: {
                            enabled: true,
                        },
                        menuStyle: {
                            background: '#E0E0E0'
                        }
                    },
                    plotOptions: {
                        pie: {
                            animation: false,
                            allowPointSelect: true,
                            cursor: 'pointer',
                            depth: 60,
                            <?php
                                //affichage de la légende ou non
                                echo "\nshowInLegend: ".($showInLegend?'true':'false').",\n";
                                //changement des couleurs personnalisés
                                if(strlen($s_color)>0){
                                    echo "\ncolors: [".$s_color."],";
                                }
                            ?>
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b> : {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }

                        },
                        series: {
                            point: {
                                events: {
                                    click: function () {
                                        if (this.options.url != null && this.options.url.length > 3) {
                                            location.href = this.options.url;
                                        }
                                    }
                                }
                            }
                        }

                    },
                    series: [{
                            name: '<?= $serie->serie_name; ?>',
                            colorByPoint: true,
                            data: <?= json_encode($serie->data, JSON_PRETTY_PRINT); ?>
                        }]
                });
                
                
                
            //highchart n'arrive pas à redimentionner correctement
            //il faut le forcer manuellement
            //http://jsfiddle.net/gh/get/jquery/1.7.2/highslide-software/highcharts.com/tree/master/samples/highcharts/members/chart-reflow/

            setTimeout(function (e) {
                chart.reflow();
            }, 5);
                
                
            });//$(function () {




        </script>
        <?php
    }

}

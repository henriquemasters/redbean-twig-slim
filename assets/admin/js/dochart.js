/**
 * 
 * @param {type} el
 * @param {type} type
 * @param {type} data
 * @param {type} legend1
 * @param {type} legend2
 * @returns {undefined}
 */
function doChart(el, type, data, legend1, legend2) {
    var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
    };
    //
    var salesChart = new Chart(el, {
        type: type,
        data: data,
        options: {
            maintainAspectRatio: false,
            tooltips: {
                enabled: false
            },
            hover: {
                animationDuration: 0
            },
            animation: {
                duration: 1,
                onComplete: function () {
                    var chartInstance = this.chart,
                            ctx = chartInstance.ctx;
                    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';

                    this.data.datasets.forEach(function (dataset, i) {
                        var meta = chartInstance.controller.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = dataset.data[index];
                            ctx.fillText(data, bar._model.x, bar._model.y - 5);
                        });
                    });
                }
            },
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                        // display: false,
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            callback: function (value) {
                                if (value >= 1000) {
                                    value /= 1000
                                    value += ' Mi'
                                }
                                return value
                            }
                            //
                        }, ticksStyle)
                    }],
                xAxes: [{
                        display: true,
                        gridLines: {
                            display: true
                        },
                        ticks: ticksStyle
                    }]
            }
        }
    });
    //
    legend1.css('color', data.datasets[0].backgroundColor);
    legend2.css('color', data.datasets[1].backgroundColor);
}
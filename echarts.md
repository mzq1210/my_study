```js
var myChart = echarts.init(document.getElementById('order-main'));
        myChart.setOption({
            color: ['#4DDE6D', '#FF964D', '#5584FF', '#01C1B2', '#8272EC'],
            dataset: this.orderMain,
            xAxis: {
                type: 'category',
                axisLabel: {
                    textStyle: {
                        fontSize: this.fontSize
                    },
                    rotate: -52,
                }
            },
            yAxis: {
                type: 'value',
                name: '单位：元',
                splitLine: {
                    show:false
                },
            },
            legend: {
                orient: 'vertical',
                left: 'right',
                top: 'bottom',
                textStyle: {
                    fontSize: this.legendFontSize
                },
                formatter: (name) => {
                    let tarValue; let total = 0;
                    for (let i = 0; i < this.orderMain.source.length; i++) {
                        if (this.orderMain.source[i].shop_name === name) {
                            tarValue = this.orderMain.source[i].total;
                            total = total + this.orderMain.source[i].total;
                        }
                    }
                    return name == '金额'? `总计：${total}` : `${name} : ${tarValue}`;
                }
            },
            grid: [{
                top: '4%',
                width: '48%',
                bottom: '2%',
                left: '2%',
                containLabel: true
            }],
            series: [
                {
                    name: '金额',
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true,
                                formatter: function (params) {
                                    if(params.value[params.dimensionNames[params.encode.y[0]]] > 0){
                                        return params.value[params.dimensionNames[params.encode.y[0]]];
                                    }else{
                                        return '';
                                    }
                                },
                                position: 'top',
                                textStyle: {
                                    color: '#4DDE6D',
                                    fontSize: this.fontSize
                                }
                            }
                        }
                    }
                },
                {
                    name: '数量',
                    type: 'pie',
                    yAxisIndex: 1,
                    center: ['75%', '50%'],
                    radius: ['15%', '60%'],
                    itemStyle: {
                        normal: {
                            label: {
                                formatter: function (params) {
                                    return params.data.shop_name+'：'+ params.data.total;
                                },
                                textStyle: {
                                    fontSize: this.fontSize
                                }
                            }
                        }
                    }
                }
            ]
        });
```


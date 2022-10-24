var d = new Date();

var dd = String(d.getDate()).padStart(1, '0');
var mm = String(d.getMonth()).padStart(1, '0');
var yyyy = d.getFullYear();
var today = mm + '/' + dd + '/' + yyyy;

var monthsArr = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
];

function getWeeks() {
    var data = '';
    switch ($('#WeeksFilter').val()) {
        case '1st':
            data += '1st Week';
            break;
        case '2nd':
            data += '2nd Week';
            break;
        case '3rd':
            data += '3rd Week';
            break;
        case '4th':
            data += '4th Week';
            break;
        default:
            data += 'Weeks Error';
    }

    return data;
}

/** --------------------- YEAR */
$.ajax({
    type: 'POST',
    url: '../../Admin/process/Buffalo_Data_Chart.php',
    data: {
        Fetch_MP_Year: true,
        buffalo_milked_Year: true
    },
    success: ((response) => {


        const result = JSON.parse(response);
        var Yield = result['Yield'];
        var Milked = result['Milked'];
        var Year = result['Year'];

        var yieldLabel = [];
        var yieldData = [];
        var milkedData = [];

        console.log(Milked);

        $.map(Yield, (key, value) => {
            yieldLabel.push(value);
            yieldData.push(key);
        });

        $.map(Milked, (key, value) => {
            milkedData.push(key);
        });

        const data = {
            labels: yieldLabel,
            datasets: [{
                type: 'line',
                label: 'Total Buffalo has been Milked',
                backgroundColor: 'rgb(3, 138, 255, 0.7)',
                borderColor: 'rgb(3, 138, 255, 0.7)',
                data: milkedData,
            }, {
                type: 'bar',
                label: 'Milk Harvested in Liters',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: yieldData,
            }]
        };

        const config = {
            data: data,
            options: {
                maintainAspectRatio: false,
                // scales: {
                //     x: {
                //         grid: {
                //             color: 'red',
                //             borderColor: 'grey',
                //             tickColor: 'grey'
                //         }
                //     }
                // },
                plugins: {
                    title: {
                        display: true,
                        text: 'Year ' + Year,
                    }
                }
            }
        };

        /** --------------------- YEAR CHART */
        var myChart1 = new Chart(
            document.getElementById('myChart1').getContext('2d'),
            config
        );

        /** --------------------- FILTER YEAR */
        $('#YearsFilter').change(() => {
            $.ajax({
                type: 'POST',
                url: '../../Admin/process/Buffalo_Data_Chart.php',
                data: {
                    Fetch_MP_Year: true,
                    buffalo_milked_Year: true,
                    yearChartFilter: $('#YearsFilter').val()
                },
                success: ((response) => {

                    const result = JSON.parse(response);
                    var Yield = result['Yield'];
                    var Milked = result['Milked'];
                    var Year = result['Year'];

                    var yieldLabel = [];
                    var yieldData = [];
                    var milkedData = [];

                    $.map(Yield, (key, value) => {
                        yieldLabel.push(value);
                        yieldData.push(key);
                    });

                    $.map(Milked, (key, value) => {
                        milkedData.push(key);
                    });

                    myChart1.options.plugins.title.text = 'Year ' + $('#YearsFilter').val();
                    myChart1.data.datasets[0].data = milkedData;
                    myChart1.data.datasets[1].data = yieldData;
                    myChart1.data.labels = yieldLabel;
                    myChart1.update();

                })
            })
        })

        /** --------------------- MONTHS / WEEKS / DAYS */
        $.ajax({
            type: 'POST',
            url: '../../Admin/process/Buffalo_Data_Chart.php',
            data: {
                Fetch_MP_Weeks: true
            },
            success: ((response) => {

                const result = JSON.parse(response);

                var Yield = result['Yield'];
                var Milked = result['Milked'];
                var weeks = result['Weeks'];
                var label = [];
                var yieldData = [];
                var milkedData = [];
                var titles = '';
                var mm = String(d.getMonth()).padStart(1, '0');

                $('#MonthsFilter option').eq(mm).prop('selected', true);
                $('#WeeksFilter option').eq(weeks).prop('selected', true);
                var stringWeeks = getWeeks();
                titles += monthsArr[$('#MonthsFilter').val() - 1] + ', ';
                titles += stringWeeks;

                $.map(Yield, (key, value) => {
                    label.push(value);
                    yieldData.push(key);
                });

                $.map(Milked, (key, value) => {
                    milkedData.push(key);
                });

                const data = {
                    labels: label,
                    datasets: [{
                        type: 'line',
                        label: 'Total Buffalo has been Milked',
                        backgroundColor: 'rgb(3, 138, 255, 0.7)',
                        borderColor: 'rgb(3, 138, 255, 0.7)',
                        data: milkedData,
                    }, {
                        type: 'bar',
                        label: 'Milk Harvested in Liters',
                        backgroundColor: 'rgb(138,43,226)',
                        borderColor: 'rgb(138,43,226)',
                        data: yieldData,
                    }]
                };

                const config = {
                    data: data,
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: titles,
                            }
                        }
                    }
                };

                /** --------------------- WEEKS WITH DAYS CHART */
                var myChart2 = new Chart(
                    document.getElementById('myChart2').getContext('2d'),
                    config
                );

                /** --------------------- FILTER WEEKS WHEN YEARS CHANGE */
                $('#YearsFilter').change(() => {
                    $.ajax({
                        type: 'POST',
                        url: '../../Admin/process/Buffalo_Data_Chart.php',
                        data: {
                            Fetch_MP_Weeks: true,
                            yearsChartFilter: $('#YearsFilter').val(),
                            monthsChartFilter: 1,
                            weeksChartFilter: '1st'
                        },
                        success: ((response) => {
                            $('#WeeksFilter option').eq(0).prop('selected', true);
                            $('#MonthsFilter option').eq(0).prop('selected', true);

                            const result = JSON.parse(response);

                            var Yield = result['Yield'];
                            var Milked = result['Milked'];
                            var weeks = result['Weeks'];
                            var label = [];
                            var yieldData = [];
                            var milkedData = [];
                            var stringWeeks = getWeeks();
                            var titles = '';

                            titles += monthsArr[$('#MonthsFilter').val() - 1] + ', ';
                            titles += stringWeeks;

                            $.map(Yield, (key, value) => {
                                label.push(value);
                                yieldData.push(key);
                            });

                            $.map(Milked, (key, value) => {
                                milkedData.push(key);
                            });

                            myChart2.data.datasets[0].data = milkedData;
                            myChart2.data.datasets[1].data = yieldData;
                            myChart2.data.labels = label;
                            myChart2.options.plugins.title.text = titles;
                            myChart2.update();
                        })
                    })
                })
                /** --------------------- FILTER WEEKS BASE ON YEAR */
                $('#WeeksFilter').change(() => {
                    $.ajax({
                        type: 'POST',
                        url: '../../Admin/process/Buffalo_Data_Chart.php',
                        data: {
                            Fetch_MP_Weeks: true,
                            monthsChartFilter: $('#MonthsFilter').val(),
                            weeksChartFilter: $('#WeeksFilter').val(),
                            yearsChartFilter: $('#YearsFilter').val()
                        },
                        success: ((response) => {

                            const result = JSON.parse(response);

                            var Yield = result['Yield'];
                            var Milked = result['Milked'];
                            var weeks = result['Weeks'];
                            var label = [];
                            var yieldData = [];
                            var milkedData = [];
                            var stringWeeks = getWeeks();
                            var titles = '';

                            titles += monthsArr[$('#MonthsFilter').val() - 1] + ', ';
                            titles += stringWeeks;

                            $.map(Yield, (key, value) => {
                                label.push(value);
                                yieldData.push(key);
                            });

                            $.map(Milked, (key, value) => {
                                milkedData.push(key);
                            });

                            myChart2.data.datasets[0].data = milkedData;
                            myChart2.data.datasets[1].data = yieldData;
                            myChart2.data.labels = label;
                            myChart2.options.plugins.title.text = titles;
                            myChart2.update();
                        })
                    })
                })

                /** --------------------- FILTER WEEKS BASED ON MONTHS */
                $('#MonthsFilter').change(() => {
                    $.ajax({
                        type: 'POST',
                        url: '../../Admin/process/Buffalo_Data_Chart.php',
                        data: {
                            Fetch_MP_Weeks: true,
                            monthsChartFilter: $('#MonthsFilter').val(),
                            weeksChartFilter: $('#WeeksFilter').val(),
                            yearsChartFilter: $('#YearsFilter').val()
                        },
                        success: ((response) => {
                            $('#WeeksFilter option').eq(0).prop('selected', true);

                            const result = JSON.parse(response);

                            var Yield = result['Yield'];
                            var Milked = result['Milked'];
                            var weeks = result['Weeks'];
                            var label = [];
                            var yieldData = [];
                            var milkedData = [];
                            var titles = '';
                            var stringWeeks = getWeeks();

                            titles += monthsArr[$('#MonthsFilter').val() - 1] + ', ';
                            titles += stringWeeks;

                            $.map(Yield, (key, value) => {
                                label.push(value);
                                yieldData.push(key);
                            });

                            $.map(Milked, (key, value) => {
                                milkedData.push(key);
                            });

                            myChart2.data.datasets[0].data = milkedData;
                            myChart2.data.datasets[1].data = yieldData;
                            myChart2.data.labels = label;
                            myChart2.options.plugins.title.text = titles;
                            myChart2.update();
                        })
                    })
                })
            })
        })
    })
})
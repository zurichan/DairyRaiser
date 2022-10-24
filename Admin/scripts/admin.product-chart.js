$(document).ready(() => {

    $.ajax({
        type: 'POST',
        url: '../../Admin/process/Product_Data_Chart.php',
        data: {
            stock_percentage: true
        },
        error: function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.log(msg);
        },
        success: ((response) => {

            const result = JSON.parse(response);
            var labels = [];
            var datas = [];
            
            $.map(result['name'], (value, name) => {
                labels.push(name);
                datas.push(value);
            });

            var data = {
                labels: labels,
                datasets: [{
                    label: 'Percentage of Stocks',
                    data: datas,
                    backgroundColor: 'rgb(3, 138, 255, 0.7)',
                    borderColor: 'rgb(3, 138, 255, 0.7)',
                }]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                color: 'red',
                                borderColor: 'grey',
                                tickColor: 'grey'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Product Stocks',
                        }
                    }
                }
            };

            const myChart = new Chart(
                document.getElementById('productDashboard').getContext('2d'),
                config
            );
        })
    })
});

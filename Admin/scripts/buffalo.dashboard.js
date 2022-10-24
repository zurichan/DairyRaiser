$.ajax({
    type: 'POST',
    url: '../../Admin/process/Buffalo_Data_Chart.php',
    data: {
     Fetch_Stats: true
    }, success: ((response) => {

        const result = JSON.parse(response);

        var total = result['total'];
        var normal = result['normal'];
        var lactating = result['lactating'];
        var sold = result['sold'];
        var sick = result['sick'];
        var deceased = result['deceased'];

        const data = {
            labels: [
              'Normal',
              'Lactating',
              'Sold',
              'Sick',
              'Deceased'
            ],
            datasets: [{
              label: 'Buffalos Status',
              data: [normal, lactating, sold, sick, deceased],
              backgroundColor: [
                'rgb(60, 179, 113)', // Normal
                'rgb(0, 128, 255)', //Lactating
                'rgb(255, 165, 0)', // Sold
                'rgb(255, 19, 71)', // Sick
                'rgb(108, 122, 137)' // Deceased
              ],
              hoverOffset: 9
            }]
          };

          const config = {
            type: 'doughnut',
            data: data,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Buffalos Status',
                    }
                }
            }
          };

          var buffaloDashboard = new Chart(
            document.getElementById('buffaloDashboard').getContext('2d'),
            config
        );
    })
});
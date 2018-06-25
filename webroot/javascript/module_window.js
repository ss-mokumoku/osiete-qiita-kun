function popular_tags_graph_window(popular_tags_name, popular_tags_count) {
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: popular_tags_name,
            datasets: [{
                label: 'タグ数',
                backgroundColor: "rgba(0,200,1,.2)",
                data: popular_tags_count
            }]
        },
        //グラフの最低値を指定する
        options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                min: 0,
                                beginAtZero: true
                            }
                        }]
                    }
                }
    });
}

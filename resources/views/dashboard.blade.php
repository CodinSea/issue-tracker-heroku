@extends('layouts.master')
@section('title', 'Dashboard')
@section('content')
<div class="container">
    <div class="row row-cols-3">
        <div>
            <canvas id="ticketsByStatus" style="width:100%;max-width:250px"></canvas>
        </div>
        <div>
            <canvas id="ticketsByPriority" style="width:100%;max-width:250px"></canvas>
        </div>
        <div>
            <canvas id="ticketsByType" style="width:100%;max-width:250px"></canvas>
        </div>
    </div>
</div>

<script>
    var ticketsByStatus = <?php echo json_encode($ticketsByStatus); ?>;
    var xValuesByStatus = [];
    var yValuesByStatus = [];
    for(var i = 0; i < ticketsByStatus.length; i++){
        xValuesByStatus.push(ticketsByStatus[i][0]);
        yValuesByStatus.push(ticketsByStatus[i][1]);
    }

    var barColors = [  
        "#34495e",      
        "#d35400",
        "#f8c471",
        "#707b7c"
    ];

    new Chart("ticketsByStatus", {
        type: "doughnut",
        data: {
            labels: xValuesByStatus,
            datasets: [{
                backgroundColor: barColors,
                data: yValuesByStatus
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    position: "bottom",
                    text: "Tickets by Status",
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: false
                }
            }
        }
    }); 

    var ticketsByPriority = <?php echo json_encode($ticketsByPriority); ?>;
    var xValuesByPriority = [];
    var yValuesByPriority = [];
    for(var i = 0; i < ticketsByPriority.length; i++){
        xValuesByPriority.push(ticketsByPriority[i][0]);
        yValuesByPriority.push(ticketsByPriority[i][1]);
    }

    var barColors = [
        "#34495e",      
        "#d35400",
        "#f8c471",
        "#707b7c"
    ];

    new Chart("ticketsByPriority", {
        type: "doughnut",
        data: {
            labels: xValuesByPriority,
            datasets: [{
                backgroundColor: barColors,
                data: yValuesByPriority
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    position: "bottom",
                    text: "Tickets by Priority",
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: false
                }
            }
        }
    }); 

    var ticketsByType = <?php echo json_encode($ticketsByType); ?>;
    var xValuesByType = [];
    var yValuesByType = [];
    for(var i = 0; i < ticketsByType.length; i++){
        xValuesByType.push(ticketsByType[i][0]);
        yValuesByType.push(ticketsByType[i][1]);
    }

    var barColors = [
        "#34495e",      
        "#d35400",
        "#f8c471",
        "#707b7c"
    ];

    new Chart("ticketsByType", {
        type: "doughnut",
        data: {
            labels: xValuesByType,
            datasets: [{
                backgroundColor: barColors,
                data: yValuesByType
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    position: "bottom",
                    text: "Tickets by Type",
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: false
                }
            }
        }
    });   
</script>
@endsection

<div>
    <canvas id="{{$student->id}}"></canvas>
</div>

@script
<script>
    let labels = $wire.get('labels')
    let dataset1 = $wire.get('dataset1')
    let dataset2 = $wire.get('dataset2')
    let dataset3 = $wire.get('dataset3')
    
    const ctx = document.getElementById('{{$student->id}}').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Perbandingan Rata-rata',
                    data: dataset1,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                },
                {
                    label: 'Rapor 50%, Tulis 30%, Praktek 20%',
                    data: dataset2,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.4,
                },
                {
                    label: 'Rapor 60%, Tulis 30%, Praktek 10%',
                    data: dataset3,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.4,
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: '{{$student->name}}'
                }
            }
        }
    });
</script>
@endscript
<div>
    <canvas id="{{$student->id}}"></canvas>
</div>

@script
<script>
    const ctx = document.getElementById('{{$student->id}}').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                    label: 'Perbandingan Rata-rata',
                    data: @json($dataset1),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                },
                {
                    label: 'Rapor 50%, Tulis 30%, Praktek 20%',
                    data: @json($dataset2),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.4,
                },
                {
                    label: 'Rapor 60%, Tulis 30%, Praktek 10%',
                    data: @json($dataset3),
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
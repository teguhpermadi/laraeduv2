<div>
    <canvas id="{{$student->id}}"></canvas>
</div>

@script
<script>
    let labels = $wire.get('labels')
    let dataset1 = $wire.get('dataset1')
    let dataset2 = $wire.get('dataset2')
    let weight_report1 = $wire.get('weight_report1')
    let weight_written_exam1 = $wire.get('weight_written_exam1')
    let weight_practical_exam1 = $wire.get('weight_practical_exam1')
    let weight_report2 = $wire.get('weight_report2')
    let weight_written_exam2 = $wire.get('weight_written_exam2')
    let weight_practical_exam2 = $wire.get('weight_practical_exam2')
    
    const ctx = document.getElementById('{{$student->id}}').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Rapor ' + weight_report1 + '%, Tulis ' + weight_written_exam1 + '%, Praktek ' + weight_practical_exam1 + '%',
                    data: dataset1,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                },
                {
                    label: 'Rapor ' + weight_report2 + '%, Tulis ' + weight_written_exam2 + '%, Praktek ' + weight_practical_exam2 + '%',
                    data: dataset2,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
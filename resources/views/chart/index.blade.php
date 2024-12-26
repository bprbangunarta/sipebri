@extends('theme.app')
@section('title', 'Statistic')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="card">
                            <div class="card-body">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Pendaftaran Kredit {{ $currentYear }}</h4>
                                        <div class="box-tools">
                                            &nbsp;
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="card-body">
                                            <label>TANGGAL SURVEI MULAI DARI</label>
                                            <input type="date" class="form-control" name="tahun" id="tahun"
                                                style="margin-top:-5px;">
                                            </select>
                                            <div class="chart">
                                                <canvas id="barChart"
                                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function populateYearSelect() {
            var select = document.getElementById('yearSelect');

            var currentYear = new Date().getFullYear();

            var startYear = 2000;

            for (var year = startYear; year <= 2100; year++) {

                var option = document.createElement("option");
                option.value = year;
                option.text = year;
                select.appendChild(option);
            }
        }

        window.onload = function() {
            populateYearSelect();
        }

        // $('#yearSelect').select2({
        //     width: '70px',
        //     height: '10px'
        // })

        $(document).ready(function() {
            $('[data-card-widget="collapse"]').click(function() {
                var card = $(this).closest('.card');
                card.find('.card-body').slideToggle();
                card.toggleClass('collapsed-card');
            });

            $('.tahun').select2()
        });

        $(function() {
            var barChartCanvas = $('#barChart').get(0).getContext('2d');
            var months = {!! json_encode($months) !!};
            var count = {!! json_encode($counts) !!};
            var count_realisasi = {!! json_encode($count_realisasi) !!};

            var barChartData = {
                labels: months,
                datasets: [{
                        label: 'Pendaftaran',
                        backgroundColor: '#0D92F4',
                        borderWidth: 2,
                        borderRadius: 5,
                        borderSkipped: false,
                        data: count
                    },
                    {
                        label: 'Realisasi',
                        backgroundColor: '#F95454',
                        borderWidth: 2,
                        borderRadius: 5,
                        borderSkipped: false,
                        data: count_realisasi
                    }
                ]
            };

            var barChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                datasetFill: false,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            };

            new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
            });
        });
    </script>
@endpush

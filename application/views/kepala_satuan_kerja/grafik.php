<!-- Page -->
<div class="page animsition">
    <div class="page-header">
        <h3 class="page-title">Grafik Proyek</h3>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('kepala_satuan_kerja') ?>">Dashboard</a></li>
            <li class="active">Grafik Proyek</li>
        </ol>
    </div>
    <div class="page-content">
        <!-- Panel Basic -->
        <div class="panel">
            <header class="panel-heading">
                <div class="panel-actions"></div>
                <h3 class="panel-title">Grafik Keseluruhan Proyek</h3>
                <button onclick="downloadPDF();" id="unduh-laporan" type="button" class="btn btn-primary"><i class="fa fa-download"></i> Unduh Grafik Laporan</button>
            </header>
            <div class="panel-body">
            	<div class="row">
            		<div>
						<!-- <h2>Laporan Keseluruhan Proyek</h2> -->

	                    <canvas id="laporan-keseluruhan-proyek" width="800" height="450"></canvas>

	                </div>
            	</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script>
<script type="text/javascript" src="<?= base_url( 'assets/js/html2canvas.js' ) ?>"></script>
<!-- <script type="text/javascript" src="<?= base_url( 'assets/vendor/chart-js/Chart.min.js' ) ?>"></script> -->
<script type="text/javascript">
	$( document ).ready(function() {

		var labels 	= [];
		var data 	= [];

		<?php foreach ( $jumlah_proyek as $row ): ?>

		labels.push('<?= $row->nama ?>');
		data.push('<?= $row->jumlah_proyek ?>');

		<?php endforeach; ?>

		var datasets = [];
		datasets.push({
			label: 'Jumlah Proyek: ',
			backgroundColor: 'rgba(26,179,148,0.5)',
			borderColor: "rgba(26,179,148,0.8)",
            pointBackgroundColor: "rgba(26,179,148,0.5)",
            pointBorderColor: "rgba(26,179,148,0.8)",
            data: data
		});

		var ctx = new Chart(document.getElementById("laporan-keseluruhan-proyek"), {
		    type: 'bar',
		    data: {
		      labels: labels,
		      datasets: datasets
		    },
		    options: {
		      legend: { display: false },
		      title: {
		        display: true,
		        text: 'Grafik Jumlah Proyek Setiap Kabupaten'
		      },
		      scales: {
		      	yAxes: [{
		      		ticks: {
		      			min: 0,
		      			max: <?= count( $proyek ) ?>,
		      			stepSize: 1
		      		}
		      	}]
		      }
		    }
		});

	});

	function getRandom(max) {
	  	return Math.random() * Math.floor(max);
	}

	function downloadPDF() {
		html2canvas(document.getElementById("laporan-keseluruhan-proyek"), {
            useCORS: true,
            onrendered: function( canvas ) {

                var imgData = canvas.toDataURL( 'image/png' );
                var doc = new jsPDF( 'p', 'mm' );
                doc.addImage( imgData, 'PNG', 3, 3 );
                doc.save( 'laporan-jumlah-proyek.pdf' );

            }
        });
	 }
</script>
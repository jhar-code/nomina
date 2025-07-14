<?php
include 'includes/session.php';

function generateRow($from, $to, $conn, $deduction)
{
	$contents = '';

	$sql = "SELECT *, sum(num_hr) AS total_hr, attendance.employee_id AS empid FROM attendance LEFT JOIN employees ON employees.id=attendance.employee_id LEFT JOIN position ON position.id=employees.position_id WHERE date BETWEEN '$from' AND '$to' GROUP BY attendance.employee_id ORDER BY employees.lastname ASC, employees.firstname ASC";

	$query = $conn->query($sql);
	$total = 0;
	$counter = 0;

	while ($row = $query->fetch_assoc()) {
		$empid = $row['empid'];

		$casql = "SELECT *, SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";

		$caquery = $conn->query($casql);
		$carow = $caquery->fetch_assoc();
		$cashadvance = $carow['cashamount'];

		$gross = $row['rate'] * $row['total_hr'];
		$total_deduction = $deduction + $cashadvance;
		$net = $gross - $total_deduction;

		$total += $net;
		$counter++;

		// Alternar colores de fila para mejor legibilidad
		$row_color = ($counter % 2 == 0) ? '#f8f9fa' : '#ffffff';

		$contents .= '
        <tr style="background-color: ' . $row_color . ';">
            <td style="padding: 6px;">' . $row['lastname'] . ', ' . $row['firstname'] . '</td>
            <td style="padding: 6px;">' . $row['employee_id'] . '</td>
            <td style="padding: 6px; text-align: right;">' . number_format($net, 2) . '</td>
        </tr>
        ';
	}

	$contents .= '
        <tr style="background-color: #e1f5fe; font-weight: bold;">
            <td colspan="2" style="padding: 8px; text-align: right;">Total General</td>
            <td style="padding: 8px; text-align: right;">' . number_format($total, 2) . '</td>
        </tr>
    ';
	return $contents;
}

$range = $_POST['date_range'];
$ex = explode(' - ', $range);
$from = date('Y-m-d', strtotime($ex[0]));
$to = date('Y-m-d', strtotime($ex[1]));

$sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
$query = $conn->query($sql);
$drow = $query->fetch_assoc();
$deduction = $drow['total_amount'];

$from_title = date('M d, Y', strtotime($ex[0]));
$to_title = date('M d, Y', strtotime($ex[1]));

require_once __DIR__ . '/../vendor/autoload.php';

// Configuración mejorada del PDF
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Reporte de Nómina: ' . $from_title . ' - ' . $to_title);
$pdf->SetMargins(15, 15, 15);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

// Estilo CSS mejorado
$style = <<<EOD
<style>
    .header {
        text-align: center;
        margin-bottom: 20px;
    }
    .company-name {
        font-size: 18px;
        font-weight: bold;
        color: #2c3e50;
    }
    .report-title {
        font-size: 16px;
        color: #3498db;
        margin-bottom: 5px;
    }
    .period {
        font-size: 14px;
        color: #7f8c8d;
        margin-bottom: 15px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    .table th {
        background-color: #3498db;
        color: white;
        padding: 8px;
        text-align: center;
        font-weight: bold;
    }
    .table td {
        padding: 6px;
        border: 1px solid #e0e0e0;
    }
    .total-row {
        font-weight: bold;
        background-color: #e1f5fe;
    }
    .footer {
        font-size: 9px;
        text-align: center;
        margin-top: 20px;
        color: #95a5a6;
    }
</style>
EOD;

$content = $style;
$content .= '
    <div class="header">
        <div class="company-name">SystemDev</div>
        <div class="report-title">REPORTE DE NÓMINA</div>
        <div class="period">Periodo: ' . $from_title . ' al ' . $to_title . '</div>
    </div>
    
    <table class="table">
        <tr>
            <th width="40%">Nombre del Empleado</th>
            <th width="30%">ID de Empleado</th>
            <th width="30%">Salario Neto</th>
        </tr>
';

$content .= generateRow($from, $to, $conn, $deduction);

$content .= '
    </table>
    <div class="footer">
        Documento generado el ' . date('d/m/Y H:i:s') . '<br>
        Sistema de Gestión de Nóminas - SystemDev
    </div>
';

$pdf->writeHTML($content, true, false, true, false, '');
$pdf->Output('nomina_' . $from_title . '_' . $to_title . '.pdf', 'I');

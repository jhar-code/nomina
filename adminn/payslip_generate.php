<?php
include 'includes/session.php';

$range = $_POST['date_range'];
$ex = explode(' - ', $range);
$from = date('Y-m-d', strtotime($ex[0]));
$to = date('Y-m-d', strtotime($ex[1]));

// Obtener deducciones totales
$sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
$query = $conn->query($sql);
$drow = $query->fetch_assoc();
$deduction = $drow['total_amount'];

$from_title = date('M d, Y', strtotime($ex[0]));
$to_title = date('M d, Y', strtotime($ex[1]));

require_once __DIR__ . '/../vendor/autoload.php';

// Configuración del PDF profesional
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Recibo de Nómina: ' . $from_title . ' - ' . $to_title);
$pdf->SetHeaderData('', '', '', '');
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont('helvetica');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetMargins(15, 15, 15);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

// css de factura
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
    .document-title {
        font-size: 14px;
        color: #7f8c8d;
        margin-bottom: 15px;
    }
    .period {
        font-size: 12px;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    .employee-info {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th {
        background-color: #3498db;
        color: white;
        padding: 8px;
        text-align: left;
    }
    .table td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
    }
    .table tr:nth-child(even) {
        background-color: #d1c4e9;
    }
    .total-row {
        font-weight: bold;
        background-color: #d1c4e9; !important;
    }
    .signature {
        margin-top: 40px;
    }
    .signature-line {
        border-top: 1px solid #333;
        width: 200px;
        margin-top: 50px;
    }
    .footer {
        font-size: 10px;
        text-align: center;
        margin-top: 20px;
        color: #7f8c8d;
    }
</style>
EOD;

$sql = "SELECT *, SUM(num_hr) AS total_hr, attendance.employee_id AS empid, employees.employee_id AS employee FROM attendance LEFT JOIN employees ON employees.id=attendance.employee_id LEFT JOIN position ON position.id=employees.position_id WHERE date BETWEEN '$from' AND '$to' GROUP BY attendance.employee_id ORDER BY employees.lastname ASC, employees.firstname ASC";

$query = $conn->query($sql);
while ($row = $query->fetch_assoc()) {
	$empid = $row['empid'];

	$casql = "SELECT *, SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";

	$caquery = $conn->query($casql);
	$carow = $caquery->fetch_assoc();
	$cashadvance = $carow['cashamount'];

	$gross = $row['rate'] * $row['total_hr'];
	$total_deduction = $deduction + $cashadvance;
	$net = $gross - $total_deduction;

	$contents = $style . '
    <div class="header">
        <div class="company-name">System Dev</div>
        <div class="document-title">RECIBO DE NÓMINA</div>
        <div class="period">Periodo: ' . $from_title . ' al ' . $to_title . '</div>
    </div>
    
    <div class="employee-info">
        <table width="100%">
            <tr>
                <td width="50%"><strong>Empleado:</strong> ' . $row['firstname'] . ' ' . $row['lastname'] . '</td>
                <td width="50%"><strong>ID Empleado:</strong> ' . $row['employee'] . '</td>
            </tr>
            <tr>
                <td><strong>Posición:</strong> ' . $row['description'] . '</td>
                <td><strong>Tasa por Hora:</strong> $' . number_format($row['rate'], 2) . '</td>
            </tr>
        </table>
    </div>
    
    <table class="table">
        <tr>
            <th width="70%">Concepto</th>
            <th width="30%" align="right">Monto</th>
        </tr>
        <tr>
            <td>Horas trabajadas (' . number_format($row['total_hr'], 2) . ' horas)</td>
            <td align="right">$' . number_format($gross, 2) . '</td>
        </tr>
        <tr>
            <td>Deducciones</td>
            <td align="right">$' . number_format($deduction, 2) . '</td>
        </tr>
        <tr>
            <td>Adelanto de efectivo</td>
            <td align="right">$' . number_format($cashadvance, 2) . '</td>
        </tr>
        <tr class="total-row">
            <td><strong>Total deducciones</strong></td>
            <td align="right"><strong>$' . number_format($total_deduction, 2) . '</strong></td>
        </tr>
        <tr class="total-row">
            <td><strong>SALARIO NETO</strong></td>
            <td align="right"><strong>$' . number_format($net, 2) . '</strong></td>
        </tr>
    </table>
    
    <div class="signature">
        <div class="signature-line"></div>
        <div>Firma del Empleado</div>
    </div>
    
    <div class="footer">
        Este documento es generado automáticamente por el sistema de nóminas de SystemDev<br>
        Fecha de generación: ' . date('M d, Y H:i:s') . '
    </div>
    ';

	$pdf->writeHTML($contents, true, false, true, false, '');
	if ($query->num_rows > 1) {
		$pdf->AddPage();
	}
}

$pdf->Output('recibo_nomina_' . $from_title . '_' . $to_title . '.pdf', 'I');

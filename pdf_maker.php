<?php
require 'database_connection.php'; 
include_once('tcpdf_6_2_13/tcpdf/tcpdf.php');

$MST_ID = isset($_GET['MST_ID']) ? $_GET['MST_ID'] : die('MST_ID parameter not provided.');

$inv_mst_query = "SELECT T1.MST_ID, T1.INV_NO, T1.CUSTOMER_NAME, T1.CUSTOMER_MOBILENO, T1.ADDRESS FROM INVOICE_MST T1 WHERE T1.MST_ID='" . $MST_ID . "'";             
$inv_mst_results = mysqli_query($con, $inv_mst_query);   
$count = mysqli_num_rows($inv_mst_results);  
if ($count > 0) {
  $inv_mst_data_row = mysqli_fetch_array($inv_mst_results, MYSQLI_ASSOC);

  //Code for generating PDF
  	$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);  
	//$pdf->SetTitle("Export HTML Table data to PDF using TCPDF in PHP");  
	$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
	$pdf->SetDefaultMonospacedFont('helvetica');  
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
	$pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
	$pdf->setPrintHeader(false);  
	$pdf->setPrintFooter(false);  
	$pdf->SetAutoPageBreak(TRUE, 10);  
	$pdf->SetFont('helvetica', '', 12);  
	$pdf->AddPage(); //default A4
	//$pdf->AddPage('P','A5'); //when you require custome page size 
	
	$content = ''; 

	$content .= '
	<style type="text/css">
	body{
	font-size:12px;
	line-height:24px;
	font-family:"Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
	color:#000;
	}
	</style>    
	<table cellpadding="0" cellspacing="0" style="border:1px solid #ddc;width:100%;">
	<table style="width:100%;" >
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="center"><b>J A N I T H | C R E A T O R</b></td></tr>
	<tr><td colspan="2" align="center"><b>CONTACT: +94 77777777</b></td></tr>
	<tr><td colspan="2" align="center"><b>WEBSITE: WWW.INFOPACE.ONLINE</b></td></tr>
	<tr><td colspan="2"><b>CUST.NAME: '.$inv_mst_data_row['CUSTOMER_NAME'].' </b></td></tr>
	<tr><td><b>MOB.NO: '.$inv_mst_data_row['CUSTOMER_MOBILE_NO'].' </b></td><td align="right"><b>BILL DT.: '.date("d-m-Y").'</b> </td></tr>
	<tr><td>&nbsp;</td><td align="right"><b>BILL NO.: '.$inv_mst_data_row['INV_NO'].'</b></td></tr>
	<tr><td colspan="2" align="center"><b>INVOICE</b></td></tr>
	<tr class="heading" style="background:#eee;border-bottom:1px solid #ddd;font-weight:bold;">
		<td>
			TYPE OF WORK
		</td>
		<td align="right">
			AMOUNT
		</td>
	</tr>';
		$total=0;
		$inv_det_query = "SELECT T2.PRODUCT_NAME, T2.AMOUNT FROM INVOICE_DET T2 WHERE T2.MST_ID='".$MST_ID."' ";
		$inv_det_results = mysqli_query($con,$inv_det_query);    
		while($inv_det_data_row = mysqli_fetch_array($inv_det_results, MYSQLI_ASSOC))
		{	
		$content .= '
		  <tr class="itemrows">
			  <td>
				  <b>'.$inv_det_data_row['PRODUCT_NAME'].'</b>
				  <br>
				  <i>Write any remarks</i>
			  </td>
			  <td align="right"><b>
				  '.$inv_det_data_row['AMOUNT'].'
			  </b></td>
		  </tr>';
		$total=$total+$inv_det_data_row['AMOUNT'];
		}
		$content .= '<tr class="total"><td colspan="2" align="right">------------------------</td></tr>
		<tr><td colspan="2" align="right"><b>GRAND&nbsp;TOTAL:&nbsp;'.$total.'</b></td></tr>
		<tr><td colspan="2" align="right">------------------------</td></tr>
	<tr><td colspan="2" align="right"><b>PAYMENT MODE: CASH/ONLINE </b></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="center"><b>THANK YOU ! VISIT AGAIN</b></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	</table>
</table>'; 
$pdf->writeHTML($content);

$file_location = "F:\\PHP_xampp\\1 NEW XAMPP\\htdocs\\TCPDF\\uploads\\"; // Change this to the actual path on your server
$datetime = date('dmY_hms');
$file_name = "INV_" . $datetime . ".pdf";
ob_end_clean();

if ($_GET['ACTION'] == 'VIEW') {
  $pdf->Output($file_name, 'I'); // I means Inline view
} elseif ($_GET['ACTION'] == 'DOWNLOAD') {
  $pdf->Output($file_name, 'D'); // D means download
} elseif ($_GET['ACTION'] == 'UPLOAD') {
  $pdf->Output($file_location . $file_name, 'F'); // F means upload PDF file on some folder
  echo "Upload successfully!!";
}
} else {
echo 'Record not found for PDF.';
}
?>
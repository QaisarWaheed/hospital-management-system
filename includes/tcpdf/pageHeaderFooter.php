<?php
class MYPDF extends TCPDF {
	public function Header() {
		// Logo
//		$image_file = K_PATH_IMAGES.'logo_example.jpg';
//		$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 8);
		// Title
		if (isset($_SESSION['user_name'])) {
			$userName = $_SESSION['user_name'];
		}else{
			header('location: ../index.php');
		}
		$this->Cell(0, 10, 'AL-NOOR CITY HOUSING SCHEME,KOT ADDU', 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 10, 'user:'.$userName, 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10,'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'L', 0, '', 0, false, 'T', 'M');
		$timestamp = date("F d, Y");
		$this->Cell(0, 10, $timestamp, 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
}
?>
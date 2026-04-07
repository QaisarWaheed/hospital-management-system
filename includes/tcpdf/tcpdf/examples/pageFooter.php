<?php
class MYPDF extends TCPDF {
	public function Header() {}
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
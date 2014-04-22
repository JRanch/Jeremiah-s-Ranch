<?php
require_once('fpdi.php');

class FPDI_EAN13 extends FPDI
{	
	## $mt is used by the multi ticket.
	function EAN13($x, $y, $barcode, $pdf_use_qrcode, $h=10, $w=.35, $mt=0)
	{	
		
		$this->Barcode($x, $y, $barcode, $pdf_use_qrcode, $h,$w,13, $mt);
		
	}

	function UPC_A($x, $y, $barcode, $pdf_use_qrcode=0, $h=10, $w=.35)
	{
		$this->Barcode($x,$y,$barcode, $pdf_use_qrcode=0, $h, $w,12);
	}
	
	function getBar()
	{
		return $this->getBarcode;
	}	

	function GetCheckDigit($barcode)
	{
		//Compute the check digit
		$sum=0;
		for($i=1;$i<=11;$i+=2)
			$sum+=3*$barcode[$i];
		for($i=0;$i<=10;$i+=2)
			$sum+=$barcode[$i];
		$r=$sum%10;
		if($r>0)
			$r=10-$r;
		return $r;
	}

	function TestCheckDigit($barcode)
	{
		//Test validity of check digit
		$sum=0;
		for($i=1;$i<=11;$i+=2)
			$sum+=3*$barcode[$i];
		for($i=0;$i<=10;$i+=2)
			$sum+=$barcode[$i];
		return ($sum+$barcode[12])%10==0;
	}

	function Barcode($x, $y, $barcode, $pdf_use_qrcode, $h, $w, $len, $mt)
	{ 

		//Padding
		$barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
		if($len==12)
			$barcode='0'.$barcode;
		//Add or control the check digit
		if(strlen($barcode)==12)
			$barcode.=$this->GetCheckDigit($barcode);
		elseif(!$this->TestCheckDigit($barcode))
			$this->Error('Incorrect check digit');
		//Convert digits to bars
		$codes=array(
			'A'=>array(
				'0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
				'5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
			'B'=>array(
				'0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
				'5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
			'C'=>array(
				'0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
				'5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
			);
		$parities=array(
			'0'=>array('A','A','A','A','A','A'),
			'1'=>array('A','A','B','A','B','B'),
			'2'=>array('A','A','B','B','A','B'),
			'3'=>array('A','A','B','B','B','A'),
			'4'=>array('A','B','A','A','B','B'),
			'5'=>array('A','B','B','A','A','B'),
			'6'=>array('A','B','B','B','A','A'),
			'7'=>array('A','B','A','B','A','B'),
			'8'=>array('A','B','A','B','B','A'),
			'9'=>array('A','B','B','A','B','A')
			);
		$code='101';
		$p=$parities[$barcode[0]];
		for($i=1;$i<=6;$i++)
			$code.=$codes[$p[$i-1]][$barcode[$i]];
		$code.='01010';
		for($i=7;$i<=12;$i++)
			$code.=$codes['C'][$barcode[$i]];
		$code.='101';
		
		## If set in the config choose 1D bars
		if ($pdf_use_qrcode == 0) {
		
			## Draw bars ##
			for($i=0;$i<strlen($code);$i++)
			{
				if($code[$i]=='1')
					$this->Rect($x+$i*$w,$y,$w,$h,'F');
			}
			//Print text uder barcode
			$this->SetFont('Arial','',10);
			$this->Text($x,$y+$h+11/$this->k,substr($barcode,-$len));
		
		}
		
		## Connecting the DB
		$db = JFactory::getDBO();
		## Making the query for getting the config and assign to $config
		$sql='SELECT qr_width FROM #__ticketmaster_config WHERE configid = 1'; 
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		## we do want to save the barcode now in the DB.
		$new_barcode = substr($barcode,-$len);

		## Starting a session.
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$session->set('barcode', $new_barcode);
		
		if ($mt == 1){		
			return true;
		}
		
		## Creating the QR Code for printing.	
		if ($pdf_use_qrcode == 1) {
			
			
		   $remoteFile ='http://chart.apis.google.com/chart?chs='.$config->qr_width.'x'.$config->qr_width.'&cht=qr&chld=L|0&chl='.$new_barcode.'';
		   
		   $localFile  = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'cache'.DS.$new_barcode.'.png';

		   self::get_qr_image($remoteFile,$localFile);	
		
		}
	}


	function get_qr_image($remoteFile,$localFile)
	{

		## Using the cache folder to save the file.
		$cache_folder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'cache'.DS;
		
		## If the folder doesn't exsist.
		if ( !is_dir($cache_folder) )
		{
			## Making the folder right now.			
			## Now move the file away for security reasons
			## Import the Joomla! Filesystem.
			jimport('joomla.filesystem.file');
			JFolder::create($cache_folder, 0755);
		}
		
		$ch = curl_init();
		$timeout = 0;
		curl_setopt ($ch, CURLOPT_URL, $remoteFile);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		$image = curl_exec($ch);
		curl_close($ch); 
		$f = fopen($localFile, 'w');
		fwrite($f, $image);
		fclose($f);
	} 
		
}
?>

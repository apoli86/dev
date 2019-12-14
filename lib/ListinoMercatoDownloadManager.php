<?php
	class ListinoMercatoDownloadManager
	{
		private $dayOfWeek;
		
		public function __construct()
		{
			$this->dayOfWeek = array();
			
			$this->dayOfWeek['Sunday'] = 'Dom';
			$this->dayOfWeek['Monday'] = 'Lun';
			$this->dayOfWeek['Tuesday'] = 'Mar';
			$this->dayOfWeek['Wednesday'] = 'Mer';
			$this->dayOfWeek['Thursday'] = 'Gio';
			$this->dayOfWeek['Friday'] = 'Ven';
			$this->dayOfWeek['Saturday'] = 'Sab';
		}
		
		function download($directory, $listino)
		{
			$date      = $listino->getDate();
			$file      = date("d", $date) . "-" . date("m", $date) . "-" . date("Y", $date) . " - " . $this->dayOfWeek[date("l", $date)] . ".pdf";
			
			$this->downloadHelper($listino->getUrl(), $directory . "/" . $file);
			
			return $directory . "/" . $file;
		}
		
		function downloadAndSend($directory, $listino)
		{
			$pdfPath = $this->download($directory, $listino);
			$pdfFile = basename($pdfPath);
			
			header("Content-type: application/pdf");
			header("Content-Disposition: attachment; filename=$pdfFile");
			header("Pragma: no-cache");
			header("Expires: 0");
			readfile($pdfPath);
		}
		
		private function downloadHelper($url, $path)
		{
			$newf = fopen($path, "wb");
			fwrite($newf, $url);
			fclose($newf);
			/*
			$file = fopen ($url, "rb");
			if ($file) {
				$newf = fopen($path, "wb");

				if ($newf)
				{
					while(!feof($file)) {
					  fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
					}
					fwrite($newf, $url);
				}
			}

			if ($file) {
				fclose($file);
			}

			if ($newf) {
				fclose($newf);
			}*/
		}
	}
?>

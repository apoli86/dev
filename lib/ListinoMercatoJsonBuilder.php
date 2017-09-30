<?php
	include_once('ListinoMercato.php');
	
	class ListinoMercatoJsonBuilder
	{
		private $dayOfWeek;
		private $months;
		
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
			
			$this->months[1] = 'Gennaio';
			$this->months[2] = 'Febbraio';
			$this->months[3] = 'Marzo';
			$this->months[4] = 'Aprile';
			$this->months[5] = 'Maggio';
			$this->months[6] = 'Giugno';
			$this->months[7] = 'Luglio';
			$this->months[8] = 'Agosto';
			$this->months[9] = 'Settembre';
			$this->months[10] = 'Ottobre';
			$this->months[11] = 'Novembre';
			$this->months[12] = 'Dicembre';
		}
		
		public function createJson($listiniByYearMonth)
		{
			$yearsJson  = array();
			
			foreach($listiniByYearMonth as $year => $months)
			{
				$monthsJson = array();
				
				foreach($months as $month => $listini)
				{
					$listiniJson = array();
					
					foreach($listini as $listino)
					{
						$date = $listino->getDate();
						$code = $listino->getCode();
						
						$listinoJson = array();
						$listinoJson['Label'] = date("d", $date) . "-" . date("m", $date) . "-" . date("Y", $date) . " - " . $this->dayOfWeek[date("l", $date)];
						$listinoJson['Date']  = $date;
						$listinoJson['Code']  = $code;
						$listinoJson['Checked'] = true;
						$listinoJson['Hide'] = false;
						$listiniJson[] = $listinoJson;
					}
					
					$monthItem = array();
					$monthItem['Label'] = $this->months[$month];
					$monthItem['Month'] = $month;
					$monthItem['Listini'] = $listiniJson;
					
					$monthsJson[] = $monthItem;
				}
				
				$yearItem = array();
				$yearItem['Label']  = $year;
				$yearItem['Year']   = $year;
				$yearItem['Months'] = $monthsJson;
					
				$yearsJson[] = $yearItem;
			}
			
			return json_encode($yearsJson);
		}
		
		public function createFromJson($listiniToDownloadJson)
		{
			$listiniToDownloadJson = str_replace("\\\"", "\"", $listiniToDownloadJson);
			
			$listini = array();
			$listiniToDownloadArray = json_decode($listiniToDownloadJson, true);
			
			foreach($listiniToDownloadArray as $listino)
			{
				$listini[] = new ListinoMercato($listino['Date'], $listino['Code']);
			}
			
			return $listini;
		}
	}
?>
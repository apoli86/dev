<?php
	class ListinoMercato
	{
		private $date;
		private $code;
		
		public function __construct($date, $code)
		{
			$this->date = $date;
			$this->code = $code;
		}
		
		public function getDate()
		{
			return $this->date;
		}
		
		public function getCode()
		{
			return $this->code;
		}
		
		public function getUrl()
		{
			$date = date('m/d/Y',$this->date);
			return "https://veronamercato-api.azurewebsites.net/api/v1/Listini?ExportFileAs=pdf&fromDate=" . $date->format('c');
		}
	}
?>

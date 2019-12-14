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
			$date = date_format(date_create()->setTimestamp($this->date), 'Y-m-d\TH:i:s.000');
			return "https://veronamercato-api.azurewebsites.net/api/v1/Listini?ExportFileAs=pdf&fromDate=" . $date;
		}
	}
?>

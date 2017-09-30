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
			return "http://www.veronamercato.it/pdf/print.php?id_listino=" . $this->code;
		}
	}
?>
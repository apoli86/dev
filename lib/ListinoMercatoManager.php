<?php
	class HolidayRepository
	{
		private $db;
		private $holidayKey;
		
		public function __construct()
		{
			$this->db = "holidays.json";
			$this->holidayKey = 'holiday';
		}
		
		public function getHolidays()
		{
			$holidaysJson = json_decode(file_get_contents($this->db), true);
			
			$holidays = array();
			
			if (!isset($holidaysJson) || !is_array($holidaysJson))
				return $holidays;
			
			foreach($holidaysJson as $holiday)
			{
				if (array_key_exists($this->holidayKey, $holiday))
					$holidays[] = strtotime($holiday[$this->holidayKey]);
			}
			
			asort($holidays);
			
			return array_unique($holidays);
		}
		
		public function addHoliday($date)
		{
			if (!isset($date) || !strtotime($date))
				return false;
			
			$holidays = $this->getHolidays();
			$holidays[] = strtotime($date);
			
			$holidays = array_unique($holidays);
			asort($holidays);
			
			$holidaysToJson = array();
			
			foreach($holidays as $holiday)
			{
				$holidayJson = array();
				$holidayJson[$this->holidayKey] = date("Y", $holiday) . "-" . date("m", $holiday) . "-" . date("d", $holiday);
				
				$holidaysToJson[] = $holidayJson;
			}
			
			$json = json_encode($holidaysToJson);
			
			file_put_contents($this->db, $json);
			
			return true;
		}
	}
	
	class ListinoMercatoManager
	{
		private $listinoMercatoBase;
		private $holidays;
		
		public function __construct($holidays)
		{
			$this->listinoMercatoBase = new ListinoMercato(mktime(0,0,0,4,1,2015), 4432);
			
			$this->holidays = $holidays;
		}
		
		public function createListinoMercatoList()
		{
			$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			//$referenceDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			
			//if ($referenceDate < $this->listinoMercatoBase->getDate() || date("Y", $referenceDate) == date("Y", $this->listinoMercatoBase->getDate()))
			//	$referenceDate = $this->listinoMercatoBase->getDate();
			//$referenceDate = $this->listinoMercatoBase->getDate();
			//$year   = date("Y", $referenceDate);
			
			$result = array();
			
			$maxYear = date("Y", $today);
			
			$minDate = $this->listinoMercatoBase->getDate();
			$minYear = date("Y", $minDate);
			
			for($year = $minYear; $year <= $maxYear; $year++)
			{
				$minMonth = $year == $minYear ? date("m", $minDate) : 1;
				$maxMonth = $year < $maxYear ? 12 : date("m");
				$monthsInYear = array();
				
				for($month = $minMonth; $month <= $maxMonth; $month++)
				{
					$dates = $this->getDatesInMonth($month, $year);
					
					$listinoMercatoList = array();
					
					foreach($dates as $date)
					{
						$dayOfWeek = date("w", $date);
						
						if ($this->isHoliday($date) || $dayOfWeek == 6)
							continue;
					
						$listinoMercatoList[] = new ListinoMercato($date, $this->calculateCode($date));
					}

					$monthsInYear[intval($month)] = $listinoMercatoList;
				}
				
				krsort($monthsInYear);
				
				$result[intval($year)] = $monthsInYear;
			}
			
			krsort($result);
			
			return $result;
		}
		
		private function calculateCode($referenceDate)
		{
			if ($referenceDate < $this->listinoMercatoBase->getDate())
				return -1;
			
			$date = $this->listinoMercatoBase->getDate();
			$code = $this->listinoMercatoBase->getCode();
			
			while($date < $referenceDate)
			{	
				$nextDate = mktime(0,0,0,date("m", $date),date("d", $date) + 1,date("Y", $date));
				
				if ($this->isHoliday($date))
				{
					$date = $nextDate;
					continue;
				}	
			
				$code++;
				$date = $nextDate;
			}
			
			return $code;
		}
		
		private function isHoliday($date)
		{
			$dayOfWeek = date("w", $date);
			
			if ($dayOfWeek == 0)
				return true;
			
			foreach($this->holidays as $holiday)
			{
				if ($holiday == $date)
					return true;
			}
			
			return false;
		}
		
		private function getDatesInMonth($month, $year)
        {
			$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $num = date('t', mktime(0, 0, 0, $month, 1, $year)); //cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $datesMonth=array();
            for($i=1;$i<=$num;$i++)
            {
				$date = mktime(0,0,0,$month,$i,$year);
				
				if ($date > $today)
					return $datesMonth;
				
                $datesMonth[$i]=$date;
            }
            return $datesMonth;
        }
	}
?>

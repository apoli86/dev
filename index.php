<?php
	include_once('lib/ListinoMercato.php');
	include_once('lib/ListinoMercatoManager.php');
	include_once('lib/ListinoMercatoDownloadManager.php');
	include_once('lib/ListinoMercatoJsonBuilder.php');
	include_once('lib/ZipManager.php');
	include_once('views/HomeView.php');
	include_once('views/HolidaysView.php');
	
	setlocale(LC_TIME, 'it_IT');
	date_default_timezone_set('Europe/Rome');
	
	class ListinoMercatoController
	{
		private $listinoMercatoManager;
		private $listinoMercatoJsonBuilder;
		private $zipManager;
		private $listinoMercatoDownloadManager;
		private $holidayRepository;
		
		public function __construct()
		{
			$this->holidayRepository = new HolidayRepository();
			
			$this->listinoMercatoManager = new ListinoMercatoManager($this->holidayRepository->getHolidays());
			$this->listinoMercatoJsonBuilder = new ListinoMercatoJsonBuilder();
			$this->zipManager = new ZipManager();
			$this->listinoMercatoDownloadManager = new ListinoMercatoDownloadManager();
		}
		
		public function handleRequest()
		{
			$action = $this->getParameter('action');
				
			$availableActions = array('home', 'downloadSelected', 'downloadPdf', 'addHoliday', 'showHolidays');
			
			if (!isset($action) || empty($action) || (!in_array($action, $availableActions))) 
				$action = 'home';
				
			$this->{$action}();
		}
		
		private function downloadPdf()
		{
			$listinoToDownloadJson = $this->getParameter('listinoToDownload');
					
			if (!isset($listinoToDownloadJson) || empty($listinoToDownloadJson))
			{
				echo "Missing listinoToDownload";
				return;
			}
				
			$filename = tempnam('.', '');
			unlink($filename);

			$tmpdir = $filename;
			mkdir($filename, 0777, true);

			$listini = $this->listinoMercatoJsonBuilder->createFromJson($listinoToDownloadJson);
			
			if (empty($listini))
			{
				echo "Missing listinoToDownload";
				return;
			}
			
			if (count($listini) > 1)
			{
				echo "Only one listino expected";
				return;
			}
			
			$listino = $listini[0];
			$this->listinoMercatoDownloadManager->downloadAndSend($tmpdir, $listino);
			
			array_map('unlink', glob("$tmpdir/*"));
			rmdir($tmpdir);
		}
		
		private function downloadSelected()
		{
			$listiniToDownloadJson = $this->getParameter('listiniToDownload');
					
			if (!isset($listiniToDownloadJson) || empty($listiniToDownloadJson))
			{
				echo "Missing listiniToDownload";
				return;
			}
				
			$filename = tempnam('.', '');
			unlink($filename);

			$tmpdir = $filename;
			mkdir($filename, 0777, true);

			$zipName = "listini_" . date("d") . "-" . date("m") . "-" . date("Y") . "_" . date("H") . "-" . date("i") . ".zip";
			
			$listini = $this->listinoMercatoJsonBuilder->createFromJson($listiniToDownloadJson);
			foreach($listini as $listino)
			{
				$this->listinoMercatoDownloadManager->download($tmpdir, $listino);
			}
			
			$this->zipManager->zipAndSend($tmpdir, $zipName);
			
			unlink($zipName);
			array_map('unlink', glob("$tmpdir/*"));
			rmdir($tmpdir);
		}
		
		private function home()
		{
			$home = new HomeView();
			
			$listiniByYearMonth     = $this->listinoMercatoManager->createListinoMercatoList();
			$listiniByYearMonthJson = $this->listinoMercatoJsonBuilder->createJson($listiniByYearMonth);
			
			$home->render($listiniByYearMonthJson);
		}
		
		private function addHoliday()
		{
			$holiday = $this->getParameter('holiday');
			
			if (!$this->holidayRepository->addHoliday($holiday))
				return;
			
			$this->showHolidays();
		}
		
		private function showHolidays()
		{
			$holidays = $this->holidayRepository->getHolidays();
			
			$holidaysView = new HolidaysView();
			$holidaysView->render($holidays);
		}
		
		private function getParameter($parameter)
		{
			$parameterValue = array_key_exists($parameter, $_GET) ? $_GET[$parameter] : null;
			$parameterValue = array_key_exists($parameter, $_POST) ? $_POST[$parameter] : $parameterValue;
			
			return $parameterValue;
		}
	}
	
	$controller = new ListinoMercatoController();
	$controller->handleRequest();
?>
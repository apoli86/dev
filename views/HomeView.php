<?php
	class HomeView
	{
		private $daysOfWeek;
		private $daysOfWeekChecked;
		
		public function __construct()
		{	
			$this->daysOfWeek        = array('Lun', 'Mar', 'Mer', 'Gio', 'Ven');
			$this->daysOfWeekChecked = array('Lun', 'Gio', 'Ven');
		}
		
		public function render($listini)
		{
			?>
			<!DOCTYPE html>
			<html data-ng-app="ListinoMercatoApp">
				<head>
					<link rel="stylesheet" type="text/css" href="css/template.css">
					<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
					<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>
					<script>
						function DatasourceBuilder()
						{
							var listiniByYearMonths = JSON.parse('<?php echo $listini ?>');
							var daysOfWeek = JSON.parse('<?php echo $this->getDaysOfWeek() ?>');
							
							return {
								ListiniByYearMonths: listiniByYearMonths,
								DaysOfWeek: daysOfWeek
							};
						}
						
						window.Datasource = DatasourceBuilder();
					</script>
					<script src="js/Home.js"></script>
				</head>
				<body data-ng-controller="ListinoMercatoController" class="container">
					<h3 style="margin-left: 20px;">Listini Mercato </h3>
					
					<div>
						<table>
							<tr>
								<td><input type="checkbox" data-ng-model="checkAllListini" ng-change="onCheckAllListiniChange()"/></td>
								<td>
									<div style="float: left; width: 50%;">
									<select data-ng-model="currentYear" data-ng-init="currentYear = listiniByYearMonths[0]" data-ng-options="year.Label for year in listiniByYearMonths | orderBy: '-Year'" style="width: 100%;"></select>
									</div>
									<div style="float: right; width: 50%;">
									<select data-ng-model="currentMonth" data-ng-init="currentMonth = currentYear.Months[0]" data-ng-options="month.Label for month in currentYear.Months"  style="width: 100%;"></select>
									</div>
									<div style="height: 10px; clear: both;"></div>
									<!--
									<div style="visibility: hidden;" data-ng-repeat="dayOfWeek in daysOfWeek"">
										<input type="checkbox"	style="visibility: hidden;" data-ng-model="dayOfWeek.Checked"/><span>{{ dayOfWeek.Day }}</span>
									</div>
									-->
									<div style="float: left;" data-ng-repeat="dayOfWeek in daysOfWeek"">
										<input type="checkbox"	data-ng-model="dayOfWeek.Checked"></input><span>{{ dayOfWeek.Day }}&nbsp;&nbsp;</span>
									</div>
								</td>
								<td><a ng-click="downloadAll()" style="cursor: pointer; font-weight: bold;"><img src="images/zip.png" width="24px"> scarica selezionati</td>
							</tr>
							
							<tr data-ng-repeat="listino in currentMonth.Listini | filter: filterByDay | orderBy:'-Label'">
								<td><input type="checkbox" data-ng-model="listino.Checked"></input></td>
								<td>{{ listino.Label }}</td>
								<td><a ng-click="downloadPdf(listino)" style="cursor: pointer;"><img src="images/pdf.png" width="24px"></a></td>
							</tr>
						</table>
					</div>
				</body>
			</html>
			<?php
		}
		
		private function getDaysOfWeek()
		{
			$daysOfWeekJson = array_map(array($this, 'getDayOfWeek'), $this->daysOfWeek);
			
			return json_encode($daysOfWeekJson);
		}
		
		private function getDayOfWeek($day)
		{
			$dayOfWeek = array();
				
			$dayOfWeek['Day']     = $day;
			$dayOfWeek['Checked'] = in_array($day, $this->daysOfWeekChecked);
				
			return $dayOfWeek;
		}
	}
?>

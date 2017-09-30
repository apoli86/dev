(function() {
	'use strict';

	var listinoMercatoApp = angular.module('ListinoMercatoApp', []); 
	
	listinoMercatoApp.controller('ListinoMercatoController', ['$scope', function ($scope) {
		var listiniByYearMonthsDataSource = window.Datasource.ListiniByYearMonths;
		$scope.listiniByYearMonths = listiniByYearMonthsDataSource;
		//$scope.currentYear = listiniByYearMonthsDataSource[listiniByYearMonthsDataSource.length - 1];
		//$scope.currentMonths = $scope.currentYear.Months;
		//$scope.currentMonth = $scope.currentMonths[$scope.currentMonths.length - 1];
		$scope.checkAllListini = true;
		
		$scope.daysOfWeek = window.Datasource.DaysOfWeek;
		
		$scope.onMonthChange = function() {
			$scope.currentListini = $scope.currentMonth.Listini;
		};
		
		$scope.onCheckAllListiniChange = function()
		{
			jQuery.each($scope.currentMonth.Listini, function(i, currentListino){
				currentListino.Checked = $scope.checkAllListini;
			});
		}
		
		$scope.sendRequest = function(params)
		{
			var form = $('<form>').attr('method', 'POST');
			
			jQuery.each(params, function(name, value){
				var param = $("<input>").attr("type", "hidden").attr("name", name).val(value);
				form.append(param);
			});
			
			form.appendTo($('body')).submit();
			form.remove();
		}
		
		$scope.downloadAll = function() {
			var currentListiniFiltered = jQuery.grep($scope.currentMonth.Listini, function(currentListino){
				return currentListino.Checked && !currentListino.Hide;
			});
			
			var listiniToDownload = jQuery.map(currentListiniFiltered, function(currentListino){
				return {
					Date: currentListino.Date,
					Code: currentListino.Code
				};
			});
			
			if (listiniToDownload.length == 0)
				return;
			
			var params = {
				action: 'downloadSelected',
				listiniToDownload: JSON.stringify(listiniToDownload)
			};
			
			$scope.sendRequest(params);
		}
		
		$scope.downloadPdf = function(listino) {
			var currentListiniFiltered = new Array();
			currentListiniFiltered.push(listino);
			
			var listiniToDownload = jQuery.map(currentListiniFiltered, function(currentListino){
				return {
					Date: currentListino.Date,
					Code: currentListino.Code
				};
			});
			
			if (listiniToDownload.length == 0)
				return;
			
			var params = {
				action: 'downloadPdf',
				listinoToDownload: JSON.stringify(listiniToDownload)
			};
			
			$scope.sendRequest(params);
		}
		
		$scope.filterByDay = function(listino)
		{
			var showListino = false;
			
			jQuery.each($scope.daysOfWeek, function(i, day){
				if (listino.Label.indexOf(day.Day) != -1)
				{
					showListino  = day.Checked;
					listino.Hide = !showListino;
				}
			});
			
			return showListino;
		}
	}]);
}());
<?php   
	class HolidaysView 
	{
		public function render($holidays)
		{
?>
			<!DOCTYPE html>
			<html data-ng-app="ListinoMercatoApp">
				<head>
					<link rel="stylesheet" type="text/css" href="css/template.css">
				</head>
				<body>
					<table>
						<tr><td>Festivit&agrave;</td></tr>
			<?php
			foreach($holidays as $holiday)
			{
					echo "<tr><td>" . date("d", $holiday) . "-" . date("m", $holiday) . "-" . date("Y", $holiday) . "</td></tr>";
			}
			?>
					</table>
				</body>
			</html>
<?php
		}
	}
?>
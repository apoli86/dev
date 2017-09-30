<?php
	class ZipManager
	{
		public function zipAndSend($folder, $zipName)
		{
			$zip = new ZipArchive();
	
			$zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
			if ($handle = opendir($folder)) {

				while (false !== ($entry = readdir($handle))) {

					if ($entry != "." && $entry != "..") {
						$ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
						
						if ($ext != 'pdf')
							continue;
						
						$zip->addFile(realpath($folder) . '/' . $entry, basename($entry));
					}
				}

				closedir($handle);
			}
			$zip->close();
			
			header("Content-type: application/zip");
			header("Content-Disposition: attachment; filename=$zipName");
			header("Pragma: no-cache");
			header("Expires: 0");
			readfile($zipName);
		}
	}
	
?>
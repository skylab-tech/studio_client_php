<?php
use Jcupitt\Vips;

// function getResolutionFromExif(file) 
// 	{

// 	}

	function attemptImageConversion($file, $conversion_type, $strip_metadata = false)
	// TODO - FINISH
		{
			$convertedImage = null;
			switch ($conversion_type) {
				case "webp":
					echo 'CONVERTING';
					$convertedImage = $file->writeToBuffer('.webp', ['Q' => 97]);
					break;
			}

			return $convertedImage;
		}

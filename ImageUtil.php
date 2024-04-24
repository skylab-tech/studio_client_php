<?php
use Jcupitt\Vips;

function validateFile($photoPath) {
	try {
			$imageInfo = getimagesize($photoPath);
			$width = $imageInfo[0];
			$height = $imageInfo[1];
			$size = filesize($photoPath);

			return (
				$size > 27 * 1024 * 1024 || // If size is greater than 27 MB
				$width > 6400 ||            // If width is greater than 6400 pixels
				$height > 6400 ||           // If height is greater than 6400 pixels
				$width * $height > 27000000 // If total pixels greater than 27 million
			);
	} catch (Exception $err) {
			return true;
	}
}

	function attemptImageConversion($file, $conversion_type, $strip_metadata = false)
		{
			$convertedImage = null;
			switch ($conversion_type) {
				case "webp":
					$convertedImage = $file->writeToBuffer('.webp', ['Q' => 97]);
					break;
				case "jpg":
					$convertedImage = $file->writeToBuffer('.jpg', ['Q' => 97]);
					break;
				case "png":
					if ($strip_metadata) {
						$fields = $file->getFields();

						foreach( $fields as $key => $value ) {
							try {
								echo "removing key..." . $value;
								$file->remove($value);
							}
							catch ( \Exception $e ) {
								echo ''. $e->getMessage() .'';
							}
						}

						echo "setting converted image";
						$convertedImage = $file->writeToBuffer('.png', ['Q' => 97]);
					}
          else {
						$convertedImage = $file->writeToBuffer('.png', ['Q' => 97]);
					}
					break;
			}

			return $convertedImage;
		}

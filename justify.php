<?php
/**
 * Justify of text
 *
 * The program takes as input a file that contains text in random order,
 * process the text and writes the result into an output file.
 *
 * Attention! The program is designed to run on the console.
 *
 * Running:
 * Open a console and run the PHP script (justify.php) located in this directory. After that
 * you should enter the input and output file.
 * Example for Windows: C:\\"Program Files"\\PHP\\php.exe D://path_to_file/justify.php
 *
 * Author: Bohdan Vorona, Jan 2014
 * http://bohdanvorona.name/
 */

/**
 * $N - width of the output text
 * Init () - main function for run the app
 */
$N = 80;
Init($N);

/**
 * Check file is exists
 * @param $fileName - name of file
 * @return bool - existed or no
 */
function CheckExistsFile($fileName) {
	if (file_exists(realpath(dirname(__FILE__)).'/'.$fileName)) {
		return TRUE;
	}
	return FALSE;
}

/**
 * Check file is not empty
 * @param $fileName - name of file
 * @return bool - empty or no
 */
function CheckNotEmptyFile($fileName) {
	if (file_get_contents(realpath(dirname(__FILE__)).'/'.$fileName)) {
		return TRUE;
	}
	return FALSE;
}

/**
 * Get name of incoming file from user
 * @return string - filename
 */
function GetInputFile() {
	print 'Enter name of incoming file: '.PHP_EOL;
	$inputFile = trim(fgets(STDIN));
	return $inputFile;
}

/**
 * Get name of outgoing file from user
 * @return string - filename
 */
function GetOutputFile() {
	print 'Enter name of outgoing file: '.PHP_EOL;
	$outputFile = trim(fgets(STDIN));
	return $outputFile;
}

/**
 * App init
 * @param $N - width of the output text
 */
function Init($N) {
	// Get names of files
	$inputFile = GetInputFile();
	$outputFile = GetOutputFile();
	
	// Check files
	if (CheckExistsFile($inputFile) && CheckExistsFile($outputFile)) {
		if (CheckNotEmptyFile($inputFile)) {

            // Processing
            $outputData = Processing(file_get_contents(realpath(dirname(__FILE__)).'/'.$inputFile), $N);

            // Output result
            $f = @fopen(realpath(dirname(__FILE__)).'/'.$outputFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f); // clear file if not empty
            }
            if (file_put_contents(realpath(dirname(__FILE__)).'/'.$outputFile, $outputData, FILE_APPEND)) {
                print 'Ok. Check outgoing file.';
            }

		}else{
			print 'Sorry, but incoming file whose name you entered is empty. File must be not empty. Please try again.'.PHP_EOL;
			Init($N);
		}
		
	}else{
		print 'Sorry, but we did not find a file (or files) whose name you entered. File must be located in the same directory as the script. Please try again.'.PHP_EOL;
		Init($N);
	}
}

/**
 * Justify current string
 * @param $strIn - current string for justify
 * @param $desiredLength - desired length of current string
 * @return string - processed current string
 */
function Justify ($strIn, $desiredLength) {
    // Splitting a string
    $strExplode = explode(' ', $strIn);

    // Count len and reverse words in $strExplode
    $strLen = strlen($strIn);
    $arrayCount = count($strExplode);
    $arrayReverse = array_reverse($strExplode);
    $lenCount = $strLen;

    // Add new spaces from end of the line
    for ($i = 1; $i <= $arrayCount; $i ++) {
        if (!empty($arrayReverse[$i])) {
            $arrayReverse[$i-1] = ' '.$arrayReverse[$i-1];
            $lenCount++;
        }
        if ($strLen + $i === $desiredLength) {
            break;
        }
    }

    // To repeat, if not enough spaces
    if ($lenCount < $desiredLength) {
        for ($i = 1; $i <= $arrayCount; $i ++) {
            if (!empty($arrayReverse[$i])) {
                $arrayReverse[$i-1] = ' '.$arrayReverse[$i-1];
            }
            if ($strLen + $i === $desiredLength) {
                break;
            }
        }
    }

    // Return processed current string with with added space
    return implode(" ", array_reverse($arrayReverse));
}

/**
 * Main function for processing before justify
 * @param $text - incoming text
 * @param int $n - desired length of strings in current text
 * @return string - text is divided into sections
 */
function Processing ($text, $n = 80) {
	$paragraphs = array();
	$paragraphsKeys = array();
	
	// Get lines and define paragraphs
	$array = explode("\r\n", $text);
	$count = count($array);
	for ($i = 0; $i < $count; $i ++) {
		if (strlen($array[$i]) < $n/2 || ctype_upper($array[$i][0])) {		
			$paragraphs[$i] = "   ".trim($array[$i]);
			$paragraphsKeys[] = $i;
		} else {
            $array[$i] = trim($array[$i]);
        }
	}
	
	// Get paragraphs, split into equal parts and apply justify
	foreach ($paragraphs as $key=>$val) {		
			if (in_array($key, $paragraphsKeys)) {
				for ($i = $key+1; $i < $paragraphsKeys[array_search($key, $paragraphsKeys)+1]; $i ++) { 
					$paragraphs[$key] .= $array[$i];
				}			
			}	
		// split into equal parts
		$paragraphs[$key] = wordwrap($paragraphs[$key], $n, "\r\n");
		
		// get divided lines
		$lines = explode("\r\n", $paragraphs[$key]);
		foreach ($lines as $k=>$line) {
			// justify
			if (strlen($line) < $n && strlen($line) >= $n/2) {
                $lines[$k] = Justify($lines[$k], $n);
			}
		}
        $paragraphs[$key] = implode("\r\n", $lines);
	}

	// Return text than divided into sections
	return implode("\r\n", $paragraphs);
}

?>

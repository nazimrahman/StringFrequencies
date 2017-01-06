<?php
class StringFrequency
{
	// Member Variables
	var $sSourceAddress;	// string containing address of source directory or file
	var $bResourceIsDir;	// is the resource a directory
	var $aLines;			// array containing lines of text gathered from source texts
	var $aSentences;		// array of sentences
	var $aWordFrequencies;	// array of word frequencies
	var $sExclusionList;	// exclusion list address
	var $sFilter;			// set a filter

	function __construct() {
		// initialize members to default values
		$this->sSourceAddress = '/tmp';
		$this->bResourceIsDir = false;
		$this->aLines = array();
		$this->aSentences = array();
		$this->aWordFrequencies = array();
		$this->sExclusionList = 'exclusionlists/el-1.txt';
		$this->sFilter = '';
	}

	public function setSourceAddress($address) {
		$this->sSourceAddress = $address;
		if (is_dir($address)) { 
			$this->bResourceIsDir = TRUE; 
		}
	}

	public function getSourceAddress() {
		return $this->sSourceAddress;
	}

	public function setExclusionList($address) {
		$this->sExclusionList = $address;
	}

	public function getExclusionList() {
		return $this->sExclusionList;
	}

	public function setFilter($address) {
		$this->sFilter = $address;
	}

	public function getFilter() {
		return $this->sFilter;
	}	

	public function getWordFrequencies() {
		if (count($this->aWordFrequencies) < 2) {
			$this->buildWordFrequencies();
		}
		return $this->aWordFrequencies;
	}

	private function buildWordFrequencies() {
		// preprocess steps
		$this->transformToLines();
		$this->cleanText();

		// initialize local variables
		$sFulltext = '';
		$aW = array();
		$aFilter = array();
		$aTmp = array();
		
		// implode all sentences into one text string
		$sFulltext = strtolower(implode(" ", $this->aSentences));

		// if a filter is defined
		if (strlen($this->sFilter) > 1) {
			// get frequencies only for words specified in the filter
			$aFilter = file($this->sFilter);
			$aW = array_count_values(str_word_count($sFulltext, 1));

			foreach ($aFilter as $filter) {
				$filter = trim($filter);
				if (isset($aW[$filter])) {
					$aTmp[$filter] = $aW[$filter];
				}
			}
			$aW = $aTmp;
		} else {
			// get frequencies for all words
			$aW = array_count_values(str_word_count($sFulltext, 1));
		}
		// sort array by frequency in descending order
		arsort($aW);
		$this->aWordFrequencies = $aW;

		// release memory
		unset($sFulltext);
		unset($aW);
		unset($aFilter);
		unset($aTmp);
	}

	private function cleanText() {
		// initialize variables
		$aSentences1 = array();
		$aSentences2 = array();

		// loop through all lines
    	foreach ($this->aLines as $line) {
      		// remove \n
      		$line = trim($line);

      		// replace non ASCII characters with spaces
      		$line = preg_replace('/[[:^print:]\(\)\&\/@:*]/', ' ', $line);

      		// other terms to remove
      		$aExclude = file($this->sExclusionList);
      		foreach ($aExclude as $ex) {
      			$pattern = '/ ' . trim($ex) . ' /';
      			$line = preg_replace($pattern, ' ', $line);
      		}

			// replace multiple spaces with one space
      		$line = preg_replace('/\s+/', ' ',$line);

      		/* At this point lines are broken into array on \n character
       		 * We need to split on . and ?
       		 * So each line need to be possibly split and 
       		 * resulting element added to another array
       		 */
      
      		// split on . and ?
      		$aSentences1 = preg_split("/[.?]/", $line);

      		// merge split elements with main array
      		$aSentences2 = array_merge($aSentences2, $aSentences1);
    	}
    	// post: $aSentences2 contains all sentences

    	// remove nonsense sentences
    	foreach ($aSentences2 as $sentence) {
    		if (preg_match("/[A-Za-z0-9]/", $sentence)) {
    			// save the sentence
    			$this->aSentences[] = $sentence;
    		}
    	}
  		// release memory
  		unset($aSentences1);
  		unset($aSentences2);
	}

	private function transformToLines() {
		// initialize local variables
		$aTmpLines = array();	// temp array to store lines

		if ($this->bResourceIsDir === TRUE) {
			// get text from directory
			
			// fetch all files from directory except those in $aExcept
			$aExcept = array('.', '..');
  			$aFiles = array_diff(scandir($this->sSourceAddress), $aExcept);
  			// post: $aFiles contains a list of all filenames

  			// open files and store lines
  			foreach ($aFiles as $file) {
    			// extract file contents line by line in an array 
    			$aTmpLines = file($this->sSourceAddress.'/'.$file);
    			$this->aLines = array_merge($this->aLines, $aTmpLines);
    		}
    			
		} else {
			// get text from file, open file and store its contents as lines
			$this->aLines = file($this->sSourceAddress);
		}
		
		// release memory
    	unset($aTmpLines);
    	unset($aFiles);

    	// post: all lines from all files are stored in $this->aLines
	}
}
?>
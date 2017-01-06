# StringFrequencies
This is a PHP class that breaks text into words and multiword strings. It also provides frequencies of the occurrence of each string. The class provide many additional features:

- cleaning text
- breaking text into sentences
- exclusion lists are supported
- filters are supported

###How to use
Following is a sample file to run the code

    include_once('StringFrequency.php');
    
    $sf = new StringFrequency;
    
    // define source of your text. Could be directory or file
    $sf->setSourceAddress('datasource');
    
    // define exclusion list, required
    $sf->setExclusionList('exclusionlists/el-2.txt');
    
    // define filters (optional)
    $sf->setFilter('filters/f-1.txt');
    
    // get word frequencies
    print_r($sf->getWordFrequencies());
    
The code can be run from command line or web.
    
###Exclusion Lists
Exclusion lists are simple text files wish a list of strings that would be ignored by the class such as

    e.g.
    i.e.
    and
    or
    
###Filters
Filters are optional. If you provide a filter, the class will only return frequencies for the string defined in the filter. Example:

    java
    python
    data
    
###Results
The class return an associative array.

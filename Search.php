<?php

class Search
{
    /**
     * @var array
     */
    private $dictionary = array();

    /**
     * @var string
     */
    private $dict_file = './data.json';

    /**
     * Search constructor.
     */
    function __construct()
    {
        $json_data = file_get_contents($this->dict_file);

        if (false === $json_data)
        {
            die('File data.json not found');
        }
        else
        {
            $this->dictionary = json_decode($json_data);
        }
    }

    /**
     *
     * @param string $query
     * @return string
     */
    function generate($query = null)
    {
        if (null != $query && $query != '')  // check for null user's input
        {
            $result = '';
            $words = explode(' ', $query);  // convert query string to array

            foreach ($words as $key => $word) // iterate through set of words from user's query
            {
                if (mb_strlen($word) > 1) // exclude short words with length = 1
                {
                    if (isset($words[$key+1]))
                    {
                        $hasNext = true;
                    }
                    else
                    {
                        $hasNext = false;
                    }

                    $nextWord = $words[$key+1]; // check for collocations
                    $occurrences = $this->searchInDictionary($word, $nextWord);
                    if (strlen($occurrences) > 1)   // if we find occurrences in dictionary
                    {
                        $result .= $occurrences;
                    }
                    else
                    {
                        $result .= $word;
                    }

                    if ($hasNext)
                    {
                        $result .= ' & ';
                    }

                }
            }

            return $result;
        }
        else {
            return 'Zero query';
        }
    }

    /**
     * @param string $word
     * @param bool $nextWord
     * @return string $result
     */
    public function searchInDictionary($word, $nextWord)
    {
        $result = '';

        if (is_array($this->dictionary))
        {
            foreach ($this->dictionary as $key => $synonyms)
            {
                foreach ($synonyms as $synonym)
                {
                    if ( mb_strtolower($synonym) === mb_strtolower($word))  // check for single word
                    {
                        $result .= '(' . implode('|', $synonyms) . ')';
                    }
                    else if (mb_strtolower($word . ' ' . $nextWord) === mb_strtolower($synonym))  // check for collocation
                    {
                        $result .= '(' . implode('|', $synonyms) . ')';  // TODO: fix missing '&'
                    }
                }
            }
        }

        return $result;
    }

}
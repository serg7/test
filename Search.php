<?php

class Search
{
    /**
     * @var array
     */
    private $dictionary = array();

    /**
     * Search constructor.
     */
    function __construct()
    {
        $json_data = file_get_contents('./data.json');
        $this->dictionary = json_decode($json_data);
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
            $result = '(';
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

                    $result .= $this->compareWord($word, $hasNext);
                }
            }

            $result .= ')';

            return $result;
        }
        else {
            return 'Zero query';
        }
    }

    /**
     * @param string $word
     * @param bool $hasNext
     * @return string $result
     */
    function compareWord($word, $hasNext)
    {
        $result = '';

        foreach ($this->dictionary as $key => $synonyms)
        {
            foreach ($synonyms as $synonym)
            {
                if (mb_strtolower($synonym) == 'баз данных')
                {
                    echo mb_strtolower($synonym) . ' - ' . mb_strtolower($word);
                }

                if (mb_strtolower($synonym) == mb_strtolower($word))
                {
                    $result .= implode('|', $synonyms);
                    if ($hasNext)
                    {
                        $result .= ') & (';
                    }
                }
            }
        }

        return $result;
    }

}
<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_URI extends CI_URI
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSegmentIndex($segmentValue)
    {
        $keyArray = array_keys($this->segment_array(), $segmentValue);
        if (empty ($keyArray[0])) {
            return -1;
        }

        return $keyArray[0];
    }

    public function assoc_to_uri($data)
    {
        return '/' . parent::assoc_to_uri($data);
    }
}
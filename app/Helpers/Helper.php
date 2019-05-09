<?php

namespace App\Helpers;

class Helper
{

	public static function shout(string $string)
    {
        return strtoupper($string);
    }

	/**
	*	get podcast status
	*/
	public static function podcasts_status($status='') {
		$status_array = array(
			'1' => 'published',
			'2' => 'review'
        ); 
        if($status){
        	return array_search($status, $status_array);
        }else{
			return $status_array;
		}
	}

	/**
	*	print the array using it
	*/
	public static function p($data){
        echo '<pre>'; print_r($data); echo '</pre>';
    }

    /**
	*	find and replace value of array using the key
	*/
	public static function find_and_replace($data){
        if(!empty($data) && count($data)){
        	if(isset($data[0]) && !empty($data[0])){
	        	foreach ($data as $key => $value) {
	        		if(isset($data[$key]['status']) && !empty($data[$key]['status'])){
	        			$data[$key]['status'] = ($data[$key]['status']==1) ? "Published" : "Review";
	        		}
	        		if(isset($data[$key]['is_deleted']) && !empty($data[$key]['is_deleted'])){
	        			$data[$key]['is_deleted'] = ($data[$key]['is_deleted']==1) ? "Not deleted" : "Deleted";
	        		}
	        	}
        	}else{
        		if(isset($data['status']) && !empty($data['status'])){
        			$data['status'] = ($data['status']==1) ? "Published" : "Review";
        		}
        		if(isset($data['is_deleted']) && !empty($data['is_deleted'])){
        			$data['is_deleted'] = ($data['is_deleted']==1) ? "Not deleted" : "Deleted";
        		}
        	}
        }
        return $data;
    }
}

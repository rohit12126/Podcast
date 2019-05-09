<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Podcasts;
use App\Podcastcomments;
use Validator;
use Helper;


class PodcastsController extends BaseController
{
    /**
     * Display a listing of the podcasts.
     *
     */
    public function index(Request $request)
    {
        $page = 0;
        if(!empty($request->page)){
            if($request->page!=0 && $request->page!=1){
                $page = ($request->page - 1) * 12;
            }
        }
        $podcasts = Podcasts::where('is_deleted', 1)->offset($page)->limit(12)->get();
        $podcasts = Helper::find_and_replace($podcasts);
        $message  = (!empty($podcasts) && count($podcasts)) ? 'podcast retrieved successfully.' : 'No podcast available.';
        return $this->sendResponse($podcasts, $message);
    }


    /**
     * Store a newly created podcast.
     *
     */
    public function store(Request $request)
    {        
        $requestData = $request->all();
        if($requestData){
            $input = $requestData;
        }else{
            // Takes raw data from the request
            $json = file_get_contents('php://input');

            // Converts it into a PHP object
            $data = (array) json_decode($json);
            $input = $data;
        }

        $validator = Validator::make($input, [
            'name' => 'required|unique:podcasts|min:4',
            'description' => 'required|max:1000',
            'marketing_url' => array(
                'required',
                'regex:/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/'
            ),
            'feed_url' => array(
                'required',
                'regex:/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/'
            )
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Errors.', $validator->errors());       
        }

        if(isset($input['image']) && !empty($input['image'])){
            if ($this->check_base64_image($input['image'])) {
                $jpg_url = "user-".time().".jpg";
                $path = public_path() . "/images/" . $jpg_url;
                $img = $input['image'];
                $img = substr($img, strpos($img, ",")+1);
                $data = base64_decode($img);
                $success = file_put_contents($path, $data);
                if($success){
                    $input['image'] = $path;
                }
            } else {
                return $this->sendError('Error', 'You have entered wrong image format.'); 
            }
        }

        $podcast = Podcasts::create($input);
        return $this->sendResponse($podcast->toArray(), 'Podcast has been created successfully.');
    }


    /**
     * Display the specified podcast.
     *
     */
    public function show(Request $request, $id)
    {
        $podcastcommentsStatus = false;
        $podcastcomments = array();

        if(is_numeric($id)){
            $podcast = Podcasts::find($id);
            if (is_null($podcast)) {
                return $this->sendError('Podcast not found.');
            }
        }else{
            if($id=='published' || $id=='review'){
                $status   = Helper::podcasts_status($id);
                if($status){
                    $podcast = Podcasts::where('status', $status)->get();
                }else{
                    return $this->sendError('Podcast not found.');
                }
            }else{
                return $this->sendError('Podcast not found.');
            }
        }

        if(!empty($request->comments)){
            if($request->comments==1){
                $podcastcommentsStatus = true;
                $podcastcomments = Podcastcomments::where('podcast_id', $id)->where('is_deleted', 1)->get();
                if(!empty($podcastcomments) && count($podcastcomments)){
                    $podcastcomments = $podcastcomments->toArray();
                }
            }
        }

        $podcast = Helper::find_and_replace($podcast->toArray());
        $message  = (!empty($podcast) && count($podcast)) ? 'podcast retrieved successfully.' : 'No podcast available.';
        return $this->sendResponse($podcast, $message, $podcastcommentsStatus, $podcastcomments);
    }


    /**
     * Update the specified podcast.
     *
     */
    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        if($requestData){
            $input = $requestData;
        }else{
            // Takes raw data from the request
            $json = file_get_contents('php://input');

            // Converts it into a PHP object
            $data = (array) json_decode($json);
            $input = $data;
        }

        $url_pattern = '/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';
        $validator = Validator::make($input, [
            'name' => 'required|min:4',
            'description' => 'required|max:1000',
            'marketing_url' => array(
                'required',
                'regex:/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/'
            ),
            'feed_url' => array(
                'required',
                'regex:/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/'
            )
        ]);

        $podcast = Podcasts::find($id);
        if($validator->fails()){
            return $this->sendError('Validation Errors.', $validator->errors());       
        }else{
            $results = Podcasts::where('id', '!=' , $podcast->id)->where('name', $input['name'])->get();
            if(!empty($results) && count($results)){
                return $this->sendError('Validation Error.', 'Please enter a unique name'); 
            }
        }

        if(isset($input['image']) && !empty($input['image'])){
            if ($this->check_base64_image($input['image'])) {
                $jpg_url = "user-".time().".jpg";
                $path = public_path() . "/images/" . $jpg_url;
                $img = $input['image'];
                $img = substr($img, strpos($img, ",")+1);
                $data = base64_decode($img);
                $success = file_put_contents($path, $data);
                if($success){
                    $podcast->image = $path;
                }
            } else {
                return $this->sendError('Error', 'You have entered wrong image format.'); 
            }
        }

        $podcast->name = $input['name'];
        $podcast->description = $input['description'];
        $podcast->marketing_url = $input['marketing_url'];
        $podcast->feed_url = $input['feed_url'];
        $podcast->save();

        return $this->sendResponse($podcast->toArray(), 'Podcast has been updated successfully.');
    }


    /**
     * Approvel the specified podcast.
     *
     */
    public function approvel($id)
    {
        $podcast = Podcasts::find($id);
        if (is_null($podcast)) {
            return $this->sendError('Podcast not found.');
        }else{
            if($podcast->status==1){
                return $this->sendError('This podcast is already approved');
            }
        }

        $podcast->status = 1;
        $podcast->save();
        return $this->sendResponse($podcast->toArray(), 'Podcast has been approved successfully.');
    }

    /**
     * Remove the specified podcast.
     *
     */
    public function destroy($id)
    {
        $podcast = Podcasts::find($id);
        if (is_null($podcast)) {
            return $this->sendError('Podcast not found.');
        }

        $podcast->is_deleted = 0;
        $podcast->save();
        return $this->sendResponse($podcast->toArray(), 'Podcast has been deleted successfully.');
    }

    /**
     * Add Comment for the particular podcast
     *
     */
    public function add_comment(Request $request)
    {
        $requestData = $request->all();
        if($requestData){
            $input = $requestData;
        }else{
            // Takes raw data from the request
            $json = file_get_contents('php://input');

            // Converts it into a PHP object
            $data = (array) json_decode($json);
            $input = $data;
        }

        $validator = Validator::make($input, [
            'author_name' => 'required',
            'author_email' => 'required|email',
            'comment' => 'required|max:1000',
            'podcast_id' => 'required',
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Errors.', $validator->errors());       
        }else{
            $Podcastcomments = Podcastcomments::where('podcast_id', $input['podcast_id'])->get();
            if(empty($Podcastcomments) || count($Podcastcomments)<=0){
                return $this->sendError('Validation Error.', 'Sorry! This podcast id is not available in our database.');
            }else{
                $matchThese = [
                    'author_name' => $input['author_name'],
                    'author_email' => $input['author_email'],
                    'comment' => $input['comment']
                ];

                $results = Podcastcomments::where($matchThese)->get();
                if(!empty($results) && count($results) >=1){
                    return $this->sendError('Validation Error.', 'Please enter a unique name, email address and comment.'); 
                }else{
                    $PodcastComments = Podcastcomments::create($input);
                    return $this->sendResponse($PodcastComments->toArray(), 'Podcast comment has been added successfully.');
                }
            }
        }
    }

    /**
     * flag comment of podcast
     *
     */
    public function flag_comment($id)
    {
        $PodcastComment = Podcastcomments::find($id);
        if (is_null($PodcastComment)) {
            return $this->sendError('No Podcast comment found.');
        }else{
            if($PodcastComment->is_deleted==0){
                return $this->sendError('This podcast comment is already deleted');
            }
        }

        $PodcastComment->is_deleted = 0;
        $PodcastComment->save();

        return $this->sendResponse($PodcastComment->toArray(), 'Podcast comment has been deleted successfully.');
    }

    /**
     * check the image is valid or not.
     *
     */
    private function check_base64_image($base64)
    {
        $res  = false;
        if ((strpos($base64, ';') !== false) && (strpos($base64, ':') !== false)) {
            $pos  = strpos($base64, ';');
            $type = explode(':', substr($base64, 0, $pos))[1];
            if(!empty($type)){
                $imageTypeArr = explode("/",$type);
                if(isset($imageTypeArr[0]) && !empty($imageTypeArr)){
                    if($imageTypeArr[0]=='image'){
                        $res = true;
                    }
                }
            }
        }
        return $res;
    }

}
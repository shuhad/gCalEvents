<?php
namespace App\Repositories;
use App\Interfaces\GClientRepositoryInterface;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Carbon\Carbon;

Class GClientRepository implements GClientRepositoryInterface {
    protected $client;
    public $calendarId = 'primary';

    public function __construct () {
        $this->client = $this->setGoogleClient();
    }

    /*
    **Set http client
    */

    public function httpClient (){
        $httpClinet = new \GuzzleHttp\Client(['curl' => [CURLOPT_SSL_VERIFYPEER => false]]);
        return $httpClinet;

    }

    /*
    **Set google client
    */

    public function setGoogleClient () {
        $client = new Google_Client();
        $client->setAuthConfig('client_secret.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);

        //$guzzleClient = new \GuzzleHttp\Client(['curl' => [CURLOPT_SSL_VERIFYPEER => false]]);
        $client->setHttpClient($this->httpClient());
        return $client;
    }

    /*
    **create google auth and redirect url
    */
    public function createAuthUrl ($code) {
        $rurl = action('gCalendarController@oauth');
        $this->client->setRedirectUri($rurl);
        if ($code == null) {
            $auth_url = $this->client->createAuthUrl();
            $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
            return $filtered_url;
        } else {
            $this->client->authenticate($code);
            Session()->put('access_token', $this->client->getAccessToken());
            return URL('events');
        }
    }

    /*
    ** Get resources from google calender api
    */
    public function getResources () {
        try {
            $this->client->setAccessToken(session('access_token'));
            $service = new Google_Service_Calendar($this->client);
            $results = $service->events->listEvents($this->calendarId);
            return $results->getItems();
        } catch (\Exception $e) {
            return false;
        }
        
    }

    /*
    **Create event in google calender api
    */
    public function createEvent ($data) {
        try {
            $description = filter_var($data->description, FILTER_SANITIZE_STRING);
            $theDay = filter_var($data->theDay, FILTER_SANITIZE_STRING);
            $startTime = filter_var($theDay.'T'.$data->startTime.'+01:00', FILTER_SANITIZE_STRING);
            $endTime = filter_var($theDay.'T'.$data->endTime.'+01:00', FILTER_SANITIZE_STRING);
            $this->client->setAccessToken(Session('access_token'));
            $service = new Google_Service_Calendar($this->client);
            $event = new Google_Service_Calendar_Event([
                'summary' => $description,
                'description' => $description,
                'start' => ['dateTime' => $startTime],
                'end' => ['dateTime' => $endTime],
                'reminders' => ['useDefault' => true],
            ]);
            
            $results = $service->events->insert($this->calendarId, $event);
            return true;
        } catch (\Exception $e) {
            //return false;
        }
    }

    /*
    **Update existing event
    */
    public function updateEvent ($data, $eventId) {
        try {
            $description = filter_var($data->description, FILTER_SANITIZE_STRING);
            $theDay = filter_var($data->theDay, FILTER_SANITIZE_STRING);
            $startTime = filter_var($theDay.'T'.$data->startTime.'+01:00', FILTER_SANITIZE_STRING);
            $endTime = filter_var($theDay.'T'.$data->endTime.'+01:00', FILTER_SANITIZE_STRING);

            $this->client->setAccessToken(Session('access_token'));
            $service = new Google_Service_Calendar($this->client);
            // retrieve the event from the API.
            $event = $service->events->get($this->calendarId, $eventId);
            $event->setSummary($description);
            $event->setDescription($description);
            //start time
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startTime);
            $event->setStart($start);

            // //end time
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($endTime);
            $event->setEnd($end);

            $updatedEvent = $service->events->update($this->calendarId, $event->getId(), $event);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /*
    **delete event 
    */
    public function deleteEvent ($eventId) {
        try {
            $this->client->setAccessToken(Session('access_token'));
            $service = new Google_Service_Calendar($this->client);
            $service->events->delete($this->calendarId, $eventId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
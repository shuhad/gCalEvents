<?php
namespace App\Interfaces;

interface GClientRepositoryInterface {

    /*
    **Set google client
    */

    public function setGoogleClient ();

    public function httpClient ();


    /*
    **create auth url
    */

    public function createAuthUrl ($code);

    /*
    ** Get resources from google calender api
    */
    public function getResources ();

    /*
    **Create event in google calender api
    */
    public function createEvent ($data);

    /*
    **Update existing event
    */
    public function updateEvent ($data, $eventId);

     /*
    **delete event 
    */
    public function deleteEvent ($eventId);
}
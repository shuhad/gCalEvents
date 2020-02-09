<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \Redirect;
use Session;
use App\Repositories\GClientRepository;
Use Alert;
use App\Http\Requests\CreateEventValidation;
class gCalendarController extends Controller
{
    protected $gcRepo;
    public function __construct(GClientRepository $gcRepo)
    {
      $this->gcRepo = $gcRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (session('access_token') != null) {
            $result = $this->gcRepo->getResources();
            if ($result === false) {
                return ['error' => true];
            } else {
                return response()->json($result);
            }
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    public function oauth()
    {
       $code = request()->get('code');
       return redirect($this->gcRepo->createAuthUrl($code));
        
    }
	public function logout()
    {
       request()->session()->forget('access_token');
	   return redirect()->away('https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=http://127.0.0.1:8000/');
    }
	

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateEventValidation $request)
    {
        if (Session('access_token') != null) {
            $result = $this->gcRepo->createEvent($request);
            if ($result == false) {
                Alert::error('Error', 'Something went wrong, try again');
                return Redirect::to('events');
            }
            Alert::success('Success', 'Event Created!');
            return Redirect::to('events');
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    public function events()
    {
        if (session('access_token') != null) {
            return view('events.index');
        }else{
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $eventId)
    {
        if (Session('access_token') != null) {
            // check if description is updated
            if($request->description == $request->oldEvtDes){
                Alert::info("Info", "The event does not need to be updated");
                return Redirect::to('events');
            }

            // check if event id exist
            if ($eventId == null) {
                Alert::error("Error", "Please select an event");
                return Redirect::to('events');
            }

            // update event
            $result = $this->gcRepo->updateEvent($request, $eventId);
            if ($result == false) {
                Alert::error("Error", "Something went wrong!");
                return Redirect::to('events');
            }
            Alert::success("Success", "Event Updated!");
            return Redirect::to('events');
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($eventId)
    {
        
        if (Session('access_token') != null) {
            // check if event id exist
            if ($eventId == null) {
                Alert::error("Error", "Please select an event");
                return Redirect::to('events');
            }
            $result = $this->gcRepo->deleteEvent($eventId);
            if ($result == false) {
                Alert::error("Error", "Something went wrong!");
                return Redirect::to('events');
            }
            Alert::success("Success", "Event Deleted!");
            return Redirect::to('events');
        } else {
            return redirect()->route('oauthCallback');
        }
    }
}

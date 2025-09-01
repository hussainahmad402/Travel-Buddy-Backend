<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Trip;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([

                "title" => 'required|string|max:255',
                'destination' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'notes' => 'nullable|string',

            ]);

            $trip = Trip::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'destination' => $validated['destination'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Trip created successfully',
                'trip' => $trip,
            ], 201);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function showAllTrips()
    {
        try {
            $trips = Trip::where('user_id', Auth::id())->get();

            return response()->json([
                'status' => true,
                'message' => 'Trips fetched successfully',
                'Trips' => $trips,
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function viewTrip($id)
    {
        try {
            $trip = Trip::where('user_id', Auth::id())->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Trip Found',
                'Trip' => $trip
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,

                'message' => 'Trip not found',
            ], 404);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }
    public function updateTrip(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'title' => 'nullable|string|max:255',
                    'destination' => 'nullable|string|max:255',
                    'start_date' => 'nullable|date',
                    'end_date' => 'nullable|date|after_or_equla:start_date',
                    'notes' => 'nullable|string|max:255',
                ]
            );
            $trip = Trip::where('user_id', Auth::id())->findOrFail($id);
            $trip->update([
                'title' => $request->title ?? $trip->title,
                'destination' => $request->destination ?? $trip->destination,
                'start_date' => $request->start_date ?? $trip->start_date,
                'end_date' => $request->end_date ?? $trip->end_date,
                'notes' => $request->notes ?? $trip->notes,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Trip Updated Successfully',
                'Trip' => $trip,
            ], 200);

        } catch (Exception $error) {

            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function destroyTrip($id)
    {
        try {
            $trip = Trip::where('user_id', Auth::id())->findOrFail($id);
            $trip->delete();
            return response()->json([
                'status' => true,
                'message' => 'Trip Deleted'
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function uploadDocument(Request $request, $id)
    {
        try {

            $request->validate(
                [
                    'file' => 'required|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:2048'
                ]

            );
            $trip = Trip::where('user_id', Auth::id())->findOrFail($id);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('trip_documents','public');

                $documents = Document::create([
                    'trip_id'=> $trip->id,
                    'file_path'=>$path,
                    'file_type'=>$file->getClientOriginalExtension()
                ]);

                return response()->json([
                    'status'=> true,
                    'message'=> 'Document Uploaded',
                    'document'=> $documents
                ],201);
            }
            return response()->json([
                'status'=> false,
                'message'=> 'File not Uploaded'
                ],400);


        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 501);
        }
    }

}

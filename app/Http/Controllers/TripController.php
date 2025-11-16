<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Trip;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

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
                'data' => $trip,
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
                'body' => $trips,
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
        $request->validate([
            'title' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:255',
        ]);

        $trip = Trip::where('user_id', Auth::id())->findOrFail($id);

        $trip->update([
            'title'       => $request->title ?? $trip->title,
            'destination' => $request->destination ?? $trip->destination,
            'start_date'  => $request->start_date ?? $trip->start_date,
            'end_date'    => $request->end_date ?? $trip->end_date,
            'notes'       => $request->notes ?? $trip->notes,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Trip updated successfully',
            'data'    => $trip, // <-- Correct key for Flutter
        ], 200);

    } catch (\Exception $error) {
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
            // ✅ Validate uploaded file
            $request->validate([
                'file' => 'required|file|max:10240', // max 10 MB
            ]);

            $trip = Trip::where('user_id', Auth::id())->find($id);

            if (!$trip) {
                return response()->json([
                    'status' => false,
                    'message' => 'Trip not found',
                ], 404);
            }

            // ✅ Store the file directly in public/documents
            $uploadedFile = $request->file('file');
            $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
            $uploadedFile->move(public_path('documents'), $fileName);

            // ✅ Save document record in database
            $document = Document::create([
                'trip_id' => $trip->id,
                'file_path' => 'documents/' . $fileName, // path accessible publicly
                'file_name' => $fileName,
                'file_type' => $uploadedFile->getClientOriginalExtension(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Document uploaded successfully',
                'document' => $document,
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }

public function listDocuments($id)
{
    try {
        // Get the trip for the authenticated user
        $trip = Trip::where('user_id', Auth::id())->find($id);

        if (!$trip) {
            return response()->json([
                'status' => false,
                'message' => 'Trip or Document not Found',
            ], 200);
        }

        $documents = $trip->documents->map(function ($doc) {
            // Remove the timestamp prefix for display
            $originalName = preg_replace('/^\d+_/', '', $doc->file_name);

            // Convert stored file_path to full URL
            $fileUrl = url($doc->file_path); // e.g., http://yourdomain.com/storage/documents/...

            return [
                'id' => $doc->id,
                'trip_id' => $doc->trip_id,
                'file_path' => $fileUrl, // send full URL
                'file_name' => $originalName,
                'file_type' => $doc->file_type,
                'created_at' => $doc->created_at,
                'updated_at' => $doc->updated_at,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Documents fetched successfully',
            'documents' => $documents
        ], 200);

    } catch (\Exception $error) {
        return response()->json([
            'status' => false,
            'message' => $error->getMessage()
        ], 500);
    }
}

    public function deleteDocuments($id)
    {
        try {
            $trip = Trip::where('user_id', Auth::id())->find($id);
            if (!$trip) {
                return response()->json([
                    'status' => false,
                    'message' => 'Trip or Document Not found'
                ], 404);
            }
            $trip->delete();
            return response()->json([
                'status' => true,
                'message' => 'Trip Deleted Successfully'
            ], 200);

        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 200);
        }
    }

    public function markAsFavourite($id)
    {
        try {

            $trip = Trip::where('user_id', Auth::id())->findOrFail($id);
            $trip->favourite = true;
            $trip->save();

            return response()->json([
                'status' => true,
                'message' => 'Trip marked as favourite',
                'trip' => $trip,
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function unmarkAsFavourite($id)
    {
        try {
            $trip = Trip::where('user_id', Auth::id())->findOrFail($id);
            $trip->favourite = false;
            $trip->save();

            return response()->json([
                'status' => true,
                'message' => 'Trip unmarked as favourite',
                'trip' => $trip,
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function listFavouriteTrips()
    {
        try {
            // $favouriteTrips = Trip::where('user_id', Auth::id())
            //     ->where('favourite', true)
            //     ->get();
            $favouriteTrips = Trip::where('favourite', true)->get();

            if ($favouriteTrips->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No favourite trips found.',
                    'trips' => [],
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Favourite trips fetched successfully.',
                'data' => $favouriteTrips,
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching favourite trips.',
                'error' => $error->getMessage(),
            ], 500);
        }
    }



}

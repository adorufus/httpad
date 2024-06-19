<?php

namespace App\Http\Controllers;

use App\Models\SavedRequest;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiRequestController extends Controller
{
    public function sendRequest(Request $request)
    {
        $client = new Client();
        $method = $request->input('method');
        $url = $request->input('url');
        $headers = $request->input('headers', []);

        // Initialize options for the request
        $options = [
            'headers' => $headers,
        ];

        // Determine the type of body and handle accordingly
        if ($request->has('bodyType') && $request->input('bodyType') === 'json') {
            $body = $request->input('body', []);
            $options['json'] = $body;
        } else {
            // Handle form data, including files
            $formData = [];
            foreach ($request->except(['method', 'url', 'headers', 'bodyType', 'body']) as $key => $value) {
                if ($request->hasFile($key)) {
                    $formData[$key] = fopen($request->file($key)->getRealPath(), 'r');
                } else {
                    $formData[$key] = $value;
                }
            }
            $options['multipart'] = [];
            foreach ($formData as $name => $contents) {
                $options['multipart'][] = [
                    'name'     => $name,
                    'contents' => $contents,
                ];
            }
        }

        // Send the request
        try {
            $response = $client->request($method, $url, $options);
            return response()->json([
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => json_decode($response->getBody(), true),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function saveRequest(Request $request)
    {
        $savedRequest = SavedRequest::create([
            'user_id' => Auth::id(),
            'method' => $request->input('method'),
            'url' => $request->input('url'),
            'headers' => $request->input('headers'),
            'bodyType' => $request->input('bodyType'),
            'body' => $request->input('body'),
            'formFields' => $request->input('formFields'),
        ]);

        return response()->json($savedRequest);
    }

    public function getRequests()
    {
        $requests = SavedRequest::where('user_id', Auth::id())->get();
        return response()->json($requests);
    }
}

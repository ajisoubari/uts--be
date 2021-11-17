<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patients;
use Illuminate\Support\Facades\Validator;

class PatientsController extends Controller
{
    public function index(Request $request)
    {
    
        $patients = new Patients();
        // ambil semua data
        $data = $patients::all();
        // validasi jika data tidak ada
        if ($data == null) {
            $response = [
                'meta' => [
                    'code' => '200',
                    'message' => 'Dat is empty'
                ]
            ];

            return response()->json($response, 200);
        }
        // sukses respon
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get All Resource'
            ],
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function show(Request $request, $id)
    {
        $patients = new Patients();
        // ambil data berdasarkan id
        $data = $patients::where('id', $id)->first();
        // respon jika data tidak ditemukan
        if ($data == null) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        // suckses respon
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get Detail Resource'
            ],
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $patients = new Patients();
        // validasi parameter
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'status' => 'required',
            'in_date' => 'required|date_format:Y-m-d',
            'out_date' => 'required|date_format:Y-m-d'
        ]);
        
        // validasi jika parameter tidak sesuai 
        if ($validator->fails()) { 
            $response = [
                'meta' => [
                    'message' => $validator->errors()
                ]
            ];

            return response()->json($response);
        }
        // cek data berdasarkan nama
        $check = $patients::where('name', $request->name)->first();
        // validasi data yang sudah ada
        if ($check != null) {
            $response = [
                'meta' => [
                    'message' => 'Data already exists'
                ]
            ];

            return response()->json($response);
        }
        // simpan data ke database
        $patients->name = $request->name;
        $patients->phone = $request->phone;
        $patients->address = $request->address;
        $patients->status = $request->status;
        $patients->in_date_at = $request->in_date;
        $patients->out_date_at = $request->out_date;
        $patients->created_at = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $patients->save();

        $response = [
            'meta' => [
                'code' => '201',
                'message' => 'Resource is added successfully'
            ],
            'data' => $patients
        ];

        return response()->json($response, 201);
        
    }

    public function update(Request $request, $id)
    {
        $patients = new Patients();
        // validasi varameter
        if ($request->phone != null || $request->in_date != null || $request->out_date != null) {
            $validator = Validator::make($request->all(), [
                'phone' => 'numeric',
                'in_date' => 'date_format:Y-m-d',
                'out_date' => 'date_format:Y-m-d'
            ]);
            // validasi jika gagal
    
            if ($validator->fails()) { 
                $response = [
                    'meta' => [
                        'message' => $validator->errors()
                    ]
                ];
    
                return response()->json($response);
            }
        }

        // update data
        $doChange = $patients::where('id', $id)->first();
        // validasi jika risorce tidak ada
        if ($doChange == null) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' =>'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        // simpan data ke database
        $doChange->name = $request->name == null ? $doChange->name : $request->name;
        $doChange->phone = $request->phone == null ? $doChange->phone : $request->phone;
        $doChange->address = $request->address == null ? $doChange->address : $request->address;
        $doChange->status = $request->status == null ? $doChange->status : $request->status;
        $doChange->in_date_at = $request->in_date == null ? $doChange->in_date_at : $request->in_date;
        $doChange->out_date_at = $request->out_date == null ? $doChange->out_date_at : $request->out_date;
        $doChange->updated_at = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $doChange->update();

        
        // sukses respon
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Resource is update successfully'
            ],
            'data' => $doChange
        ];
        return response()->json($response, 200);
    }

    public function destroy(Request $request, $id)
    {
        $patients = new Patients();

        $destroy = $patients::where('id', $id)->first();
        // validasi jika risorce tidak ada
        if ($destroy == null) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        
        $destroy->delete();
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Resource is delete successfully'
            ]
        ];

        return response()->json($response, 200);
    }

    public function search(Request $request, $name)
    {

        $patients = new Patients();
        // cari data berdasarkan nama
        $search = $patients::where('name', 'like', '%' . $name . '%')->get();
        // validasi jika data tidak ada
        if (count($search) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];
            return response()->json($response, 404);
        } 

        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get searched resource'
            ],
            'data' => $search
        ];
        
        return response()->json($response, 200);
    }

    public function positive(Request $request)
    {
        $patients = new Patients();
        // ambil data positif
        $data = $patients::where('status', 'positive')->get();
        // validasi data jika tidak ada
        if (count($data) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        // sukses respon
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get positive resource'
            ],
            'total' => count($data),
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    public function recovered(Request $request)
    {
        $patients = new Patients();
        // daptkan data recovered
        $data = $patients::where('status', 'recovered')->get();
        // validasi data jika tidak ada
        if (count($data) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        // sukses respon
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get recovered resource'
            ],
            'total' => count($data),
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    public function dead(Request $request)
    {
        
        $patients = new Patients();
        // ambil data dead
        $data = $patients::where('status', 'dead')->get();
        // validasi data jika tidak ada
        if (count($data) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        // sukses respon
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get dead resource'
            ],
            'total' => count($data),
            'data' => $data
        ];

        return response()->json($response, 200);
    }

}

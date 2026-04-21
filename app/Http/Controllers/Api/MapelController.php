<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mapel = Mapel::all()->map(function($item) {
            return [
                'id' => $item->id,
                'kode_mapel' => $item->kode_mapel,
                'nama_mapel' => $item->nama_mapel,
                'links' => [
                    'self' => url('/api/mapel/' . $item->id)
                ]
            ];
        });
        return $this->success([
            'mapels' => $mapel,
            'links' => [
                'self' => url('/api/mapel')
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_mapel' => 'required|string|unique:mapel,kode_mapel',
            'nama_mapel' => 'required|string'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $mapel = new Mapel();
        $mapel->kode_mapel = $request->kode_mapel;
        $mapel->nama_mapel = $request->nama_mapel;

        $saveMapel = $mapel->save();
        if ($saveMapel) {
            $data = [
                'id' => $mapel->id,
                'kode_mapel' => $mapel->kode_mapel,
                'nama_mapel' => $mapel->nama_mapel,
                'links' => [
                    'self' => url('/api/mapel/' . $mapel->id),
                    'collection' => url('/api/mapel')    
                ]
            ];
            return $this->success($data,201,'Mapel berhasil ditambahkan');
        } else {
            return $this->failedResponse('Mapel gagal ditambahkan',500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mapel $mapel)
    {
        $mapel->load('jadwals.guru', 'jadwals.kelas');

        $data = [
            'id' => $mapel->id,
            'kode_mapel' => $mapel->kode_mapel,
            'nama_mapel' => $mapel->nama_mapel,
            'jadwals' => $mapel->jadwals->map(function($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'hari' => $jadwal->hari,
                    'jam_pelajaran' => $jadwal->jam_pelajaran,
                    'guru' => [
                        'id' => $jadwal->guru->id,
                        'nama' => $jadwal->guru->nama,
                        'links' => [
                            'self' => url('/api/guru/' . $jadwal->guru->id)
                        ]
                    ],
                    'kelas' => [
                        'id' => $jadwal->kelas->id,
                        'nama_kelas' => $jadwal->kelas->nama_kelas,
                        'links' => [
                            'self' => url('/api/kelas/' . $jadwal->kelas->id)
                        ]
                    ],

                    'links' => [
                        'self' => url('/api/jadwal/' . $jadwal->id)
                    ]
                ];
            }),
            'links' => [
                'self' => url('/api/mapel/' . $mapel->id),
                'collection' => url('/api/mapel'),
                'jadwals' => url('/api/mapel/' . $mapel->id . '/jadwal')
            ]
        ];

        return $this->success($data,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mapel $mapel)
    {
        $validator = Validator::make($request->all(), [
            'kode_mapel' => 'required|string|unique:mapel,kode_mapel,' .$mapel->id,
            'nama_mapel' => 'required|string'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $mapel->kode_mapel = $request->kode_mapel;
        $mapel->nama_mapel = $request->nama_mapel;

        $saved = $mapel->save();
        if ($saved) {
            $data = [
                'id' => $mapel->id,
                'kode_mapel' => $mapel->kode_mapel,
                'nama_mapel' => $mapel->nama_mapel,
                'links' => [
                    'self' => url('/api/mapel/' . $mapel->id),  
                    'collection' => url('/api/mapel')
                ]
            ];
            return $this->success($data,200,'Mapel berhasil diupdate');
        } else {
            return $this->failedResponse('Mapel gagal diupdate',500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mapel $mapel)
    {
        $deleteData = $mapel->delete();

        if ($deleteData) {
            return $this->success([
                'links' => [
                    'collection' => url('/api/mapel')
                ]
            ], 200,'Mapel berhasil dihapus');
        } else {
            return $this->failedResponse('Mapel gagal dihapus!',500);
        }
    }

    public function jadwal(Mapel $mapel)
    {
        $mapel->load('jadwals.guru', 'jadwals.kelas');

        $jadwals = $mapel->jadwals->map(function($jadwal) {
            return [
                'id' => $jadwal->id,
                'hari' => $jadwal->hari,
                'jam_pelajaran' => $jadwal->jam_pelajaran,
                'guru' => [
                    'id' => $jadwal->guru->id,
                    'nama' => $jadwal->guru->nama,
                    'links' => [
                        'self' => url('/api/guru/' . $jadwal->guru->id)
                    ]
                ],
                'kelas' => [
                    'id' => $jadwal->kelas->id,
                    'nama_kelas' => $jadwal->kelas->nama_kelas,
                    'links' => [
                        'self' => url('/api/kelas/' . $jadwal->kelas->id)
                    ]
                ],
                'links' => [
                    'self' => url('/api/jadwal/' . $jadwal->id)
                ]
            ];
        });

        return $this->success([
            'mapel' => [
                'id' => $mapel->id,
                'nama_mapel' => $mapel->nama_mapel,
                'links' => [
                    'self' => url('/api/mapel/' . $mapel->id),
                ]
            ],
            'jadwals' => $jadwals,
            'links' => [
                'self' => url('/api/mapel/' . $mapel->id . '/jadwal'),
                'mapel' => url('/api/mapel/' . $mapel->id),
                'collection' => url('/api/jadwal')
            ]
        ], 200);
    }
}
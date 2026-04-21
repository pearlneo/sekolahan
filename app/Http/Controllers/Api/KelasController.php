<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::all()->map(function($item) {
            return [
                'id' => $item->id,
                'kode_kelas' => $item->kode_kelas,
                'nama_kelas' => $item->nama_kelas,
                'links' => [
                    'self' => url('/api/kelas/' . $item->id)
                ]
            ];
        });
        return $this->success([
            'kelas' => $kelas,
            'links' => [
                'self' => url('/api/kelas')
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas',
            'nama_kelas' => 'required|string'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $kelas = new Kelas();
        $kelas->kode_kelas = $request->kode_kelas;
        $kelas->nama_kelas = $request->nama_kelas;

        $saveKelas = $kelas->save();
        if ($saveKelas) {
            $data = [
                'id' => $kelas->id,
                'kode_kelas' => $kelas->kode_kelas,
                'nama_kelas' => $kelas->nama_kelas,
                'links' => [
                    'self' => url('/api/kelas/' . $kelas->id),
                    'collection' => url('/api/kelas')
                ]
            ];
            return $this->success($data,201,'Kelas berhasil ditambahkan');
        } else {
            return $this->failedResponse('Kelas gagal ditambahkan',500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas) 
    { 
        $kelas->load('siswas', 'jadwals'); 
        
        return $this->success([ 
            'id' => $kelas->id, 
            'kode_kelas' => $kelas->kode_kelas, 
            'nama_kelas' => $kelas->nama_kelas, 
            'siswas' => $kelas->siswas->map(function($siswa) { 
                return [ 
                    'id' => $siswa->id, 
                    'nama' => $siswa->nama, 
                    'links' => [ 
                        'self' => url('/api/siswa/' . $siswa->id) 
                    ] 
                ]; 
            }), 
            'jadwals' => $kelas->jadwals->map(function($jadwal) { 
                return [ 
                    'id' => $jadwal->id, 
                    'hari' => $jadwal->hari, 
                    'jam_pelajaran' => $jadwal->jam_pelajaran, 
                    'links' => [ 
                        'self' => url('/api/jadwal/' . $jadwal->id) 
                    ]
                ]; 
            }), 
            'links' => [ 
                'self' => url('/api/kelas/' . $kelas->id), 
                'collection' => url('/api/kelas'), 
                'siswas' => url('/api/kelas/' . $kelas->id . '/siswa'), 
                'jadwals' => url('/api/kelas/' . $kelas->id . '/jadwal') 
            ] 
        ], 200); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas,'.$kelas->id,
            'nama_kelas' => 'required|string'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $kelas->kode_kelas = $request->kode_kelas;
        $kelas->nama_kelas = $request->nama_kelas;

        $saved = $kelas->save();
        if ($saved) {
            $data = [
                'id' => $kelas->id,
                'kode_kelas' => $kelas->kode_kelas,
                'nama_kelas' => $kelas->nama_kelas,
                'links' => [
                    'self' => url('/api/kelas/' . $kelas->id),
                    'collection' => url('/api/kelas')
                ]
            ];
            return $this->success($data,200,'Kelas berhasil diupdate');
        } else {
            return $this->failedResponse('Kelas gagal diupdate',500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Kelas $kelas)
    {
        $deleteData = $kelas->delete();

        if ($deleteData) {
            return $this->success([
                'links' => [
                    'collection' => url('/api/kelas')
                ]
            ], 200, 'Kelas berhasil dihapus');
        } else {
            return $this->failedResponse('Kelas gagal dihapus!',500);
        }
    }

    public function siswa(Kelas $kelas)
    {
        $kelas->load('siswas', 'jadwals');

        return $this->success([
            'id' => $kelas->id,
            'kode_kelas' => $kelas->kode_kelas,
            'nama_kelas' => $kelas->nama_kelas,
            'siswas' => $kelas->siswas->map(function($siswa) {
                return [
                    'id' => $siswa->id,
                    'nama' => $siswa->nama,
                    'links' => [
                        'self' => url('/api/siswa/' . $siswa->id)
                    ]
                ];
            }),
            'jadwals' => $kelas->jadwals->map(function($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'hari' => $jadwal->hari,
                    'jam_pelajaran' => $jadwal->jam_pelajaran,
                    'links' => [
                        'self' => url('/api/jadwal/' . $jadwal->id)
                    ]
                ];
            }),
        
            'links' => [
                'self' => url('/api/kelas/' . $kelas->id),
                'collection' => url('/api/kelas'),
                'siswas' => url('/api/kelas/' . $kelas->id . '/siswa'),
                'jadwals' => url('/api/kelas/' . $kelas->id . '/jadwal')
             ]
        ], 200);
    }

    public function jadwal(Kelas $kelas)
    {
        $kelas->load('jadwals.guru', 'jadwals.mapel');
        
        $jadwals = $kelas->jadwals->map(function($jadwal) {
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
                'mapel' => [
                    'id' => $jadwal->mapel->id,
                    'nama_mapel' => $jadwal->mapel->nama_mapel,
                    'links' => [
                        'self' => url('/api/mapel/' . $jadwal->mapel->id)
                    ]

                ],
                'links' => [
                    'self' => url('/api/jadwal/' . $jadwal->id)
                ]
            ];
        });
        return $this->success([
            'kelas' => [
                'id' => $kelas->id,
                'nama_kelas' => $kelas->nama_kelas,
                'links' => [
                    'self' => url('/api/kelas/' . $kelas->id)
                ]
            ],
            'jadwals' => $jadwals,
            'links' => [
                'self' => url('/api/kelas/' . $kelas->id . '/jadwal'),
                'kelas' => url('/api/kelas/' . $kelas->id),
                'collection' => url('/api/jadwal')
             ]
        ], 200);
    }
}
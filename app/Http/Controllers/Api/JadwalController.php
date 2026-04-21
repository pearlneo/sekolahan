<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwal = Jadwal::with('kelas','mapel','guru')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'hari' => $item->hari,
                'jam_pelajaran' => $item->jam_pelajaran,
                'kelas' => [
                    'id' => $item->kelas->id,
                    'nama_kelas' => $item->kelas->nama_kelas,
                    'links' => [
                        'self' => url('/api/kelas/' . $item->kelas->id)
                    ]
                ],
                'mapel' => [
                    'id' => $item->mapel->id,
                    'nama_mapel' => $item->mapel->nama_mapel,
                    'links' => [
                        'self' => url('/api/mapel/' . $item->mapel->id)
                    ]
                ],
                'guru' => [
                    'id' => $item->guru->id,
                    'nama' => $item->guru->nama,
                    'links' => [
                        'self' => url('/api/guru/' . $item->guru->id)
                    ]
                ],

                'links' => [
                    'self' => url('/api/jadwal/' . $item->id),
                    'kelas_jadwals' => url('/api/kelas/' . $item->kelas->id . '/jadwal'),
                    'mapel_jadwals' => url('/api/mapel/' . $item->mapel->id . '/jadwal'),
                    'guru_jadwals' => url('/api/guru/' . $item->guru->id . '/jadwal'),
                ]
            ];
        });
        return $this->success([
            'jadwals' => $jadwal,
            'links' => [
                'self' => url('/api/jadwal')
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_pelajaran' => 'required|string'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $jadwal = new Jadwal();
        $jadwal->kelas_id = $request->kelas_id;
        $jadwal->mapel_id = $request->mapel_id;
        $jadwal->guru_id = $request->guru_id;
        $jadwal->hari = $request->hari;
        $jadwal->jam_pelajaran = $request->jam_pelajaran;

        $saveJadwal = $jadwal->save();
        if ($saveJadwal) {
            $data = [
                'id' => $jadwal->id,
                'hari' => $jadwal->hari,
                'links' => [
                    'self' => url('/api/jadwal/' . $jadwal->id),
                    'collection' => url('/api/jadwal'),
                ]
            ];
            return $this->success($data,201,'Jadwal berhasil ditambahkan');
        } else {
            return $this->failedResponse('Jadwal gagal ditambahkan',500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Jadwal $jadwal)
    {
        $jadwal->load('kelas', 'mapel', 'guru');

        $data = [
            'id' => $jadwal->id,
            'hari' => $jadwal->hari,
            'jam_pelajaran' => $jadwal->jam_pelajaran,
            'kelas' => [
                'id' => $jadwal->kelas->id,
                'nama_kelas' => $jadwal->kelas->nama_kelas,
                'links' => [
                    'self' => url('/api/kelas/' . $jadwal->kelas->id)
                ]
            ],
            'mapel' => [
                'id' => $jadwal->mapel->id,
                'nama_mapel' => $jadwal->mapel->nama_mapel,
                'links' => [
                    'self' => url('/api/mapel/' . $jadwal->mapel->id)
                ]
            ],
            'guru' => [
                'id' => $jadwal->guru->id,
                'nama' => $jadwal->guru->nama,
                'links' => [
                    'self' => url('/api/guru/' . $jadwal->guru->id)
                ]
            ],
            'links' => [
                'self' => url('/api/jadwal/' . $jadwal->id),
                'collection' => url('/api/jadwal'),
                'kelas' => url('/api/kelas/' . $jadwal->kelas->id),
                'mapel' => url('/api/mapel/' . $jadwal->mapel->id),
                'guru' => url('/api/guru/' . $jadwal->guru->id),

                'kelas_jadwals' => url('/api/kelas/' . $jadwal->kelas->id . '/jadwal'),
                'mapel_jadwals' => url('/api/mapel/' . $jadwal->mapel->id . '/jadwal'),
                'guru_jadwals' => url('/api/guru/' . $jadwal->guru->id . '/jadwal'),
            ]
        ];

        return $this->success($data,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_pelajaran' => 'required|string'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $jadwal->kelas_id = $request->kelas_id;
        $jadwal->mapel_id = $request->mapel_id;
        $jadwal->guru_id = $request->guru_id;
        $jadwal->hari = $request->hari;
        $jadwal->jam_pelajaran = $request->jam_pelajaran;

        $saved = $jadwal->save();
        if ($saved) {
            $data = [
                'id' => $jadwal->id,
                'hari' => $jadwal->hari,
                'links' => [
                    'self' => url('/api/jadwal/' . $jadwal->id), 
                    'collection' => url('/api/jadwal'),  
                ]
            ];
            return $this->success($data,200,'Jadwal berhasil diupdate');
        } else {
            return $this->failedResponse('Jadwal gagal diupdate',500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jadwal $jadwal)
    {
        $deletedData = $jadwal->delete();

        if ($deletedData) {
            return $this->success([
                'links' => [
                    'collection' => url('/api/jadwal')
                ]
            ], 200,'Jadwal berhasil dihapus');
        } else {
            return $this->failedResponse('Jadwal gagal dihapus',500);
        }
    }
}

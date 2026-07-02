<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Quotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // --- 5 Klien ---
        $clients = [
            ['name' => 'PT Maju Bersama', 'address' => 'Jl. Sudirman No. 10, Jakarta Pusat', 'city' => 'Jakarta', 'phone' => '021-1234567', 'pic_name' => 'Budi Santoso'],
            ['name' => 'CV Karya Mandiri', 'address' => 'Jl. Gatot Subroto No. 45, Jakarta Selatan', 'city' => 'Jakarta', 'phone' => '021-9876543', 'pic_name' => 'Sari Dewi'],
            ['name' => 'PT Nusa Indah', 'address' => 'Jl. MT Haryono No. 88, Bekasi', 'city' => 'Bekasi', 'phone' => '021-4445555', 'pic_name' => 'Ahmad Fauzi'],
            ['name' => 'PT Sentosa Jaya', 'address' => 'Jl. Raya Serpong No. 12, Tangerang', 'city' => 'Tangerang', 'phone' => '021-7778888', 'pic_name' => 'Rina Hartati'],
            ['name' => 'CV Bintang Timur', 'address' => 'Jl. Kalimalang No. 33, Bekasi Barat', 'city' => 'Bekasi', 'phone' => '021-3332222', 'pic_name' => 'Doni Prasetyo'],
        ];

        $createdClients = [];
        foreach ($clients as $clientData) {
            $createdClients[] = Client::create(array_merge($clientData, ['created_by' => $admin?->id]));
        }

        // --- Data Proyek ---
        $projects = [
            [
                'name' => 'SENAYAN SQUARE (Misc.)', 
                'subname' => 'HTL - ADD WORK for SHENSHU REPAIR 2nd FL', 
                'service' => ['Supply', 'Delivery'], 
                'category' => 'MISC BUILDING WORK',
                'subject' => 'Batu Koral Alor',
                'items' => [
                    ['uraian' => 'Stone Koral Alor size 2-3cm @5kg', 'qty' => 10, 'unit' => 'Bag', 'harga_satuan' => 90000, 'sub_total' => 900000],
                    ['uraian' => 'Transportation to Site', 'qty' => 1, 'unit' => 'Ls', 'harga_satuan' => 150000, 'sub_total' => 150000],
                ]
            ],
            [
                'name' => 'GRAND MALL BEKASI', 
                'subname' => 'Area Parkir B2', 
                'service' => ['Installation'], 
                'category' => 'MISC BUILDING WORK',
                'subject' => 'Pemasangan Pipa Saluran Air',
                'items' => [
                    ['uraian' => 'Pipa PVC 4 Inch', 'qty' => 20, 'unit' => 'Btg', 'harga_satuan' => 120000, 'sub_total' => 2400000],
                    ['uraian' => 'Jasa Pemasangan', 'qty' => 1, 'unit' => 'Ls', 'harga_satuan' => 1500000, 'sub_total' => 1500000],
                ]
            ],
            [
                'name' => 'APARTEMEN RESIDIA', 
                'subname' => 'Tower C Unit 1-50', 
                'service' => ['Supply'], 
                'category' => 'MISC BUILDING WORK',
                'subject' => 'Peralatan Listrik dan Lampu',
                'items' => [
                    ['uraian' => 'Lampu LED Philips 12W', 'qty' => 100, 'unit' => 'Pcs', 'harga_satuan' => 45000, 'sub_total' => 4500000],
                    ['uraian' => 'Kabel Listrik NYM 2x1.5', 'qty' => 5, 'unit' => 'Roll', 'harga_satuan' => 350000, 'sub_total' => 1750000],
                ]
            ],
            [
                'name' => 'GEDUNG PERKANTORAN PRIMA', 
                'subname' => 'Lantai 5-10', 
                'service' => ['Demolish', 'Dismantle'], 
                'category' => 'MISC BUILDING WORK',
                'subject' => 'Pembongkaran Partisi Gypsum',
                'items' => [
                    ['uraian' => 'Jasa Pembongkaran Partisi', 'qty' => 150, 'unit' => 'm2', 'harga_satuan' => 25000, 'sub_total' => 3750000],
                    ['uraian' => 'Pembuangan Puing (Buang Keluar)', 'qty' => 5, 'unit' => 'Rit', 'harga_satuan' => 300000, 'sub_total' => 1500000],
                ]
            ],
            [
                'name' => 'PASAR MODERN TANGERANG', 
                'subname' => 'Blok C & D', 
                'service' => ['Supply', 'Installation'], 
                'category' => 'MISC BUILDING WORK',
                'subject' => 'Kerangka Baja Ringan',
                'items' => [
                    ['uraian' => 'Baja Ringan Canal C 0.75', 'qty' => 50, 'unit' => 'Btg', 'harga_satuan' => 85000, 'sub_total' => 4250000],
                    ['uraian' => 'Jasa Pemasangan Baja Ringan', 'qty' => 1, 'unit' => 'Ls', 'harga_satuan' => 2500000, 'sub_total' => 2500000],
                ]
            ],
        ];

        // 6 bulan berdekatan: Januari - Juni 2026
        $months = [
            Carbon::create(2026, 1),
            Carbon::create(2026, 2),
            Carbon::create(2026, 3),
            Carbon::create(2026, 4),
            Carbon::create(2026, 5),
            Carbon::create(2026, 6),
        ];

        $types = ['po', 'po', 'wo', 'po', 'wo', 'po', 'wo', 'po', 'wo', 'po', 'wo', 'po', 'po', 'wo', 'wo'];

        $yearCounters = [];

        for ($i = 0; $i < 15; $i++) {
            $type      = $types[$i];
            $month     = $months[$i % 6];
            $day       = rand(1, 28);
            $date      = $month->copy()->setDay($day);
            $client    = $createdClients[$i % 5];
            $project   = $projects[$i % 5];

            $monthRoman = $this->toRoman($date->month);
            $year       = $date->year;

            if (!isset($yearCounters[$year])) {
                $yearCounters[$year] = 0;
            }
            $yearCounters[$year]++;
            $no = str_pad($yearCounters[$year], 3, '0', STR_PAD_LEFT);

            $abbr = $client->abbreviation ?? 'IMG';
            $refNumber = "{$no}/SPH/IMG-{$abbr}/{$monthRoman}/{$year}/" . str_pad($day, 2, '0', STR_PAD_LEFT);

            $serviceString = implode(' & ', $project['service']);

            // Create varied dummy data structures
            $items = [];
            $randCase = $i % 3;

            if ($randCase == 0) {
                // Complex structure: Headers + Items + Discount
                $items = [
                    [
                        'type' => 'header_row',
                        'data' => ['uraian' =>  $serviceString]
                    ],
                    [
                        'type' => 'item_row',
                        'data' => ['uraian' => $project['items'][0]['uraian'], 'qty' => $project['items'][0]['qty'], 'unit' => $project['items'][0]['unit'], 'harga_satuan' => $project['items'][0]['harga_satuan'], 'sub_total' => $project['items'][0]['sub_total']]
                    ],
                    [
                        'type' => 'header_row',
                        'data' => ['uraian' => 'Transport & Logistik']
                    ],
                    [
                        'type' => 'item_row',
                        'data' => ['uraian' => $project['items'][1]['uraian'], 'qty' => $project['items'][1]['qty'], 'unit' => $project['items'][1]['unit'], 'harga_satuan' => $project['items'][1]['harga_satuan'], 'sub_total' => $project['items'][1]['sub_total']]
                    ],
                    [
                        'type' => 'discount_row',
                        'data' => ['uraian' => 'Discount Khusus Akhir Tahun', 'qty' => null, 'unit' => null, 'harga_satuan' => 500000, 'sub_total' => -500000]
                    ]
                ];
            } elseif ($randCase == 1) {
                // Flat structure: Just items, no headers
                $items = [
                    [
                        'type' => 'item_row',
                        'data' => ['uraian' => $project['items'][0]['uraian'], 'qty' => $project['items'][0]['qty'], 'unit' => $project['items'][0]['unit'], 'harga_satuan' => $project['items'][0]['harga_satuan'], 'sub_total' => $project['items'][0]['sub_total']]
                    ],
                    [
                        'type' => 'item_row',
                        'data' => ['uraian' => $project['items'][1]['uraian'], 'qty' => $project['items'][1]['qty'], 'unit' => $project['items'][1]['unit'], 'harga_satuan' => $project['items'][1]['harga_satuan'], 'sub_total' => $project['items'][1]['sub_total']]
                    ]
                ];
            } else {
                // Medium structure: 1 Header, multiple items, and a Note
                $items = [
                    [
                        'type' => 'header_row',
                        'data' => ['uraian' => 'Rincian ' . $serviceString]
                    ],
                    [
                        'type' => 'item_row',
                        'data' => ['uraian' => $project['items'][0]['uraian'], 'qty' => $project['items'][0]['qty'], 'unit' => $project['items'][0]['unit'], 'harga_satuan' => $project['items'][0]['harga_satuan'], 'sub_total' => $project['items'][0]['sub_total']]
                    ],
                    [
                        'type' => 'item_row',
                        'data' => ['uraian' => $project['items'][1]['uraian'], 'qty' => $project['items'][1]['qty'], 'unit' => $project['items'][1]['unit'], 'harga_satuan' => $project['items'][1]['harga_satuan'], 'sub_total' => $project['items'][1]['sub_total']]
                    ],
                    [
                        'type' => 'note_row',
                        'data' => ['uraian' => 'Catatan: Sudah termasuk biaya packing.']
                    ]
                ];
            }
            $total = collect($items)->sum(function($i) {
                return $i['data']['sub_total'] ?? 0;
            });

            Quotation::create([
                'client_id'           => $client->id,
                'reference_number'    => $refNumber,
                'date'                => $date->toDateString(),
                'project_name'        => $project['name'],
                'project_subname'     => $project['subname'],
                'service_type'        => $project['service'],
                'work_category'       => $project['category'],
                'subject_description' => $project['subject'],
                'type'                => $type,
                'status'              => 'draft', // Tetap draft aman
                'items'               => $items,
                'total_amount'        => $total,
                'signature_name'      => 'Bambang H',
                'signature_role'      => 'Marketing',
                'created_by'          => $admin?->id,
            ]);
        }

        // --- KODE PROSES UPDATE OTOMATIS DI SINI SUDAH DIHAPUS ---
    }

    private function toRoman(int $month): string
    {
        $map = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        return $map[$month - 1];
    }
}
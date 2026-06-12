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
            ['name' => 'SENAYAN SQUARE', 'subname' => 'Gedung A Lt. 3', 'service' => 'Pengadaan Material Bangunan', 'category' => 'Sipil'],
            ['name' => 'GRAND MALL BEKASI', 'subname' => 'Area Parkir B2', 'service' => 'Jasa Instalasi Mekanikal', 'category' => 'Mekanikal'],
            ['name' => 'APARTEMEN RESIDIA', 'subname' => 'Tower C Unit 1-50', 'service' => 'Pengadaan Peralatan Listrik', 'category' => 'Elektrikal'],
            ['name' => 'GEDUNG PERKANTORAN PRIMA', 'subname' => 'Lantai 5-10', 'service' => 'Jasa Finishing Interior', 'category' => 'Interior'],
            ['name' => 'PASAR MODERN TANGERANG', 'subname' => 'Blok C & D', 'service' => 'Pengadaan Kerangka Baja', 'category' => 'Struktural'],
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

            $qty1 = rand(5, 50);
            $harga1 = rand(500000, 5000000);
            $qty2 = 1;
            $harga2 = rand(100000, 500000);
            
            $items = [
                ['uraian' => 'Material ' . $project['service'], 'qty' => $qty1, 'unit' => 'Bag', 'harga_satuan' => $harga1, 'sub_total' => $qty1 * $harga1],
                ['uraian' => 'Transportation to Site', 'qty' => $qty2, 'unit' => 'Ls', 'harga_satuan' => $harga2, 'sub_total' => $qty2 * $harga2],
            ];
            $total = collect($items)->sum('sub_total');

            Quotation::create([
                'client_id'           => $client->id,
                'reference_number'    => $refNumber,
                'date'                => $date->toDateString(),
                'project_name'        => $project['name'],
                'project_subname'     => $project['subname'],
                'service_type'        => $project['service'],
                'work_category'       => $project['category'],
                'subject_description' => $project['service'] . ' pada proyek ' . $project['name'] . '.',
                'type'                => $type,
                'status'              => 'draft', // Tetap draft aman
                'items'               => $items,
                'total_amount'        => $total,
                'signature_name'      => 'Direktur Utama',
                'signature_role'      => 'Direktur',
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
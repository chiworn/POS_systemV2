<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'id' => 1,
                'company_name' => 'Coca-Cola Cambodia',
                'contact_name' => 'Mr. Dara',
                'phone' => '012345678',
                'email' => 'sales@cocacola.com',
                'address' => 'Phnom Penh, Cambodia',
                'created_at' => '2026-08-01 08:00:00',
                'updated_at' => '2026-08-01 08:00:00',
            ],
            [
                'id' => 2,
                'company_name' => 'Pepsi Cambodia',
                'contact_name' => 'Ms. Srey Mom',
                'phone' => '011223344',
                'email' => 'sales@pepsi.com',
                'address' => 'Phnom Penh, Cambodia',
                'created_at' => '2026-08-01 08:10:00',
                'updated_at' => '2026-08-01 08:10:00',
            ],
            [
                'id' => 3,
                'company_name' => 'Nestlé Cambodia',
                'contact_name' => 'Mr. Vanna',
                'phone' => '010998877',
                'email' => 'contact@nestle.com',
                'address' => 'Phnom Penh, Cambodia',
                'created_at' => '2026-08-01 08:20:00',
                'updated_at' => '2026-08-01 08:20:00',
            ],
            [
                'id' => 4,
                'company_name' => 'Unilever Cambodia',
                'contact_name' => 'Ms. Lina',
                'phone' => '015667788',
                'email' => 'info@unilever.com',
                'address' => 'Phnom Penh, Cambodia',
                'created_at' => '2026-08-01 08:30:00',
                'updated_at' => '2026-08-01 08:30:00',
            ],
            [
                'id' => 5,
                'company_name' => 'P&G Cambodia',
                'contact_name' => 'Mr. Piseth',
                'phone' => '017556677',
                'email' => 'support@pg.com',
                'address' => 'Phnom Penh, Cambodia',
                'created_at' => '2026-08-01 08:40:00',
                'updated_at' => '2026-08-01 08:40:00',
            ],
            [
                'id' => 6,
                'company_name' => 'Lucky Wholesale',
                'contact_name' => 'Mr. Chantha',
                'phone' => '092334455',
                'email' => 'lucky@wholesale.com',
                'address' => 'Battambang, Cambodia',
                'created_at' => '2026-08-01 08:50:00',
                'updated_at' => '2026-08-01 08:50:00',
            ],
            [
                'id' => 7,
                'company_name' => 'ABC Distribution',
                'contact_name' => 'Ms. Sophea',
                'phone' => '093778899',
                'email' => 'abc@distribution.com',
                'address' => 'Siem Reap, Cambodia',
                'created_at' => '2026-08-01 09:00:00',
                'updated_at' => '2026-08-01 09:00:00',
            ],
            [
                'id' => 8,
                'company_name' => 'Khmer Food Supply',
                'contact_name' => 'Mr. Rith',
                'phone' => '096123456',
                'email' => 'sales@khmerfood.com',
                'address' => 'Kampong Cham, Cambodia',
                'created_at' => '2026-08-01 09:10:00',
                'updated_at' => '2026-08-01 09:10:00',
            ],
            [
                'id' => 9,
                'company_name' => 'Green Market Supplier',
                'contact_name' => 'Ms. Nary',
                'phone' => '097654321',
                'email' => 'info@greenmarket.com',
                'address' => 'Takeo, Cambodia',
                'created_at' => '2026-08-01 09:20:00',
                'updated_at' => '2026-08-01 09:20:00',
            ],
            [
                'id' => 10,
                'company_name' => 'Fresh Daily Trading',
                'contact_name' => 'Mr. Sokha',
                'phone' => '088998877',
                'email' => 'contact@freshdaily.com',
                'address' => 'Kandal, Cambodia',
                'created_at' => '2026-08-01 09:30:00',
                'updated_at' => '2026-08-01 09:30:00',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['id' => $supplier['id']],
                $supplier
            );
        }
    }
}

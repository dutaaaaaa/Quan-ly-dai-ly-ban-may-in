<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Tài khoản
            [
                'name' => 'Quản Trị Viên',
                'password' => Hash::make('123456'), // Mật khẩu
                'role' => 1 // 1 là quyền Admin
            ]
        );
        // 1. TẠO LOẠI MÁY IN (Dùng updateOrCreate để chạy nhiều lần không bị trùng)
        $catLaser = Category::updateOrCreate(['name' => 'Máy in Laser'], ['description' => 'Tốc độ in nhanh, văn bản sắc nét, phù hợp dùng cho văn phòng.', 'status' => 1]);
        $catPhun = Category::updateOrCreate(['name' => 'Máy in Phun màu'], ['description' => 'Chuyên in ảnh, in đồ họa với màu sắc rực rỡ, độ phân giải cao.', 'status' => 1]);
        $catNhiet = Category::updateOrCreate(['name' => 'Máy in Nhiệt'], ['description' => 'Chuyên dùng để in bill siêu thị, in tem nhãn, mã vạch.', 'status' => 1]);
        $catKim = Category::updateOrCreate(['name' => 'Máy in Kim'], ['description' => 'Chuyên dùng để in hóa đơn tài chính, biểu mẫu nhiều liên.', 'status' => 1]);

        // 2. TẠO DANH SÁCH MÁY IN & PHIÊN BẢN
        $products = [
            [
                'category_id' => $catLaser->id,
                'name' => 'Canon LBP 2900',
                'brand' => 'Canon',
                'description' => 'Máy in quốc dân, siêu bền bỉ, dễ đổ mực.',
                'variants' => [
                    ['sku' => 'CANON-2900-TR', 'color' => 'Trắng', 'price' => 3500000, 'stock' => 25]
                ]
            ],
            [
                'category_id' => $catLaser->id,
                'name' => 'HP LaserJet Pro M15a',
                'brand' => 'HP',
                'description' => 'Thiết kế cực kỳ nhỏ gọn, phù hợp in gia đình.',
                'variants' => [
                    ['sku' => 'HP-M15A-TR', 'color' => 'Trắng', 'price' => 2100000, 'stock' => 10],
                    ['sku' => 'HP-M15A-DEN', 'color' => 'Đen', 'price' => 2150000, 'stock' => 5] // Bản màu đen đắt hơn xíu
                ]
            ],
            [
                'category_id' => $catPhun->id,
                'name' => 'Brother DCP-T720DW',
                'brand' => 'Brother',
                'description' => 'Máy in phun màu đa năng, có in 2 mặt tự động.',
                'variants' => [
                    ['sku' => 'BROTHER-T720-DEN', 'color' => 'Đen', 'price' => 5200000, 'stock' => 8]
                ]
            ],
            [
                'category_id' => $catNhiet->id,
                'name' => 'Xprinter XP-420B',
                'brand' => 'Khác',
                'description' => 'Máy in đơn hàng Shopee, Tiktok bách chiến bách thắng.',
                'variants' => [
                    ['sku' => 'XP-420B-TR', 'color' => 'Trắng', 'price' => 1350000, 'stock' => 50],
                    ['sku' => 'XP-420B-DEN', 'color' => 'Đen', 'price' => 1350000, 'stock' => 30]
                ]
            ],
            [
                'category_id' => $catKim->id,
                'name' => 'Epson LQ-310',
                'brand' => 'Khác',
                'description' => 'Chuyên in hóa đơn VAT, in biểu mẫu 3-4 liên.',
                'variants' => [
                    ['sku' => 'EPSON-LQ310', 'color' => 'Xám', 'price' => 4800000, 'stock' => 12]
                ]
            ]
        ];

        // Vòng lặp tự động nhét dữ liệu vào Database
        foreach ($products as $pData) {
            $variants = $pData['variants'];
            unset($pData['variants']); // Tách phiên bản ra để lưu riêng
            
            $product = Product::updateOrCreate(
                ['name' => $pData['name']], 
                $pData
            );

            // Xóa phiên bản cũ (nếu có) rồi nhét phiên bản mới vào
            $product->variants()->delete();
            foreach ($variants as $v) {
                $product->variants()->create($v);
            }
        }
    }
}
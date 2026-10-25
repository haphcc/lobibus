<?php
if (!function_exists('admin_label')) {
    function admin_label(?string $value): string
    {
        $labels = [
            'active' => 'Hoạt động',
            'inactive' => 'Ngừng hoạt động',
            'locked' => 'Đã khóa',
            'maintenance' => 'Bảo trì',
            'standard' => 'Thường',
            'sleeper' => 'Giường nằm',
            'limousine' => 'Limousine',
            'vip' => 'VIP',
            'scheduled' => 'Đã lên lịch',
            'running' => 'Đang chạy',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'expired' => 'Hết hạn',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền',
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản',
            'momo' => 'MoMo',
            'vnpay' => 'VNPAY',
            'admin' => 'Quản trị viên',
            'customer' => 'Khách hàng',
        ];

        return $labels[$value ?? ''] ?? (string) $value;
    }
}

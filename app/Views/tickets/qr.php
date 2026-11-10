<?php $ticket = $ticket ?? null; ?>
<section class="booking-page py-5">
    <div class="container">
        <div class="booking-panel mx-auto text-center" style="max-width: 560px;">
            <?php if (!$ticket): ?>
                <h1 class="h4">Không tìm thấy mã QR</h1>
                <p class="text-muted"><?= e($message ?? 'Vé không tồn tại.') ?></p>
                <a class="btn btn-success" href="<?= url('/booking/history') ?>">Về lịch sử đặt vé</a>
            <?php else: ?>
                <span class="section-kicker">QR Ticket</span>
                <h1 class="h4 mb-2"><?= e($ticket['ticket_code']) ?></h1>
                <p class="text-muted"><?= e($ticket['from_name']) ?> -> <?= e($ticket['to_name']) ?></p>
                <?php if (!empty($ticket['qr_code_path'])): ?>
                    <img class="ticket-qr-image large mb-3" src="<?= asset($ticket['qr_code_path']) ?>" alt="QR <?= e($ticket['ticket_code']) ?>">
                <?php endif; ?>
                <dl class="booking-info-list text-start">
                    <dt>Booking</dt>
                    <dd><?= e($ticket['booking_code']) ?></dd>
                    <dt>Khách hàng</dt>
                    <dd><?= e($ticket['customer_name']) ?></dd>
                    <dt>Khởi hành</dt>
                    <dd><?= e(date('H:i d/m/Y', strtotime((string) $ticket['departure_time']))) ?></dd>
                    <dt>Trạng thái</dt>
                    <dd><?= e($ticket['status']) ?> / <?= e($ticket['booking_status']) ?></dd>
                </dl>
                <a class="btn btn-outline-success w-100" href="<?= url('/booking/detail?id=' . (int) $ticket['booking_id']) ?>">Xem chi tiết booking</a>
            <?php endif; ?>
        </div>
    </div>
</section>

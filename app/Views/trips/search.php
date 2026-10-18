<?php $pageJs = ['trip-search.js', 'map.js']; ?>
<section class="container py-5">
    <h1>Tìm chuyến xe</h1>
    <form id="tripSearchForm" class="row g-3 my-4">
        <div class="col-md-3"><input name="from" class="form-control" placeholder="Điểm đi" value="<?= e($_GET['from'] ?? 'Hà Nội') ?>"></div>
        <div class="col-md-3"><input name="to" class="form-control" placeholder="Điểm đến" value="<?= e($_GET['to'] ?? 'Hải Phòng') ?>"></div>
        <div class="col-md-3"><input name="date" type="date" class="form-control" value="<?= e($_GET['date'] ?? '') ?>"></div>
        <div class="col-md-3"><button class="btn btn-success w-100">Tìm kiếm</button></div>
    </form>
    <div id="tripResults" class="row g-3"></div>
    <a href="/">Quay về trang chủ</a>
</section>

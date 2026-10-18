<section class="container py-5" style="max-width:560px;">
    <h1 class="mb-4">Đăng ký</h1>
    <form method="post" action="/register" class="card card-body shadow-sm">
        <label class="form-label" for="name">Họ tên</label>
        <input id="name" name="name" class="form-control mb-3" required>
        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control mb-3" required>
        <label class="form-label" for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" class="form-control mb-3" required>
        <button class="btn btn-success">Tạo tài khoản</button>
        <a href="/" class="mt-3">Quay về trang chủ</a>
    </form>
</section>

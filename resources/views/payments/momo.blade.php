<form action="{{ route('checkout.momo') }}" method="POST">
    @csrf
    <label for="amount">Số tiền</label>
    <input type="text" name="total" value="{{ $total }}" readonly>

    <!-- Nút xác nhận thanh toán -->
    <button type="submit">Thanh toán qua Momo</button>
</form>


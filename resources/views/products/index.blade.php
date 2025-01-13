@extends('layouts.app')

@section('content')
   <div class="container m-10">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-primary inline-flex place-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                </svg>
                <span>New Product</span>
            </a>
        @endif

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('products.index', ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">
                            Name
                            @if(request('sort') === 'name')
                                <span>{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span>▼</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('products.index', ['sort' => 'price', 'direction' => request('sort') === 'price' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">
                            Price
                            @if(request('sort') === 'price')
                                <span>{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span>▼</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('products.index', ['sort' => 'quantity_available', 'direction' => request('sort') === 'quantity_available' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">
                            Available Quantity
                            @if(request('sort') === 'quantity_available')
                                <span>{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span>▼</span>
                            @endif
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->quantity_available }}</td>
                        <td>
                            @if($product->quantity_available > 0)
                                <button class="btn btn-success btn-sm purchase-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#purchaseModal-{{ $product->id }}"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-quantity="{{ $product->quantity_available }}">
                                    Purchase
                                </button>
                            @else
                                <button class="btn btn-sm" disabled>
                                    <span class="text-muted">Sold Out</span>
                                </button>
                            @endif

                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->links() }}


        <!-- Purchase Modal -->
        @foreach($products as $product)
        <div class="modal fade" id="purchaseModal-{{ $product->id }}" aria-labelledby="purchaseModal-{{ $product->id }}" tabindex="-1"  aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Purchase {{ $product->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="purchaseForm-{{ $product->id }}" action="{{ route('products.purchase', ['product' => $product->id]) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="form-group mb-3">
                                <label for="product_name-{{ $product->id }}">Product Name</label>
                                <input type="text" class="form-control rounded" id="product_name-{{ $product->id }}" value="{{ $product->name }}" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="product_price-{{ $product->id }}">Price</label>
                                <input type="text" class="form-control rounded" id="product_price-{{ $product->id }}" value="${{ number_format($product->price, 2) }}" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="available_quantity-{{ $product->id }}">Available Quantity</label>
                                <input type="number" class="form-control rounded" id="available_quantity-{{ $product->id }}" value="{{ $product->quantity_available }}" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="purchase_quantity-{{ $product->id }}">Purchase Quantity<span class="text-danger">*</span></label>
                                <input type="number" class="form-control purchase-quantity rounded" 
                                        id="purchase_quantity-{{ $product->id }}" 
                                        name="purchase_quantity" 
                                        min="1"
                                        max="{{ $product->quantity_available }}"
                                        required>
                                <small id="quantityError-{{ $product->id }}" class="form-text text-danger" style="display: none;">
                                    Quantity exceeds available stock.
                                </small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="total_amount-{{ $product->id }}">Total Amount</label>
                                <input type="text" class="form-control rounded" id="total_amount-{{ $product->id }}" name="total_amount" readonly>
                            </div>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="purchaseButton-{{ $product->id }}">
                                Purchase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.modal').on('show.bs.modal', function() {
        const productId = $(this).attr('id').split('-')[1];
        // Set default quantity to 1
        $(`#purchase_quantity-${productId}`).val(1);
        
        // Set initial total amount same as price
        const price = $(`#product_price-${productId}`).val();
        $(`#total_amount-${productId}`).val(price);
    });
    // Handle purchase quantity changes
    $('.purchase-quantity').on('input', function() {
        const productId = $(this).attr('id').split('-')[1];
        const quantity = parseInt($(this).val()) || 0;
        const price = parseFloat($(`#product_price-${productId}`).val().replace('$', ''));
        const maxQuantity = parseInt($(`#available_quantity-${productId}`).val());
        
        // Calculate and update total amount
        const total = (quantity * price).toFixed(2);
        $(`#total_amount-${productId}`).val(`$${total}`);
        
        // Validate quantity
        if (quantity > maxQuantity) {
            $(`#quantityError-${productId}`).show();
            $(`#purchaseButton-${productId}`).prop('disabled', true);
        } else {
            $(`#quantityError-${productId}`).hide();
            $(`#purchaseButton-${productId}`).prop('disabled', false);
        }
    });

    // Reset form when modal is closed
    $('.modal').on('hidden.bs.modal', function() {
        const productId = $(this).attr('id').split('-')[1];

        $(`#purchaseForm-${productId}`)[0].reset();
        $(`#quantityError-${productId}`).hide();
        $(`#purchaseButton-${productId}`).prop('disabled', false);
    });
});
</script>
@endsection
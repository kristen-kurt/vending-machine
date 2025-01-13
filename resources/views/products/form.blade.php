@extends('layouts.app')

@section('content')
    <div class="container mt-10">
        <h1 class="font-bold text-lg">{{ isset($product) ? 'Edit Product' : 'Create New Product' }}</h1>

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Product Form -->
        <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" method="POST" class="mt-10">
            @csrf
            @if(isset($product))
                @method('PUT')
            @endif

            <!-- Product Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control rounded" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
            </div>

            <!-- Product Price -->
            <div class="mb-3">
                <label for="price" class="form-label">Product Price</label>
                <input type="number" class="form-control rounded" id="price" name="price" value="{{ old('price', $product->price ?? '') }}" step="0.01" required>
            </div>

            <!-- Product Quantity -->
            <div class="mb-3">
                <label for="quantity_available" class="form-label">Product Quantity</label>
                <input type="number" class="form-control rounded" id="quantity_available" name="quantity_available" value="{{ old('quantity_available', $product->quantity_available ?? '') }}" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Create' }}</button>
            <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back</button>
        </form>
    </div>
@endsection
{{-- resources/views/orders/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редактировать заявку')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-edit me-2"></i>Редактировать заявку #{{ $order->id }}
                        </h5>
                        
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
                        @csrf
                        @method('PUT')

                        <!-- Выбор склада -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="warehouse_id" class="form-label">Склад получения *</label>
                                <select class="form-select @error('warehouse_id') is-invalid @enderror" 
                                        id="warehouse_id" name="warehouse_id" required>
                                    <option value="">Выберите склад</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" 
                                            {{ (old('warehouse_id') ?? $order->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }} ({{ $warehouse->city }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Статус *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ (old('status') ?? $order->status) == 'pending' ? 'selected' : '' }}>Ожидает</option>
                                    <option value="confirmed" {{ (old('status') ?? $order->status) == 'confirmed' ? 'selected' : '' }}>Подтверждена</option>
                                    <option value="rejected" {{ (old('status') ?? $order->status) == 'rejected' ? 'selected' : '' }}>Отклонена</option>
                                    <option value="completed" {{ (old('status') ?? $order->status) == 'completed' ? 'selected' : '' }}>Выполнена</option>
                                    <option value="received" {{ (old('status') ?? $order->status) == 'received' ? 'selected' : '' }}>Получен</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Выбор клиента (только для администратора) -->
                        @if(auth()->user()->isAdmin())
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Клиент *</label>
                                <select class="form-select @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id" required>
                                    <option value="">Выберите клиента</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" 
                                            {{ (old('user_id') ?? $order->user_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->full_name }} ({{ $client->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Товары -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Товары в заявке</h6>
                                <button type="button" class="btn btn-sm btn-outline-gray" id="addProduct">
                                    <i class="fas fa-plus me-1"></i>Добавить товар
                                </button>
                            </div>

                            <div id="products-container">
                                @foreach(old('products', $order->products->toArray()) as $index => $product)
                                <div class="card-custom mb-3 product-item px-2" >
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-5">
                                                <label class="form-label">Товар *</label>
                                                <select class="form-select product-select" name="products[{{ $index }}][id]" required>
                                                    <option value="">Выберите товар</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}" 
                                                                data-quantity="{{ $prod->total_quantity }}"
                                                                data-category="{{ $prod->category->name }}"
                                                                data-manufacturer="{{ $prod->manufacturer->name }}"
                                                                {{ (isset($product['id']) ? $product['id'] : $product['pivot']['product_id']) == $prod->id ? 'selected' : '' }}>
                                                            {{ $prod->name }} (Доступно: {{ $prod->total_quantity }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Количество *</label>
                                                <input type="number" step="0.01" min="0.01" class="form-control quantity-input" 
                                                       name="products[{{ $index }}][quantity]" required 
                                                       value="{{ isset($product['pivot']) ? $product['pivot']['quantity'] : $product['quantity'] }}" 
                                                       placeholder="0.00" oninput="validateQuantity(this)">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Доступно</label>
                                                <div class="form-control-plaintext available-quantity text-center">—</div>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-danger w-100 remove-product">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="product-info mt-2 small text-muted" style="display: none;">
                                            <span class="category"></span> • <span class="manufacturer"></span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            @error('products')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Примечание -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Примечание</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Итоги -->
                        <div class="card-custom bg-light mb-4">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <h6 class="text-muted mb-1">Всего товаров</h6>
                                        <h4 class="fw-bold" id="total-products">0</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-muted mb-1">Общее количество</h6>
                                        <h4 class="fw-bold" id="total-quantity">0</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-muted mb-1">Уникальных позиций</h6>
                                        <h4 class="fw-bold" id="unique-products">0</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-gray">
                                <i class="fas fa-eye me-2"></i>Просмотр
                            </a>
                            <div>
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-gray me-2">
                                    <i class="fas fa-times me-2"></i>Отмена
                                </a>
                                <button type="submit" class="btn btn-gray">
                                    <i class="fas fa-save me-2"></i>Обновить заявку
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Шаблон для товара -->
<template id="product-template">
    <div class="card-custom mb-3 product-item">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <label class="form-label">Товар *</label>
                    <select class="form-select product-select" name="products[][id]" required>
                        <option value="">Выберите товар</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-quantity="{{ $product->total_quantity }}"
                                    data-category="{{ $product->category->name }}"
                                    data-manufacturer="{{ $product->manufacturer->name }}">
                                {{ $product->name }} (Доступно: {{ $product->total_quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Количество *</label>
                    <input type="number" step="0.01" min="0.01" class="form-control quantity-input" 
                           name="products[][quantity]" required placeholder="0.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Доступно</label>
                    <div class="form-control-plaintext available-quantity text-center">—</div>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger w-100 remove-product">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="product-info mt-2 small text-muted" style="display: none;">
                <span class="category"></span> • <span class="manufacturer"></span>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productsContainer = document.getElementById('products-container');
    const productTemplate = document.getElementById('product-template');
    const addProductBtn = document.getElementById('addProduct');
    let productIndex = {{ count(old('products', $order->products->toArray())) }};

    // Добавление товара
    addProductBtn.addEventListener('click', function() {
        const clone = productTemplate.content.cloneNode(true);
        const productItem = clone.querySelector('.product-item');
        
        // Обновляем имена полей с индексом
        const selects = productItem.querySelectorAll('select');
        const inputs = productItem.querySelectorAll('input');
        
        selects.forEach(select => {
            select.name = `products[${productIndex}][id]`;
        });
        
        inputs.forEach(input => {
            input.name = `products[${productIndex}][quantity]`;
        });

        productsContainer.appendChild(clone);
        productIndex++;
        updateTotals();
    });

    // Удаление товара
    productsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-product')) {
            e.target.closest('.product-item').remove();
            updateTotals();
        }
    });

    // Обновление информации о доступном количестве
    function initProductSelects() {
        productsContainer.querySelectorAll('.product-select').forEach(select => {
            const productItem = select.closest('.product-item');
            const selectedOption = select.options[select.selectedIndex];
            const availableQuantity = productItem.querySelector('.available-quantity');
            const productInfo = productItem.querySelector('.product-info');
            const category = productItem.querySelector('.category');
            const manufacturer = productItem.querySelector('.manufacturer');

            if (selectedOption.value) {
                availableQuantity.textContent = selectedOption.getAttribute('data-quantity');
                category.textContent = selectedOption.getAttribute('data-category');
                manufacturer.textContent = selectedOption.getAttribute('data-manufacturer');
                productInfo.style.display = 'block';
            } else {
                availableQuantity.textContent = '—';
                productInfo.style.display = 'none';
            }
        });
    }

    productsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const productItem = e.target.closest('.product-item');
            const selectedOption = e.target.options[e.target.selectedIndex];
            const availableQuantity = productItem.querySelector('.available-quantity');
            const productInfo = productItem.querySelector('.product-info');
            const category = productItem.querySelector('.category');
            const manufacturer = productItem.querySelector('.manufacturer');

            if (selectedOption.value) {
                availableQuantity.textContent = selectedOption.getAttribute('data-quantity');
                category.textContent = selectedOption.getAttribute('data-category');
                manufacturer.textContent = selectedOption.getAttribute('data-manufacturer');
                productInfo.style.display = 'block';
            } else {
                availableQuantity.textContent = '—';
                productInfo.style.display = 'none';
            }
            updateTotals();
        }
    });

    // Обновление количества
    productsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            updateTotals();
        }
    });

    // Обновление итогов
    function updateTotals() {
        const productItems = productsContainer.querySelectorAll('.product-item');
        const totalProducts = document.getElementById('total-products');
        const totalQuantity = document.getElementById('total-quantity');
        const uniqueProducts = document.getElementById('unique-products');

        let totalQty = 0;
        let uniqueProductsSet = new Set();

        productItems.forEach(item => {
            const quantityInput = item.querySelector('.quantity-input');
            const productSelect = item.querySelector('.product-select');
            
            if (quantityInput.value) {
                totalQty += parseFloat(quantityInput.value) || 0;
            }
            
            if (productSelect.value) {
                uniqueProductsSet.add(productSelect.value);
            }
        });

        totalProducts.textContent = productItems.length;
        totalQuantity.textContent = totalQty.toFixed(2);
        uniqueProducts.textContent = uniqueProductsSet.size;
    }

    // Инициализация при загрузке
    initProductSelects();
    updateTotals();

    // Функция проверки доступности количества
    function validateQuantity(input) {
        const productItem = input.closest('.product-item');
        const productSelect = productItem.querySelector('.product-select');
        const availableQuantity = parseInt(productItem.querySelector('.available-quantity').textContent) || 0;
        const enteredQuantity = parseFloat(input.value) || 0;
        
        if (enteredQuantity > availableQuantity) {
            input.classList.add('is-invalid');
            productItem.querySelector('.quantity-feedback')?.remove();
            
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback quantity-feedback';
            feedback.textContent = `Превышено доступное количество. Доступно: ${availableQuantity}`;
            input.parentNode.appendChild(feedback);
            
            return false;
        } else {
            input.classList.remove('is-invalid');
            productItem.querySelector('.quantity-feedback')?.remove();
            return true;
        }
    }

    // Обновляем обработчик ввода количества
    productsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            validateQuantity(e.target);
            updateTotals();
        }
    });

    // Обновляем обработчик изменения товара
    productsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            // ... существующий код ...
            
            // Валидируем количество после смены товара
            const quantityInput = productItem.querySelector('.quantity-input');
            if (quantityInput.value) {
                validateQuantity(quantityInput);
            }
        }
    });

    // Валидация формы перед отправкой
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        let isValid = true;
        const quantityInputs = this.querySelectorAll('.quantity-input');
        
        quantityInputs.forEach(input => {
            if (!validateQuantity(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Пожалуйста, исправьте ошибки в количестве товаров перед отправкой формы.');
        }
    });
});
</script>
@endpush
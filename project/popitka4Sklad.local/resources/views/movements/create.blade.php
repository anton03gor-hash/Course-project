{{-- resources/views/movements/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Новое перемещение')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-plus me-2"></i>Новое перемещение
                        </h5>
                        <a href="{{ route('movements.index') }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-arrow-left me-1"></i>Назад
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('movements.store') }}" method="POST" id="movementForm">
                        @csrf

                        <!-- Тип перемещения -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Тип перемещения *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required onchange="toggleOrderField()">
                                    <option value="">Выберите тип</option>
                                    <option value="between_warehouses" {{ old('type') == 'between_warehouses' ? 'selected' : '' }}>Между складами</option>
                                    <option value="for_order" {{ old('type') == 'for_order' ? 'selected' : '' }}>Для заявки</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6" id="orderField" style="display: none;">
                                <label for="order_id" class="form-label">Заявка *</label>
                                <select class="form-select @error('order_id') is-invalid @enderror" 
                                        id="order_id" name="order_id">
                                    <option value="">Выберите заявку</option>
                                    @foreach($orders as $order)
                                        <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                            Заявка #{{ $order->id }} - {{ $order->user->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Товар -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="product_id" class="form-label">Товар *</label>
                                <select class="form-select @error('product_id') is-invalid @enderror" 
                                        id="product_id" name="product_id" required onchange="updateWarehousesList()">
                                    <option value="">Выберите товар</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-quantity="{{ $product->stocks->sum('quantity') }}"
                                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (Общее количество: {{ $product->stocks->sum('quantity') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Склады -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="from_warehouse_id" class="form-label">Склад-источник *</label>
                                <select class="form-select @error('from_warehouse_id') is-invalid @enderror" 
                                        id="from_warehouse_id" name="from_warehouse_id" required onchange="updateAvailableQuantity()">
                                    <option value="">Сначала выберите товар</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" 
                                                data-quantity="0"
                                                style="display: none;"
                                                {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }} ({{ $warehouse->city }}) - <span class="available-qty">0</span> шт.
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <small>Будут показаны только склады, на которых есть выбранный товар</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="to_warehouse_id" class="form-label">Склад-назначения *</label>
                                <select class="form-select @error('to_warehouse_id') is-invalid @enderror" 
                                        id="to_warehouse_id" name="to_warehouse_id" required onchange="validateWarehouses()">
                                    <option value="">Выберите склад-назначения</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }} ({{ $warehouse->city }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Количество -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="quantity" class="form-label">Количество *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" value="{{ old('quantity') }}" required 
                                           placeholder="0.00" oninput="validateQuantity()">
                                    <span class="input-group-text">
                                        Доступно на выбранном складе: <span id="availableQuantity" class="fw-bold ms-1">—</span>
                                    </span>
                                </div>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="quantityFeedback" class="text-danger small mt-1" style="display: none;"></div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('movements.index') }}" class="btn btn-outline-gray">
                                <i class="fas fa-times me-2"></i>Отмена
                            </a>
                            <button type="submit" class="btn btn-gray" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Создать перемещение
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Данные о наличии товаров на складах
let warehouseStocks = @json($warehouseStocks ?? []);

function toggleOrderField() {
    const type = document.getElementById('type').value;
    const orderField = document.getElementById('orderField');
    const orderSelect = document.getElementById('order_id');
    
    if (type === 'for_order') {
        orderField.style.display = 'block';
        orderSelect.required = true;
    } else {
        orderField.style.display = 'none';
        orderSelect.required = false;
        orderSelect.value = '';
    }
}

// Обновление списка складов при выборе товара
function updateWarehousesList() {
    const productId = document.getElementById('product_id').value;
    const fromWarehouseSelect = document.getElementById('from_warehouse_id');
    
    if (!productId) {
        // Сбрасываем только склад-источник, если товар не выбран
        resetFromWarehouseSelect(fromWarehouseSelect);
        updateAvailableQuantity();
        return;
    }
    
    // Собираем склады, где есть этот товар
    const warehousesWithProduct = warehouseStocks.filter(stock => stock.product_id == productId);
    
    // Обновляем только склад-источник
    updateFromWarehouseSelect(warehousesWithProduct, fromWarehouseSelect);
    
    // Обновляем доступное количество
    updateAvailableQuantity();
}

// Обновление списка складов-источников
function updateFromWarehouseSelect(warehousesWithProduct, selectElement) {
    // Сохраняем выбранное значение
    const selectedValue = selectElement.value;
    
    // Сбрасываем опции только склада-источника
    resetFromWarehouseSelect(selectElement);
    
    // Добавляем склады с товаром
    if (warehousesWithProduct.length > 0) {
        const placeholderOption = selectElement.querySelector('option[value=""]');
        placeholderOption.textContent = "Выберите склад-источник";
        
        warehousesWithProduct.forEach(stock => {
            const option = selectElement.querySelector(`option[value="${stock.warehouse_id}"]`);
            if (option) {
                option.style.display = 'block';
                option.setAttribute('data-quantity', stock.quantity);
                // Обновляем текст опции
                option.textContent = `${stock.warehouse_name} - ${stock.quantity} шт.`;
                
                // Если это был ранее выбранный склад, сохраняем выбор
                if (stock.warehouse_id == selectedValue) {
                    option.selected = true;
                }
            }
        });
    } else {
        const placeholderOption = selectElement.querySelector('option[value=""]');
        placeholderOption.textContent = "Товар отсутствует на складах";
    }
}

// Сброс списка складов-источников (НЕ затрагивает склад-получатель)
function resetFromWarehouseSelect(selectElement) {
    const options = selectElement.querySelectorAll('option');
    options.forEach(option => {
        if (option.value !== '') {
            option.style.display = 'none';
            option.selected = false;
            // Восстанавливаем оригинальный текст
            const warehouseId = option.value;
            const warehouse = @json($warehouses->keyBy('id')->toArray())[warehouseId];
            if (warehouse) {
                option.textContent = `${warehouse.name} (${warehouse.city})`;
            }
        } else {
            option.textContent = "Сначала выберите товар";
        }
    });
    selectElement.value = '';
}

// Обновление доступного количества при выборе склада-источника
function updateAvailableQuantity() {
    const fromWarehouseSelect = document.getElementById('from_warehouse_id');
    const selectedOption = fromWarehouseSelect.options[fromWarehouseSelect.selectedIndex];
    const availableQuantity = document.getElementById('availableQuantity');
    
    if (selectedOption && selectedOption.value) {
        const availableQty = selectedOption.getAttribute('data-quantity') || 0;
        availableQuantity.textContent = availableQty;
    } else {
        availableQuantity.textContent = '—';
    }
    
    validateQuantity();
    validateWarehouses();
}

// Проверка складов (не должны совпадать)
function validateWarehouses() {
    const fromWarehouseId = document.getElementById('from_warehouse_id').value;
    const toWarehouseId = document.getElementById('to_warehouse_id').value;
    const submitBtn = document.getElementById('submitBtn');
    
    if (fromWarehouseId && toWarehouseId && fromWarehouseId === toWarehouseId) {
        submitBtn.disabled = true;
        showWarehouseError('Склад-источник и склад-назначения не могут совпадать');
        return false;
    } else {
        submitBtn.disabled = false;
        hideWarehouseError();
        return true;
    }
}

// Показать ошибку складов
function showWarehouseError(message) {
    const feedback = document.createElement('div');
    feedback.className = 'text-danger small mt-1';
    feedback.id = 'warehouseFeedback';
    feedback.textContent = message;
    
    const existingFeedback = document.getElementById('warehouseFeedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    document.getElementById('to_warehouse_id').parentNode.appendChild(feedback);
}

// Скрыть ошибку складов
function hideWarehouseError() {
    const existingFeedback = document.getElementById('warehouseFeedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
}

// Валидация количества
function validateQuantity() {
    const quantityInput = document.getElementById('quantity');
    const availableQuantity = document.getElementById('availableQuantity');
    const feedback = document.getElementById('quantityFeedback');
    const submitBtn = document.getElementById('submitBtn');
    const fromWarehouseId = document.getElementById('from_warehouse_id').value;
    
    // Если склад-источник не выбран, не проверяем количество
    if (!fromWarehouseId) {
        quantityInput.classList.remove('is-invalid');
        feedback.style.display = 'none';
        submitBtn.disabled = !validateWarehouses();
        return true;
    }
    
    const enteredQuantity = parseFloat(quantityInput.value) || 0;
    const availableQty = parseFloat(availableQuantity.textContent) || 0;
    
    if (enteredQuantity > availableQty) {
        quantityInput.classList.add('is-invalid');
        feedback.textContent = `Превышено доступное количество. Доступно: ${availableQty}`;
        feedback.style.display = 'block';
        submitBtn.disabled = true;
        return false;
    } else if (enteredQuantity <= 0) {
        quantityInput.classList.add('is-invalid');
        feedback.textContent = 'Количество должно быть больше 0';
        feedback.style.display = 'block';
        submitBtn.disabled = true;
        return false;
    } else {
        quantityInput.classList.remove('is-invalid');
        feedback.style.display = 'none';
        submitBtn.disabled = !validateWarehouses();
        return true;
    }
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    toggleOrderField();
    
    // Если товар уже выбран (при ошибке валидации), обновляем склады
    const productId = document.getElementById('product_id').value;
    if (productId) {
        updateWarehousesList();
    }
    
    // Если склад-источник уже выбран, обновляем доступное количество
    const fromWarehouseId = document.getElementById('from_warehouse_id').value;
    if (fromWarehouseId) {
        updateAvailableQuantity();
    }
    
    // Валидация формы перед отправкой
    document.getElementById('movementForm').addEventListener('submit', function(e) {
        if (!validateQuantity() || !validateWarehouses()) {
            e.preventDefault();
            alert('Пожалуйста, исправьте ошибки перед отправкой формы.');
        }
    });
    
    // Валидация при изменении склада-получателя
    document.getElementById('to_warehouse_id').addEventListener('change', function() {
        validateWarehouses();
    });
});

// AJAX для получения данных о наличии товара на складах (альтернативный вариант)
function loadWarehouseStocks(productId) {
    if (!productId) return;
    
    fetch(`/api/products/${productId}/stocks`)
        .then(response => response.json())
        .then(data => {
            warehouseStocks = data;
            updateWarehousesList();
        })
        .catch(error => {
            console.error('Error loading warehouse stocks:', error);
        });
}
</script>
@endpush

@push('styles')
<style>
.available-qty {
    font-weight: bold;
    color: #198754;
}
</style>
@endpush
{{-- resources/views/movements/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редактировать перемещение')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-edit me-2"></i>Редактировать перемещение #{{ $movement->id }}
                        </h5>
                        
                    </div>
                </div>
                <div class="card-body">
                    @if($movement->status === 'complete')
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Внимание! Это перемещение уже выполнено. Изменение данных может привести к расхождениям в учете.
                        </div>
                    @endif
                    
                    <form action="{{ route('movements.update', $movement) }}" method="POST" id="movementForm">
                        @csrf
                        @method('PUT')

                        <!-- Тип перемещения -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Тип перемещения *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required onchange="toggleOrderField()" 
                                        {{ $movement->status === 'complete' ? 'disabled' : '' }}>
                                    <option value="">Выберите тип</option>
                                    <option value="between_warehouses" {{ (old('type') ?? $movement->type) == 'between_warehouses' ? 'selected' : '' }}>Между складами</option>
                                    <option value="for_order" {{ (old('type') ?? $movement->type) == 'for_order' ? 'selected' : '' }}>Для заявки</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6" id="orderField" style="{{ (old('type') ?? $movement->type) == 'for_order' ? '' : 'display: none;' }}">
                                <label for="order_id" class="form-label">Заявка *</label>
                                <select class="form-select @error('order_id') is-invalid @enderror" 
                                        id="order_id" name="order_id" {{ $movement->status === 'complete' ? 'disabled' : '' }}>
                                    <option value="">Выберите заявку</option>
                                    @foreach($orders as $order)
                                        <option value="{{ $order->id }}" 
                                            {{ (old('order_id') ?? $movement->order_id) == $order->id ? 'selected' : '' }}>
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
                                        id="product_id" name="product_id" required onchange="updateWarehousesList()"
                                        {{ $movement->status === 'complete' ? 'disabled' : '' }}>
                                    <option value="">Выберите товар</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-quantity="{{ $product->total_quantity }}"
                                                {{ (old('product_id') ?? $movement->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (Общее количество: {{ $product->total_quantity }})
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
                                        id="from_warehouse_id" name="from_warehouse_id" required onchange="updateAvailableQuantity()"
                                        {{ $movement->status === 'complete' ? 'disabled' : '' }}>
                                    <option value="">Сначала выберите товар</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" 
                                                data-quantity="0"
                                                style="display: none;"
                                                {{ (old('from_warehouse_id') ?? $movement->from_warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }} ({{ $warehouse->city }})
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
                                        id="to_warehouse_id" name="to_warehouse_id" required onchange="validateWarehouses()"
                                        {{ $movement->status === 'complete' ? 'disabled' : '' }}>
                                    <option value="">Выберите склад-назначения</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" 
                                            {{ (old('to_warehouse_id') ?? $movement->to_warehouse_id) == $warehouse->id ? 'selected' : '' }}>
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
                                           id="quantity" name="quantity" value="{{ old('quantity', $movement->quantity) }}" required 
                                           placeholder="0.00" oninput="validateQuantity()"
                                           {{ $movement->status === 'complete' ? 'disabled' : '' }}>
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

                        <!-- Статус -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Статус *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="in_progress" {{ (old('status') ?? $movement->status) == 'in_progress' ? 'selected' : '' }}>В процессе</option>
                                    <option value="complete" {{ (old('status') ?? $movement->status) == 'complete' ? 'selected' : '' }}>Выполнено</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Скрытые поля для disabled элементов -->
                        @if($movement->status === 'complete')
                            <input type="hidden" name="type" value="{{ $movement->type }}">
                            <input type="hidden" name="product_id" value="{{ $movement->product_id }}">
                            <input type="hidden" name="from_warehouse_id" value="{{ $movement->from_warehouse_id }}">
                            <input type="hidden" name="to_warehouse_id" value="{{ $movement->to_warehouse_id }}">
                            <input type="hidden" name="quantity" value="{{ $movement->quantity }}">
                            @if($movement->order_id)
                                <input type="hidden" name="order_id" value="{{ $movement->order_id }}">
                            @endif
                        @endif

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('movements.show', $movement) }}" class="btn btn-outline-gray">
                                <i class="fas fa-eye me-2"></i>Просмотр
                            </a>
                            <div>
                                <a href="{{ route('movements.index') }}" class="btn btn-outline-gray me-2">
                                    <i class="fas fa-times me-2"></i>Отмена
                                </a>
                                <button type="submit" class="btn btn-gray" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Обновить перемещение
                                </button>
                            </div>
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
        resetFromWarehouseSelect(fromWarehouseSelect);
        updateAvailableQuantity();
        return;
    }
    
    const warehousesWithProduct = warehouseStocks.filter(stock => stock.product_id == productId);
    updateFromWarehouseSelect(warehousesWithProduct, fromWarehouseSelect);
    updateAvailableQuantity();
}

// Обновление списка складов-источников
function updateFromWarehouseSelect(warehousesWithProduct, selectElement) {
    const selectedValue = selectElement.value;
    const productId = document.getElementById('product_id').value;
    const currentMovementProductId = "{{ $movement->product_id }}";
    const currentMovementFromWarehouseId = "{{ $movement->from_warehouse_id }}";
    
    // Если редактируем существующее перемещение и товар не менялся,
    // показываем текущий склад даже если на нем сейчас нет товара
    if (productId == currentMovementProductId && warehousesWithProduct.length === 0) {
        const placeholderOption = selectElement.querySelector('option[value=""]');
        placeholderOption.textContent = "Товар отсутствует на складах";
        
        // Показываем текущий склад
        const currentOption = selectElement.querySelector(`option[value="${currentMovementFromWarehouseId}"]`);
        if (currentOption) {
            currentOption.style.display = 'block';
            currentOption.disabled = false;
            currentOption.textContent = "{{ $movement->fromWarehouse->name }} ({{ $movement->fromWarehouse->city }}) - 0 шт. (текущий)";
            currentOption.setAttribute('data-quantity', 0);
        }
        return;
    }
    
    resetFromWarehouseSelect(selectElement);
    
    if (warehousesWithProduct.length > 0) {
        const placeholderOption = selectElement.querySelector('option[value=""]');
        placeholderOption.textContent = "Выберите склад-источник";
        
        warehousesWithProduct.forEach(stock => {
            const option = selectElement.querySelector(`option[value="${stock.warehouse_id}"]`);
            if (option) {
                option.style.display = 'block';
                option.disabled = false;
                option.setAttribute('data-quantity', stock.quantity);
                option.textContent = `${stock.warehouse_name} - ${stock.quantity} шт.`;
                
                if (stock.warehouse_id == selectedValue) {
                    option.selected = true;
                }
            }
        });
        
        // Если текущий склад не в списке доступных, но мы редактируем существующее перемещение
        // и товар совпадает, добавляем его в список
        if (productId == currentMovementProductId) {
            const currentOption = selectElement.querySelector(`option[value="${currentMovementFromWarehouseId}"]`);
            if (currentOption && !currentOption.style.display || currentOption.style.display === 'none') {
                currentOption.style.display = 'block';
                currentOption.disabled = false;
                currentOption.textContent = "{{ $movement->fromWarehouse->name }} ({{ $movement->fromWarehouse->city }}) - 0 шт. (текущий)";
                currentOption.setAttribute('data-quantity', 0);
                
                // Если ничего не выбрано, выбираем текущий
                if (!selectedValue) {
                    currentOption.selected = true;
                }
            }
        }
    } else {
        const placeholderOption = selectElement.querySelector('option[value=""]');
        placeholderOption.textContent = "Товар отсутствует на складах";
    }
}

// Сброс списка складов-источников
function resetFromWarehouseSelect(selectElement) {
    const options = selectElement.querySelectorAll('option');
    options.forEach(option => {
        if (option.value !== '') {
            option.style.display = 'none';
            option.disabled = true;
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
    const movementStatus = "{{ $movement->status }}";
    const currentQuantity = parseFloat("{{ $movement->quantity }}");
    
    // Если перемещение уже выполнено, не проверяем доступность
    if (movementStatus === 'complete') {
        quantityInput.classList.remove('is-invalid');
        feedback.style.display = 'none';
        submitBtn.disabled = false;
        return true;
    }
    
    if (!fromWarehouseId) {
        quantityInput.classList.remove('is-invalid');
        feedback.style.display = 'none';
        submitBtn.disabled = !validateWarehouses();
        return true;
    }
    
    const enteredQuantity = parseFloat(quantityInput.value) || 0;
    const availableQty = parseFloat(availableQuantity.textContent) || 0;
    const currentFromWarehouseId = "{{ $movement->from_warehouse_id }}";
    
    // Учитываем текущее количество перемещения при проверке
    let adjustedAvailableQty = availableQty;
    if (fromWarehouseId == currentFromWarehouseId && movementStatus === 'in_progress') {
        adjustedAvailableQty += currentQuantity; // Добавляем обратно текущее количество перемещения
    }
    
    if (enteredQuantity > adjustedAvailableQty) {
        quantityInput.classList.add('is-invalid');
        feedback.textContent = `Превышено доступное количество. Доступно: ${adjustedAvailableQty}`;
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
    
    // Если товар уже выбран (при загрузке страницы), обновляем склады
    const productId = document.getElementById('product_id').value;
    if (productId) {
        // Даем время на загрузку данных
        setTimeout(() => {
            updateWarehousesList();
        }, 100);
    }
    
    // Если склад-источник уже выбран, обновляем доступное количество
    const fromWarehouseId = document.getElementById('from_warehouse_id').value;
    if (fromWarehouseId) {
        setTimeout(() => {
            updateAvailableQuantity();
        }, 150);
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
    
    // Автоматическое обновление складов при загрузке
    setTimeout(() => {
        updateWarehousesList();
    }, 200);
});
</script>
@endpush
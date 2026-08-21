@props(['name' => 'category', 'value' => null, 'label' => 'Customer Category', 'required' => false, 'class' => 'form-select'])

<label class="form-label">{{ $label }}</label>
<select name="{{ $name }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => $class, 'id' => $name]) }}>
    @foreach(\App\Models\Customer::CATEGORIES as $key => $optionLabel)
        <option value="{{ $key }}" @selected(($value ?? 'new') === $key)>{{ $optionLabel }}</option>
    @endforeach
</select>

@props(['name' => 'region', 'value' => null, 'label' => 'Area', 'required' => false, 'class' => 'form-select'])

<label class="form-label">{{ $label }}</label>
<select name="{{ $name }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => $class, 'id' => $name]) }}>
    <option value="">Select area</option>
    @foreach(\App\Models\Customer::AREAS as $area)
        <option value="{{ $area }}" @selected(($value ?? '') === $area)>{{ $area }}</option>
    @endforeach
</select>

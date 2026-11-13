@props(['name', 'label', 'autocomplete' => 'new-password', 'help' => null])
@php($id = 'account-'.$name)
<div class="editor-field">
    <label for="{{ $id }}">{{ $label }} <span class="required-mark">*</span></label>
    <div class="password-control">
        <input id="{{ $id }}" name="{{ $name }}" type="password" autocomplete="{{ $autocomplete }}" maxlength="255" @if($autocomplete === 'new-password') minlength="8" @endif required @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @else @if($help) aria-describedby="{{ $id }}-help" @endif @enderror>
        <button type="button" data-toggle-password="{{ $id }}" aria-label="Prikaži lozinku: {{ $label }}" aria-pressed="false"><x-icon name="eye" /></button>
    </div>
    @if($help)<p id="{{ $id }}-help" class="field-help">{{ $help }}</p>@endif
    @error($name)<span id="{{ $id }}-error" class="field-error">{{ $message }}</span>@enderror
</div>

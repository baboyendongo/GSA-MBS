@php
    $options = $responsables ?? [];
    $selected = (string) ($value ?? '');

    if ($selected !== '' && ! in_array($selected, $options, true)) {
        array_unshift($options, $selected);
    }
@endphp
<select id="{{ $id }}" name="responsable" @if (! empty($required)) required @endif>
    <option value="">Choisir un responsable</option>
    @foreach ($options as $option)
        <option value="{{ $option }}" @selected($selected === $option)>{{ $option }}</option>
    @endforeach
</select>

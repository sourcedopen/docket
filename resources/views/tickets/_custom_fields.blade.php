<div
    x-data="{
        schema: @js($schema['fields'] ?? (array_key_exists('fields', $schema ?? []) ? $schema['fields'] : (is_array($schema) && isset($schema[0]) ? $schema : []))),
        values: @js($values ?? [])
    }"
>
    <template x-for="field in schema" :key="field.key">
        <div class="form-control mb-4">
            <label class="label">
                <span class="label-text font-medium" x-text="field.label + (field.required ? ' *' : '')"></span>
            </label>

            {{-- Text input --}}
            <template x-if="field.type === 'text'">
                <input
                    type="text"
                    :name="'custom_fields[' + field.key + ']'"
                    :value="values[field.key] ?? ''"
                    :required="field.required ?? false"
                    class="input input-bordered"
                >
            </template>

            {{-- Number input --}}
            <template x-if="field.type === 'number'">
                <input
                    type="number"
                    :name="'custom_fields[' + field.key + ']'"
                    :value="values[field.key] ?? ''"
                    :required="field.required ?? false"
                    class="input input-bordered"
                >
            </template>

            {{-- Date input --}}
            <template x-if="field.type === 'date'">
                <input
                    type="date"
                    :name="'custom_fields[' + field.key + ']'"
                    :value="values[field.key] ?? ''"
                    :required="field.required ?? false"
                    class="input input-bordered"
                >
            </template>

            {{-- Textarea --}}
            <template x-if="field.type === 'textarea'">
                <textarea
                    :name="'custom_fields[' + field.key + ']'"
                    :required="field.required ?? false"
                    class="textarea textarea-bordered"
                    rows="3"
                    x-text="values[field.key] ?? ''"
                ></textarea>
            </template>

            {{-- Select --}}
            <template x-if="field.type === 'select'">
                <select
                    :name="'custom_fields[' + field.key + ']'"
                    :required="field.required ?? false"
                    class="select select-bordered"
                >
                    <option value="">Select an option</option>
                    <template x-for="option in (field.options ?? [])" :key="option">
                        <option :value="option" :selected="values[field.key] === option" x-text="option"></option>
                    </template>
                </select>
            </template>
        </div>
    </template>
</div>

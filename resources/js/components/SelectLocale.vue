<script
    setup
    lang="ts"
>
import {Label} from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const locales = {
    'ar-EG': 'Egyptian',
    'de': 'German',
    'en-EN': 'English',
    'es-ES': 'Spanish',
    'fr-FR': 'French',
};

let selectedItem = defineModel<String>();
</script>

<template>
    <Select
        id="selectLocale"
        v-model="selectedItem"
        class="grid gap-1.5"
    >
        <Label
            for="selectLocale"
            class="mb-1.5"
        >
            Select Locale
        </Label>
        <SelectTrigger>
            <SelectValue placeholder="Select Locale" />
        </SelectTrigger>
        <SelectContent>
            <SelectItem
                v-for="(countryName, locale) in locales"
                :key="`locale-select-${countryName}`"
                :value="locale"
            >
                <div>
                    {{ countryName }}
                </div>
            </SelectItem>
        </SelectContent>
    </Select>
</template>

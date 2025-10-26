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
import {CurrencyArrayItem} from '@/types';
import {
    computed,
    PropType
} from 'vue';

const props = defineProps(
    {
        label: {
            type: String,
            required: false,
            default: '',
        },
        items: {
            type: Object as PropType<CurrencyArrayItem>,
            required: true,
        },
        f: {
            type: String,
            required: false,
        }
    }
);

const idGenerator = computed(
    () => {
        return `currencySelectorFor-${props.label}`;
    }
);

let selectedItem = defineModel<String>();
</script>

<template>
    <Select
        :id="idGenerator"
        v-model="selectedItem"
        class="grid gap-1.5"
    >
        <Label
            :for="idGenerator"
            class="mb-1.5"
        >
            {{ label }}
        </Label>
        <SelectTrigger>
            <SelectValue placeholder="Select Currency" />
        </SelectTrigger>
        <SelectContent>
            <SelectItem
                v-for="item in items"
                :key="`currency-select-${item.numericCode}-${item.code}`"
                :value="item.code"
            >
                <div>
                    <b>{{ item.code }}</b>
                    <br />
                    {{ item.name }}
                </div>
            </SelectItem>
        </SelectContent>
    </Select>
</template>

<style scoped>

</style>

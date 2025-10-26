<script
    setup
    lang="ts"
>

import SelectCurrency from '@/components/SelectCurrency.vue';
import SelectLocale from '@/components/SelectLocale.vue';
import {Label} from '@/components/ui/label';
import {
    NumberField,
    NumberFieldContent,
    NumberFieldInput,
} from '@/components/ui/number-field';
import {
    CurrencyArrayItem,
    CurrencyRateResponse
} from '@/types';
import {
    router,
    useForm
} from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import {
    computed,
    PropType,
    ref,
} from 'vue';
import {toast} from 'vue-sonner';

const props = defineProps(
    {
        availableCurrencies: {
            type: Object as PropType<CurrencyArrayItem>,
            required: true
        },
        currencyRate: {
            type: Object as PropType<CurrencyRateResponse>,
            required: true
        },
    }
);


/**
 * TODO: Bug. This is never updated from the props on initial load.
 * Tried watcher, and different hooks (onMount, beforeMounted), none of them are working. The values are always `unknown`
 */
let form = useForm(
    {
        baseCurrency: props.currencyRate.baseCurrency?.code || 'EUR',
        quoteCurrency: props.currencyRate.quoteCurrency?.code || 'USD',
    }
);

const calculatedConvertedAmount = computed<number>(
    () => {
        return amount.value * props.currencyRate.quote;
    }
);

const baseCurrencyDisplayOptions = computed<Intl.NumberFormatOptions>(
    () => {
        return {
            style: 'currency',
            currency: form.baseCurrency,
            minimumFractionDigits: props.currencyRate.baseCurrency?.decimalDigits || 2,
            maximumFractionDigits: props.currencyRate.baseCurrency?.decimalDigits || 2,
        };
    }
);

const quoteCurrencyDisplayOptions = computed<Intl.NumberFormatOptions>(
    () => {
        return {
            style: 'currency',
            currency: form.quoteCurrency,
            minimumFractionDigits: props.currencyRate.quoteCurrency?.decimalDigits || 2,
            maximumFractionDigits: props.currencyRate.quoteCurrency?.decimalDigits || 2,
        };
    }
);

const formatedAmount = computed<string>(
    () => {
        return new Intl.NumberFormat(
            selectedLocale.value,
            quoteCurrencyDisplayOptions.value
        ).format(
            calculatedConvertedAmount.value
        );
    }
);

let selectedLocale = ref('en-EN');
let amount = ref(1);

const debounceForMilliseconds = 500;
const submit = debounce(
    () => {
        form.get(
            '/',
            {
                preserveState: true,
                except: ['availableCurrencies'],
                onError: (error): void => {
                    toast.error(error.message);

                    form.reset();

                    router.visit('/');
                },
            }
        );
    },
    debounceForMilliseconds
);

const foo = (value: string | number) => {
    console.dir(value);
};
</script>

<template>
    <h1>Hello World</h1>

    <div class="max-w-lg mx-auto mt-8">
        <div class="grid grid-cols-2 content-center gap-12">
            <div>
                <SelectCurrency
                    v-model="form.baseCurrency"
                    :items="availableCurrencies"
                    label="Base Currency"
                    @update:modelValue="submit"
                />
            </div>
            <div>
                <SelectCurrency
                    v-model="form.quoteCurrency"
                    :items="availableCurrencies"
                    label="Quoted Currency"
                    @update:modelValue="submit"
                />
            </div>
            <div>
                <NumberField
                    id="baseCurrency"
                    v-model="amount"
                    :defaultValue="amount"
                    :min="0"
                    :locale="selectedLocale"
                    :formatOptions="baseCurrencyDisplayOptions"
                >
                    <Label for="baseCurrency">
                        Base Currency
                    </Label>
                    <NumberFieldContent>
                        <NumberFieldInput />
                    </NumberFieldContent>
                </NumberField>
            </div>
            <div>
                <Label
                    for="quotedCurrency"
                    class="mb-5"
                >
                    Quoted Currency
                </Label>
                <Label id="quotedCurrency">
                    {{ formatedAmount }}
                </Label>
            </div>
            <div>
                <SelectLocale v-model="selectedLocale" />
            </div>
        </div>
    </div>
</template>

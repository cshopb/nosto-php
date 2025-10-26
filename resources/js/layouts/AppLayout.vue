<script
    setup
    lang="ts"
>
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type {BreadcrumbItemType} from '@/types';
import {Toaster} from '@/components/ui/sonner';
import 'vue-sonner/style.css'; // vue-sonner v2 requires this import

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto">
            <slot />
        </div>

        <Toaster richColors />
    </AppLayout>
</template>

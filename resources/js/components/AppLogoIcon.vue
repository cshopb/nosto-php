<script
    setup
    lang="ts"
>
import {BadgePoundSterling} from 'lucide-vue-next';
import type {HTMLAttributes} from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <BadgePoundSterling />
</template>

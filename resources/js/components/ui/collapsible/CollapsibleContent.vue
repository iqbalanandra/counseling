<script setup lang="ts">
import type { CollapsibleContentProps } from "reka-ui"
import { CollapsibleContent } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<CollapsibleContentProps & { class?: HTMLAttributes["class"] }>()
</script>

<template>
  <CollapsibleContent
    data-slot="collapsible-content"
    :class="cn('overflow-hidden data-[state=open]:animate-collapsible-down data-[state=closed]:animate-collapsible-up', props.class)"
    v-bind="props"
  >
    <slot />
  </CollapsibleContent>
</template>

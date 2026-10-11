<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()
</script>

<template>
  <main
    data-slot="sidebar-inset"
    :class="cn(
      'relative flex w-full flex-1 flex-col bg-[#FCFAF7] dark:bg-[#141816] transition-all',
      'md:peer-data-[variant=inset]:m-2.5 md:peer-data-[variant=inset]:rounded-3xl md:peer-data-[variant=inset]:border md:peer-data-[variant=inset]:border-[#EBE2D4] dark:md:peer-data-[variant=inset]:border-neutral-800 md:peer-data-[variant=inset]:shadow-xs overflow-hidden',
      props.class,
    )"
  >
    <slot />
  </main>
</template>

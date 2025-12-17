<script setup lang="ts">
import { ref, onMounted } from "vue";
import { loadGoogleMaps } from "@/services/googleMaps";

const mapEl = ref<HTMLDivElement | null>(null);

onMounted(async () => {
  const google = await loadGoogleMaps();

  new google.maps.Map(mapEl.value!, {
    center: { lat: 14.5995, lng: 120.9842 },
    zoom: 18,
    mapTypeId: "satellite",
  });
});
</script>

<template>
  <div ref="mapEl" class="w-full h-full rounded-lg"></div>
</template>

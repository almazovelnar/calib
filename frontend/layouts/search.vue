<template>
  <div class="layout-search" @click="bodyClick">
    <a class="skip-link" href="#main">Skip to main</a>
    <!-- eslint-disable vue/no-v-html -->
    <div
      v-if="this.$helpers.settings().banner_text"
      class="banner"
      :style="{
        height: this.$helpers.settings().banner_height + 'px',
        overflow: 'hidden',
      }"
      v-html="this.$helpers.settings().banner_text"
    />
    <!--eslint-enable-->
    <Header />
    <Nuxt id="main" :class="this.$route.name + '-page'" />
  </div>
</template>

<script>
export default {
  name: 'Search',

  methods: {
    bodyClick() {
      const wrapper = document.getElementsByClassName('search-wrapper')[0]

      wrapper.classList.remove('open')

      wrapper.querySelector('input').value = ''
    },
  },
}
</script>

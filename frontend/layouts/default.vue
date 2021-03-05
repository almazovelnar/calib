<template>
  <div class="layout-default" @click="bodyClick">
    <div id="imgwrapper" class="d-none" @click.stop="removeimage"></div>

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
    <Footer />

    <Bar
      v-if="enableBar"
      :enable-bar.sync="enableBar"
      :news.sync="newsBarBlocks"
    />
  </div>
</template>

<script>
export default {
  name: 'Default',

  data() {
    return {
      newsBarBlocks: [],
      enableBar: false,
    }
  },

  mounted() {
    this.$axios
      .get('posts/recent?bar=true&short=true&limit=20&page=1&exclude=false')
      .then((resp) => {
        this.newsBarBlocks = resp.data.body
        this.enableBar = true
      })
  },

  methods: {
    removeimage(event) {
      if (document.getElementById('imgwrapper').querySelector('img'))
        document.getElementById('imgwrapper').querySelector('img').remove()

      document.getElementById('imgwrapper').classList.add('d-none')
    },

    bodyClick() {
      const wrapper = document.getElementsByClassName('search-wrapper')[0]
      wrapper.classList.remove('open')
      wrapper.querySelector('input').value = ''
    },
  },
}
</script>

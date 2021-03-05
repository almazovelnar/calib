<template>
  <a
    :href="'/post/' + news.slug"
    class="news-block"
    :data-type="type"
    :target="blank ? '_blank' : ''"
    :class="{ wide: type === 'wide' }"
    :style="type === 'wide' ? 'background-image: url(' + news.image + ')' : ''"
  >
    <div v-if="type === 'wide'" class="backdrop"></div>
    <div v-if="type === 'image'" class="image">
      <img :src="news.image" :alt="news.name" />
    </div>
    <span
      v-if="pox.length > 0"
      class="tag"
      :class="hideDate ? 'd-none d-sm-flex' : 'd-flex'"
    >
      <div
        v-for="(pox1, index) in pox"
        :key="index"
        @click.stop="goToCat('/category/' + pox1.slug)"
      >
        {{ pox1.name }}
      </div>
    </span>
    <span class="title">{{ news.name }}</span>
    <span
      :class="hideDate ? 'd-none d-sm-flex' : 'd-flex'"
      class="justify-content-between"
    >
      <span class="date">{{ news.date }}</span>
      <span class="share">
        <span @click.stop="shareFacebook">
          <img
            :src="type === 'wide' ? '/content/fb-white.svg' : '/content/fb.svg'"
            alt="Share on Facebook"
          />
        </span>
        <span @click.stop="shareWhatsapp">
          <img
            :src="
              type === 'wide'
                ? '/content/whatsapp-white.svg'
                : '/content/whatsapp.svg'
            "
            alt="Share on WhatsApp"
          />
        </span>
        <span @click.stop="shareTelegram">
          <img
            :src="
              type === 'wide'
                ? '/content/telegram-white.svg'
                : '/content/telegram-gray.svg'
            "
            alt="Share on Telegram"
          />
        </span>
        <span @click.stop="shareTwitter">
          <img
            :src="
              type === 'wide'
                ? '/content/twitter-white.svg'
                : '/content/twitter-gray.svg'
            "
            alt="Share on Twitter"
          />
        </span>
      </span>
    </span>

    <span class="progress"></span>
  </a>
</template>

<script>
export default {
  name: 'Block',

  props: {
    type: {
      type: String,
      default: () => 'image',
    },

    progress: {
      type: Boolean,
      default: () => false,
    },

    news: {
      required: true,
      type: Object,
    },

    blank: {
      default: () => {
        return false
      },
      type: Boolean,
    },

    hideDate: {
      default: () => {
        return false
      },
      type: Boolean,
    },
  },

  data() {
    return {
      pox: [],
    }
  },

  mounted() {
    if (typeof this.news.ctgry !== typeof undefined) {
      this.pox = JSON.parse(JSON.stringify(this.news.ctgry))
    }
  },

  methods: {
    goToCat(to) {
      window.location = to
    },

    shareFacebook() {
      window.open(
        'https://www.facebook.com/sharer/sharer.php?u=' +
          encodeURIComponent(
            window.location.protocol +
              '//' +
              window.location.host +
              '/post/' +
              this.news.slug
          ),
        'facebook-share-dialog',
        'width=626,height=436'
      )
    },

    shareTelegram() {
      window.open(
        'https://t.me/share?url=' +
          encodeURIComponent(
            window.location.protocol +
              '//' +
              window.location.host +
              '/post/' +
              this.news.slug
          ),
        'telegram-share-dialog',
        'width=626,height=436'
      )
    },

    shareWhatsapp() {
      window.open(
        'whatsapp://send?text=' +
          encodeURIComponent(
            window.location.protocol +
              '//' +
              window.location.host +
              '/post/' +
              this.news.slug
          ),
        'whatsapp-share-dialog',
        'width=626,height=436'
      )
    },

    shareTwitter() {
      window.open(
        'https://twitter.com/intent/tweet?url=' +
          encodeURIComponent(
            window.location.protocol +
              '//' +
              window.location.host +
              '/post/' +
              this.news.slug
          ),
        'twitter-share-dialog',
        'width=626,height=436'
      )
    },
  },
}
</script>

<template>
  <div :class="name">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'XSlider',

  props: {
    edgePadding: {
      type: Number,
      default: () => 0,
    },

    name: {
      type: String,
      default: () => 'slider',
    },

    responsive: {
      type: Object || Boolean,
      default: () => false,
    },

    autoplay: {
      type: Boolean,
      default: () => false,
    },

    progress: {
      type: Boolean,
      default: () => false,
    },

    controls: {
      type: Boolean,
      default: () => false,
    },

    nav: {
      type: Boolean,
      default: () => false,
    },

    items: {
      type: Number || String,
      default: () => 2,
    },

    slideBy: {
      type: Number || String,
      default: () => 1,
    },

    gutter: {
      type: Number,
      default: () => 0,
    },

    navPosition: {
      type: String,
      default: 'bottom',
    },

    ncontainer: {
      type: String,
      default: () => 'false',
    },
  },

  data() {
    return {
      slider: null,
    }
  },

  mounted() {
    const settings = {
      container: '.' + this.name,
      items: this.items,
      edgePadding: this.edgePadding,
      slideBy: this.slideBy,
      navPosition: this.navPosition,
      navContainer: this.ncontainer
        ? document.querySelector(this.ncontainer)
        : 'false',
      navAsThumbnails: this.ncontainer !== 'false',
      gutter: this.gutter,
      autoplay: this.autoplay,
      mouseDrag: true,
      touch: true,
      preventScrollOnTouch: true,
      nav: this.nav,
      controls: this.controls,
      autoplayButtonOutput: false,
      responsive: this.responsive,
    }

    this.slider = this.$slider.run(settings)

    if (this.progress) {
      document
        .querySelectorAll('.' + this.name)[0]
        .querySelector('.tns-slide-active .progress')
        .classList.add('animate')

      this.slider.events.on('indexChanged', function (info) {
        document
          .getElementById(info.container.id)
          .querySelector(
            '.tns-item:nth-of-type(' +
              (parseInt(info.indexCached) + 1) +
              ') .progress'
          )
          .classList.remove('animate')

        document
          .getElementById(info.container.id)
          .querySelector(
            '.tns-item:nth-of-type(' +
              (parseInt(info.index) + 1) +
              ') .progress'
          )
          .classList.add('animate')
      })
    }
  },
}
</script>

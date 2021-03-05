export default ({ app, store }, inject) => {
  inject('helpers', {
    categories() {
      return store.getters['helpers/categories']
    },

    settings() {
      return store.getters['helpers/settings']
    },
  })
}

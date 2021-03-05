export const state = () => ({
  categories: {},
  settings: {},
})

export const mutations = {
  categories(state, payload) {
    state.categories = payload
  },
  settings(state, payload) {
    state.settings = payload
  },
}

export const getters = {
  categories(state) {
    return state.categories
  },

  settings(state) {
    return state.settings
  },
}

export default async function ({ store, app: { $axios } }) {
  await store.commit(
    'helpers/categories',
    (await $axios.get('categories')).data.body
  )

  await store.commit(
    'helpers/settings',
    (await $axios.get('settings')).data.body
  )
}

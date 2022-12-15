import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-permission-checkboxes', IndexField)
  app.component('detail-permission-checkboxes', DetailField)
  app.component('form-permission-checkboxes', FormField)
})

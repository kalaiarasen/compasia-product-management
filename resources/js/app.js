import { createApp } from 'vue'
import ProductMasterList from './ProductMasterList.vue'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const app = createApp(ProductMasterList)


app.use(Toast)

app.mount('#app')

<script>
import Layout from "@/Templates/Master"

export default {
   layout: Layout,
   metaInfo: { title: "Title" },

   components: {},

   data: () => ({
      form: {
         id: 1,
         name: "Joe Cianflone",
         email: ""
      }
   }),

   props: {},

   methods: {
      test() {
         this.$inertia.post("/", this.form).then(() => (this.sending = false))
      }
   }
}
</script>

<style lang="scss">
</style>

<template>
   <div></div>
</template>



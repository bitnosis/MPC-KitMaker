<template>
  <div>

   
        <div>
            <nav-bar v-if="this.$route.name !== 'welcome'"></nav-bar>
        </div>
        <VerifyNotice :id="id" v-if="(this.$route.name !== 'welcome') && id && !verified" />
        <!-- PAD EDIT SECTION -->



        <div>
          
          <router-view class="p-6 " v-slot="{ Component }">
            
              <component :is="Component" />
         
          </router-view>
        </div>
  </div>
</template>

<script>
import NavBar from './NavBar.vue'
import VerifyNotice from './VerifyNotice.vue'
export default {
  computed : {
    id () {
      return this.$store.getters.id 
    },
    verified () {
      return this.$store.getters.verified 
    }

  },
  components : {
    NavBar,
    VerifyNotice
  },
  
}
</script>


<style>
.sticky {position:fixed;}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

</style>

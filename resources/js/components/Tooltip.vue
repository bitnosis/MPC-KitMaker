<template >
 <div 
    class="absolute bg-teal-lightest border-t-4 border-teal rounded-b text-teal-darkest px-4 py-3 shadow-md" 
    style="height: auto; min-height: 200%; top: 105%; left: 0%; min-width:220px;" 
    role="alert">
      <div class="flex">
        <div class="py-1"><svg class="fill-current h-6 w-6 text-teal mr-4" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
        <div>
          <p class="font-bold">A tooltip!</p>
          <p class="text-sm">Built with Vue.js & Tailwind CSS.</p>
        </div>
      </div>
    </div>
</template>



<script>

export default {
    props : {
        show: false
    } , 
    
    
}
</script>
<template>
  <div class="max-w-screen-lg mx-auto text-gray-900">
    <div class="flex justify-center">
        <div class="flex-1">
            <div class="border w-auto">
                <div  class="border p-4 font-semibold ">Dashboard</div>

                <div class="p-4 bg-white">
                    

                  <div style="border-style:solid;" @dragover.prevent @drop.prevent>
    <button @click="exportXPM">
    Save Kit
    </button>

      <input type="text" v-model="Kit.name"/>
      <input type="file" multiple @change="uploadFile"/>
      <div @drop="dragFile" style="background-color:green;margin-bottom:10px;padding:10px;">
        Or drag the file here  {{Kit.name}}
        <div v-if="Files.length">
          <div v-for="file in Files" :key="file">
             {{file.name}}
             <button @click="playAudio(file)">
               Play
             </button>
             
             <audio v-bind:id="'index-'+file.padIndex" class='sample_spots' hidden v-show="Files != ''"/>

          </div>
        </div>
      </div>
    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>

export default {
    data() {
        return {
           Kit: {name:"This is the default kt Default"},
    	   Files:[] 
        }
    },
    methods: {
        stopSounds() {
            this.Files.forEach((value, index) => {
                var sound = this.$el.querySelector('#index-'+value.padIndex);
                sound.pause();
                sound.currentTime = 0;
                value.playing = false;
            });
        },
        playAudio(file){
            this.stopSounds();
            var sound = this.$el.querySelector('#index-'+file.padIndex);
            sound.play();
            file.playing = true;
        },
        loadPreviews () {
			this.Files.forEach((value, index) => {
                let audio = this.$el.querySelector('#index-'+value.padIndex)
                let reader = new FileReader();
                reader.readAsDataURL( value.file );
                reader.addEventListener('load', function(){
                 audio.src = reader.result;
                });
            });
        },
        uploadFile(e) {
            var that = this;
            for(var i = 0; i<e.target.files.length; i++){
               var d = {padIndex: i+1, name:e.target.files[i].name, playing:false, file: e.target.files[i]}
       		   this.Files.push(d);
            }
            this.$forceUpdate();
            setTimeout(function(){
       	        that.loadPreviews();
           }, 200)
        }
    }
}
</script>
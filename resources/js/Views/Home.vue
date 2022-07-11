<template>
<transition name="slide">
    <div v-if="editingPad!=null" class="w-60 bg-gray-800 h-full shadow-lg px-2 sticky pt-3" style='z-index:5;margin-top:-60px;'>
        <h3 class="font-bold text-2xl ml-2 mt-4 mb-2 text-gray-400">Edit Pad</h3>
        <ul class="relative">
            <li class="relative">
                <a class="flex items-center text-sm py-4 px-6 h-12 overflow-hidden text-gray-700 text-ellipsis whitespace-nowrap rounded hover:text-gray-900 hover:bg-gray-100 transition duration-300 ease-in-out" href="#!" data-mdb-ripple="true" data-mdb-ripple-color="dark">Sidenav link 1</a>
            </li>
            <li class="relative">
                <a class="flex items-center text-sm py-4 px-6 h-12 overflow-hidden text-gray-700 text-ellipsis whitespace-nowrap rounded hover:text-gray-900 hover:bg-gray-100 transition duration-300 ease-in-out" href="#!" data-mdb-ripple="true" data-mdb-ripple-color="dark">Sidenav link 2</a>
            </li>
            <li class="relative">
                <a class="flex items-center text-sm py-4 px-6 h-12 overflow-hidden text-gray-700 text-ellipsis whitespace-nowrap rounded hover:text-gray-900 hover:bg-gray-100 transition duration-300 ease-in-out" href="#!" data-mdb-ripple="true" data-mdb-ripple-color="dark">Sidenav link 2</a>
            </li>
        </ul>
    </div>
</transition>

<div class="max-w-screen-lg mx-auto text-gray-900">
    <div class="flex justify-center">
        <div class="flex-1 mt-4" @click="editingPad=null">
            <div class="border w-auto">
                <div class="border p-4 font-semibold ">MPC Kit Maker -{{dragging}} {{Kit.name}}</div>
                <div class="container w-full mx-auto pt-20">

                    <button @click="exportXPM">
                        Save Kit
                    </button>
                    <!-- KIT SETTINGS -->

                    <div class="flex flex-row flex-grow mb-8">
                        <div class='text-center'>
                            <p class="mt-4 text-gray-400">Send 1</p>

                            <ControlKnob id="send1" v-model="kitSettings.send1" :options="options" />
                        </div>
                        <div class='text-center'>
                            <p class="mt-4 text-gray-400">Send 2</p>

                            <ControlKnob id="send1" v-model="kitSettings.send2" :options="options" />
                        </div>

                        <div class='text-center'>
                            <p class="mt-4 text-gray-400">Send 3</p>

                            <ControlKnob id="send1" v-model="kitSettings.send3" :options="options" />
                        </div>

                        <div class='text-center'>
                            <p class="mt-4 text-gray-400">Send 4</p>

                            <ControlKnob id="send1" v-model="kitSettings.send4" :options="options" />
                        </div>

                    </div>

                    <!-- FILE INPUT / DROPZONE -->
                    <div class="flex flex-col flex-grow mb-3">
                        <div id="FileUpload" :class="['dropzone-area', dragging ? 'dropzone-over' : '']" @dragenter="dragging=true" @dragend="dragging=false" @drop="dragging=false" @dragleave="dragging=false" class="dropzone-area block w-full p-8 relative bg-white appearance-none border-2 pointer border-gray-300 border-solid rounded-md ">
                            <input type="file" multiple class="absolute inset-0 z-50 m-0 p-0 w-full h-full outline-none opacity-0" @change="uploadFile">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-cloud-upload-alt fa-3x text-currentColor"></i>
                                <h4 class='uppercase font-bold text-gray-500'>
                                    Drag files here to automatically load onto kit
                                </h4>
                                <p class="mt-4 text-gray-400">or click a pad below to load specifically onto that pad</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full flex flex-wrap mb-16 text-gray-800 leading-normal">

                        <div class='mb-6 mt-6 w-1/2 bank p-4' v-for="bank in Banks" :key="bank">
                            <h3 class="font-bold text-2xl uppercase  mb-1 text-gray-400">Bank {{bank}}</h3>
                            <div class='flex flex-wrap bg-gray-200 p-6 rounded-lg w-full bank shadow-lg reverseorder'>
                                <template v-for="file in Files.slice().reverse()" :key="file">
                                    <div v-if="bank==file.bank" @click.stop="playPad(file)" class="w-1/4">
                                        <p class="mb-1 mt-2 text-xs font-bold text-gray-500">{{file.bank+padNumber(file.padNumber)}}</p>
                                        <div class="flex flex-row ">
                                            
                                            <div :class='file.color' class="flex-1 glowing-border samplePad pushable pointer md:text-center shadow-md">
                                                <div class="flex-shrink front bg-gray-900 border-4 border-gray-400">
                                                    <input style="cursor:pointer!important;" hidden type="file" :id="'file-'+file.padIndex" @change="uploadSingleFile">
                                                    <button title="Click to load a file into pad" v-if="file.file===null" style='cursor:pointer!important;' class="rounded p-1 pointer bg-gray-600 ">
                                                        <label class='pointer' :for="'file-'+file.padIndex">
                                                            <svg class="h-6 w-6 pointer " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                                                <line x1="12" y1="11" x2="12" y2="17" />
                                                                <line x1="9" y1="14" x2="15" y2="14" /></svg>
                                                        </label>
                                                    </button>
                                                    <audio v-bind:ref="'padAudio-'+file.padIndex" class='sample_spots' hidden controls />
                                                    <button v-if="!file.playing && file.file!=null" class="rounded p-1 pointer bg-green-600 ">
                                                        <svg class="h-4 w-4 text-grey-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                            <polygon points="5 3 19 12 5 21 5 3" /></svg>
                                                    </button>

                                                    <button v-if="file.playing && file.file!=null" class="rounded p-1 bg-red-600">
                                                        <svg class="h-4 w-4 text-grey-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="6" y="4" width="4" height="16" />
                                                            <rect x="14" y="4" width="4" height="16" /></svg>
                                                    </button>

                                                    <h5 class=" mt-2 font-bold text-xs">
                                                        <span class=' text-gray-600' v-if="file.name==null">Empty </span><span class='text-gray-200' v-else>{{truncate(file.name)}}</span>
                                                    </h5>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </template>

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



            console.log('WEBMITI');
            
// Function triggered when WebMidi.js is ready
function onEnabled() {
    console.log(WebMidi);
    console.log('Enabling Web MIDI')
  // Display available MIDI input devices
  if (WebMidi.inputs.length < 1) {
    
  } else {
    console.log('Enabling Web MIDI')
    WebMidi.inputs.forEach((d, index) => {
      document.body.innerHTML+= `${index}: ${d.name} <br>`;
    });
  }

}

// Enable WebMidi.js and trigger the onEnabled() function when ready
WebMidi.enable().then(onEnabled).catch(err => alert(err));




import {
    xmlString_1,
    xmlString_2
} from "./xmlString.js"
import Tooltip from "../components/Tooltip"
import ControlKnob from '@slipmatio/control-knob'
export default {
    components: {
        Tooltip,
        ControlKnob
    },
    data() {
        return {
            options: {
                minValue: 0,
                maxValue: 127,
                imageSize: 60,
                showValue: true,
                tickLength: 30,
                tickOffset: -3,
                tickStroke: 2,
                rimStroke: 3,
                valueArchStroke: 5,
                bgRadius: 39,
                tabIndex: 1,
                rimClass: 'text-grey-600',
                bgClass: 'text-gray-200',
                tickClass: 'text-red-900',
            },
            kitSettings: {
                send1: 0,
                send2: 0,
                send3: 0,
                send4: 0
            },
            nextEmptySlot: 1,
            kitSize: 16,
            dragging: false,
            editingPad: null,
            clickedPad: null,
            Kit: {
                name: "NewKit"
            },
            Files: [],
            Banks: ["A", "B", "C", "D", "E", "F", "G", "H"]
        }
    },
    mounted() {
        this.clearKit();
    },
    computed: {
        emptySlot: function () {
            var pad = 1;
            this.Files.every((value, index) => {
                if (value.file === null) {
                    pad = value.padIndex;
                    return false;
                }
                return true;
            });
            return pad;
        }
    },
    methods: {

        // USEFUL METHODS
        truncate(input) {
            return input.length > 5 ? `${input.substring(0, 7)}..` : input;
        },
        padColor(color) {
            switch (color) {
                case 'red':
                    return 'padColor2'
                    break;
            }
        },

        // ACTUAL PROGRAM FUNCTIONS
        loadPreviews() {
            this.Files.forEach((value, index) => {
                let audio = this.$refs['padAudio-' + value.padIndex];
                let reader = new FileReader();
                reader.readAsDataURL(value.file);
                reader.addEventListener('load', function () {
                    audio.src = reader.result;
                });
            });
        },
        uploadSingleFile(e) {
            var that = this;
            var file = e.target.files[0];
            if (file != null) {
                var d = {
                    padIndex: this.clickedPad.padIndex,
                    name: file.name,
                    playing: false,
                    file,
                    bank: this.clickedPad.bank,
                    color: null
                }
                this.Files[this.clickedPad.padIndex - 1] = d;
            }

            this.$forceUpdate();
            setTimeout(function () {
                that.loadPreviews();
            }, 200)
        },
        padNumber(num){
            if(num<10) return "0"+num;
            return num;
        },
        uploadFile(e) {
            var that = this;
            var currentIndex = 0;
            var currentBank = "A";
            


            for (var i = 0; i < e.target.files.length; i++) {
var rand = Math.random();
var color = 'red';
if(rand>0.5){
color = 'blue';
}
                this.Files.every((value, index) => {
                    if (value.file === null) {

                        var d = {
                            padIndex: value.padIndex,
                            padNumber: value.padNumber,
                            name: e.target.files[i].name,
                            playing: false,
                            file: e.target.files[i],
                            bank: value.bank,
                            color: color
                        }
                        this.Files[index] = d;
                        return false;
                    }
                    return true;
                })

            }
            console.log(this.Files)
            this.$forceUpdate();
            setTimeout(function () {
                that.loadPreviews();
            }, 200)
        },
        clearKit() {
            var padIndex = 1;
            this.Banks.forEach((value, index) => {
                for (var i = 0; i < 16; i++) {
                    this.Files.push({
                        color: 'red',
                        name: null,
                        file: null,
                        playing: false,
                        padIndex: padIndex,
                        padNumber: i+1,
                        bank: value
                    });
                    padIndex++;
                }
            })
        },
        stopSounds() {
            this.Files.forEach((file, index) => {
                var sound = this.$refs['padAudio-' + file.padIndex];
                sound.pause();
                sound.currentTime = 0;
                file.playing = false;
            });
        },
        playPad(pad) {

            if (pad.file != null) {
                if (pad.playing == true) {
                    this.stopAudio(pad);

                } else {
                    this.currentlyPlayingPad = pad.padIndex;
                    this.playAudio(pad);

                }
            }
            this.clickedPad = pad;

            //this.editingPad = pad;
        },
        playAudio(file) {
            if (file.file != null) {
                this.stopSounds();
                var sound = this.$refs['padAudio-' + file.padIndex];
                sound.play();
                file.playing = true;
            }

        },
        stopAudio(file) {
            if (file.file != null) {
                this.stopSounds();
            }
            file.playing = false;
        },
        prettify(sourceXml) {
            var xmlDoc = new DOMParser().parseFromString(sourceXml, 'application/xml');
            var xsltDoc = new DOMParser().parseFromString([
                // describes how we want to modify the XML - indent everything
                '<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform">',
                '  <xsl:strip-space elements="*"/>',
                '  <xsl:template match="para[content-style][not(text())]">', // change to just text() to strip space in text nodes
                '    <xsl:value-of select="normalize-space(.)"/>',
                '  </xsl:template>',
                '  <xsl:template match="node()|@*">',
                '    <xsl:copy><xsl:apply-templates select="node()|@*"/></xsl:copy>',
                '  </xsl:template>',
                '  <xsl:output indent="yes"/>',
                '</xsl:stylesheet>',
            ].join('\n'), 'application/xml');
            var xsltProcessor = new XSLTProcessor();
            xsltProcessor.importStylesheet(xsltDoc);
            var resultDoc = xsltProcessor.transformToDocument(xmlDoc);
            var resultXml = new XMLSerializer().serializeToString(resultDoc);
            return resultXml;
        },
        exportXPM() {

            var MPCIndex = 1;
            var xmltext = xmlString_1 + xmlString_2;
            this.Files.forEach((value, index) => {
                //xmltext+=this.generateXPMInstrument(value.padIndex);
                MPCIndex++;
            });
            for (var i = MPCIndex; i < 128; i++) {
                MPCIndex = i;
                //xmltext+=this.generateXPMInstrument(MPCIndex);
            }
            var filename = this.Kit.name + ".xpm";
            var pom = document.createElement('a');
            var file = this.prettify(xmltext);
            var string = '<?xml version="1.0" encoding="UTF-8"?>\n\n';
            var bb = new Blob([string + file], {
                type: 'text/xml'
            });

            pom.setAttribute('href', window.URL.createObjectURL(bb));
            pom.setAttribute('download', filename);

            pom.dataset.downloadurl = ['text/xml', pom.download, pom.href].join(':');
            pom.draggable = true;
            pom.classList.add('dragout');

            pom.click();
        },

    }
}
</script>

<style scoped>
.pointer {
    cursor: pointer;
}

.slide-leave-active,
.slide-enter-active {
    transition: 1s;
}

.slide-enter {
    transform: translate(100%, 0);
}

.slide-leave-to {
    transform: translate(-100%, 0);
}

.pushable {
    border-radius: 4px;
    padding: 0;
    cursor: pointer;
    outline-offset: 6px;
}

.front {
    display: block;
    padding: 16px 22px;
    border-radius: 4px;
    font-size: 1.0rem;
    transform: translateY(-6px);
}

.sticky {
    position: fixed;
}

.padColor1 {
    background: #4b4b4b;
}

.padColor2 {
    background: #5f0000;
}

.pushable:active .front {
    transform: translateY(-2px);
}

#FileUpload.active {
    box-shadow: 0 0 0 3px rgba(164, 202, 254, 0.45);
    border-color: #a4cafe;
}

.dropzone-over {
    box-shadow: 0 0 0 5px rgba(164, 202, 254, 0.45);
    border-color: #a4cafe;
    border: 2px dashed #2E94C4;

}

.dropzone-area {
    border: 2px dashed #CBCBCB;
}

.dropzone-area:hover {
    border: 2px dashed #2E94C4;
}

.samplePad {
    height: 87px;
}

.reverseorder {
    flex-direction: row-reverse;
}

.glowing-border {
    border: 2px solid #787878;
    border-radius: 8px;
    
}
.glowing-border.red {
    border-color: #ff1a1a;
    box-shadow: 0 2px 8px #ff1616;
}

.glowing-border.blue {
    border-color: #1a98ff;
    box-shadow: 0 0px 8px #1696ff;
}

</style>

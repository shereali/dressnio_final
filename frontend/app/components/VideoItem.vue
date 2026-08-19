<!-- VideoCarousel.vue -->
<template>
  <div class="flex items-center justify-center">
    <div class="relative w-full sm:w-96 overflow-hidden">
      <div
        class="flex transition-transform ease-in-out duration-300"
        :style="{
          transform: `scale(${isVideoPlaying ? 1.1 : 1}) translateX(-${
            currentIndex * (100 / videos.length)
          }%)`,
        }"
      >
        <div
          v-for="(video, index) in videos"
          :key="index"
          class="carousel-item relative"
        >
          <video
            ref="videoPlayer"
            :src="video.src"
            class="w-full h-full object-cover rounded"
            @ended="playNextVideo"
          ></video>
          <button
            v-if="!isVideoPlaying"
            class="play-button"
            @click="playVideo(index)"
          >
            <i class="fas fa-play"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      videos: [
        { src: "videos/v-1.mp4" },
        { src: "videos/v-2.mp4" },
        { src: "videos/v-3.mp4" },
        { src: "videos/v-1.mp4" },
        { src: "videos/v-2.mp4" },
        { src: "videos/v-3.mp4" },
        // Add more video objects as needed
      ],
      currentIndex: 0,
      isVideoPlaying: false,
    };
  },
  methods: {
    playNextVideo() {
      if (this.currentIndex < this.videos.length - 1) {
        this.currentIndex++;
      } else {
        this.currentIndex = 0;
      }
      this.$refs.videoPlayer.load();
      if (this.isVideoPlaying) {
        // Delay the play action to ensure the video is loaded
        setTimeout(() => {
          this.$refs.videoPlayer.play();
        }, 100);
      }
    },
    playVideo(index) {
      this.currentIndex = index;
      this.isVideoPlaying = true;
      this.$refs.videoPlayer.load();
      // Delay the play action to ensure the video is loaded
      setTimeout(() => {
        this.$refs.videoPlayer.play();
      }, 100);
    },
  },
};
</script>

<style scoped>
/* Add additional styling as needed */
.carousel-item {
  position: relative;
  flex-shrink: 0;
  width: 100%; /* Full width */
  overflow: hidden;
}

.play-button {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: rgba(0, 0, 0, 0.7);
  color: #fff;
  border: none;
  padding: 10px;
  cursor: pointer;
}
</style>

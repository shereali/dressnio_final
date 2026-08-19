export default function (message: string) {
  function getVoices() {
    let voices = speechSynthesis.getVoices();
    if (!voices.length) {
      // some time the voice will not be initialized so we can call speak with empty string
      // this will initialize the voices
      let utterance = new SpeechSynthesisUtterance("");
      speechSynthesis.speak(utterance);
      voices = speechSynthesis.getVoices();
    }
    return voices;
  }

  function speak(
    text: string,
    voice: any,
    rate: number,
    pitch: number,
    volume: number
  ) {
    // create a SpeechSynthesisUtterance to configure the how text to be spoken
    let speakData = new SpeechSynthesisUtterance();
    speakData.volume = volume; // From 0 to 1
    speakData.rate = rate; // From 0.1 to 10
    speakData.pitch = pitch; // From 0 to 2
    speakData.text = text;
    speakData.lang = "en";
    speakData.voice = voice;

    // pass the SpeechSynthesisUtterance to speechSynthesis.speak to start speaking
    speechSynthesis.speak(speakData);
  }

  if ("speechSynthesis" in window) {
    let voices = getVoices();
    let rate = 0.5,
      pitch = 2,
      volume = 1;
    // let text = message;

    speak(message, voices[2], rate, pitch, volume);

    // setTimeout(() => {
    //   // speak after 2 seconds
    //   rate = 0.5;
    //   (pitch = 2), (volume = 1);
    //   //   text = message;
    //   speak(message, voices[2], rate, pitch, volume);
    // }, 1000);
  } else {
    console.log(" Speech Synthesis Not Supported 😞");
  }
}

<template>
  <div class="flex flex-col lg:flex-row gap-16" id="about" ref="about">
    <div class="flex flex-col xl:flex-row align-middle max-w-fit">
      <div class="m-4 font-coolvetica text-xl">
        <h1 class="font-coolvetica text-4xl my-2 text-left">Other Stuff</h1>
        <p>Any questions? Ask me in my discord channel.</p>
        <br />
      </div>
    </div>
    <div class="flex flex-col xl:flex-row align-middle max-w-fit"></div>
  </div>
  <div class="container flex flex-col lg:flex-row justify-between items-center">
    <!-- Links Section -->
    <div class="mb-4 lg:mb-0">
      <p class="mb-2">
        Made by
        <a
          href="https://www.youtube.com/FlinkTM"
          target="_blank"
          class="underline text-blue-500 hover:text-blue-700"
          >FlinkTM</a
        >
        and
        <a
          href="https://github.com/GreepTheSheep/"
          target="_blank"
          class="underline text-blue-500 hover:text-blue-700"
          >Greep</a
        >
      </p>
      <p class="mb-2">
        <a href="https://flinkblog.de/impressum" class="underline text-blue-500 hover:text-blue-700"
          >Legalities</a
        >
      </p>
      <p>
        If you like the RMC, consider
        <a href="https://www.twitch.tv/flinktm" class="underline text-blue-500 hover:text-blue-700"
          >dropping a sub!</a
        >
      </p>
    </div>
    <!-- Additional Info Section -->
    <div class="mb-4 lg:mb-0">
      <p>
        Contact me on:
        <a
          href="https://youtube.com/FlinkTM"
          target="_blank"
          class="text-blue-500 hover:text-blue-700"
          >Youtube</a
        >
      </p>
    </div>
  </div>
  <div class>
    <a href="https://youtube.com/FlinkTM" target="_blank">
      <img src="@/assets/img/Flink LI Logo_transparent.png" width="128" height="128" />
    </a>
  </div>
</template>

<style scoped>
footer {
  background-color: #2d3748; /* Dark background color */
  color: white;
  padding: 1.5rem;
  margin-top: 4rem;
}

footer a {
  text-decoration: none;
  color: #3182ce;
  transition: color 0.3s;
}

footer a:hover {
  color: #2b6cb0;
}
</style>

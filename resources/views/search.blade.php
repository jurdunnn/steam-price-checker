<x-app-layout>
    <div
        x-data="data"
        class="relative min-h-screen bg-gray-900 bg-center sm:flex sm:justify-center selection:bg-blue-500 selection:text-white"
    >
        <div class="absolute top-0 w-full h-[.2vh]">
            <div class="loading h-full bg-blue-600 w-[0%]">
            </div>
        </div>

        <div class="w-2/4 p-6 mx-auto mt-24 lg:p-8">
            <!-- Logo -->
            <div class="flex justify-center">
                <x-logo />
            </div>

            <!-- Search -->
            <div class="relative">
                <div class="relative flex flex-row w-full text-white bg-gray-700 border-gray-500 rounded-lg border-1">
                    <input
                        type="text"
                        class="w-full py-4 text-xl bg-gray-700 border-none rounded-lg border-l-1"
                        x-model.debounce.500ms="search"
                        />
                </div>

                <div x-show="showResults == true" class="absolute h-full min-w-full bottom-18">
                    <ul id="results" class="min-w-full flex flex-col gap-y-2 m-h-[4rem] mt-2 rounded-lg py-1 bg-gray-700 text-white">
                        <template x-for="game in games" :key="game.steam_app_id">
                            <li class="flex flex-row flex-grow px-4 overflow-hidden max-h-28" :id="`game${game.id}`" style="opacity: 0">
                                <img x-show="game.image != null" x-bind:src="game.image.image_url" class="object-contain w-48" />

                                <div class="flex flex-col justify-between py-2 ml-4">
                                    <p x-show="game.title != null" class="font-semibold" x-text="game.title"></p>

                                    <!--
                                        Unfortunately an alpinejs bug occurred here, preventing a loop from being possible.

                                        Though annoying, it's not massively a problem, as the output below would have needed
                                        to be limited to ~3 modifiers anyway.
                                    -->
                                    <div x-show="game.modifiers.length > 0" class="flex flex-row max-w-full overflow-hidden text-sm gap-x-1">
                                        <template x-if="game.modifiers.length == 1">
                                            <p
                                                class="px-2 py-1 rounded-xl"
                                                :class="`bg-${game.modifiers[0].color}-600`"
                                                x-text="game.modifiers[0].title"
                                            ></p>
                                        </template>

                                        <template x-if="game.modifiers.length == 2">
                                            <p
                                                class="px-2 py-1 rounded-xl"
                                                :class="`bg-${game.modifiers[1].color}-600`"
                                                x-text="game.modifiers[1].title"
                                            ></p>
                                        </template>

                                        <template x-if="game.modifiers.length == 3">
                                            <p
                                                x-show="game.modifiers == 3"
                                                class="px-2 py-1 rounded-xl"
                                                :class="`bg-${game.modifiers[2].color}-600`"
                                                x-text="game.modifiers[2].title"
                                            ></p>
                                        </template>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sidebar show button -->
        <div x-on:click="showSettings" class="absolute cursor-pointer flex items-center group right-0 top-0 w-12 h-[100vh] hover:bg-gradient-to-r hover:from-gray-900 hover:to-gray-600 duration-300 ease-in-out opacity-25 hover:opacity-75">
            <span class="mx-auto text-white group-hover:scale-110">
                <i class="fa-solid fa-chevron-left fa-xl"></i>
            </span>
        </div>

        <!-- Settings Sidebar -->
        <div x-show="showSidebar" id="sidebar" class="absolute shadow-xl right-0 flex flex-col gap-y-12 top-0 w-0 h-[100vh] bg-gray-700 text-white">
            <button x-on:click="hideSettings" class="p-2 mr-auto hover:scale-105">
                <i class="fa-solid fa-square-xmark fa-2xl"></i>
            </button>

            <h1 class="mx-auto -my-6 text-2xl font-bold">Settings</h1>

            <div class="flex flex-col gap-y-6">
                <div class="flex flex-col px-6 mr-auto gap-y-1">
                    <h2 class="text-xl font-bold">Downloadable Content (DLC)</h2>
                    <div class="flex gap-x-4">
                        <button class="px-4 py-2 text-lg font-semibold bg-green-400 rounded-lg" x-on:click="settings.dlc = true" :class="settings.dlc ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Show</button>
                        <button class="px-4 py-2 text-lg font-semibold bg-red-400 rounded-lg" x-on:click="settings.dlc = false" :class="!settings.dlc ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Hide</button>
                    </div>
                </div>

                <div class="flex flex-col px-6 mr-auto">
                    <h2 class="text-xl font-bold">Videos and Trailers</h2>
                    <div class="flex gap-x-4">
                        <button class="px-4 py-2 text-lg font-semibold bg-green-400 rounded-lg" x-on:click="settings.video = true" :class="settings.video ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Show</button>
                        <button class="px-4 py-2 text-lg font-semibold bg-red-400 rounded-lg duration-150 ease-in hover:scale-105" x-on:click="settings.video = false" :class="!settings.video ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Hide</button>
                    </div>
                </div>

                <div class="flex flex-col px-6 mr-auto">
                    <h2 class="text-xl font-bold">Unreleased Games</h2>
                    <div class="flex gap-x-4">
                        <button class="px-4 py-2 text-lg font-semibold bg-green-400 rounded-lg" x-on:click="settings.unreleased = true" :class="settings.unreleased ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Show</button>
                        <button class="px-4 py-2 text-lg font-semibold bg-red-400 rounded-lg" x-on:click="settings.unreleased = false" :class="!settings.unreleased ? 'duration-150 ease-in hover:scale-105' : 'opacity-25' ">Hide</button>
                    </div>
                </div>

                <div class="flex flex-col px-6 mr-auto">
                    <h2 class="text-xl font-bold">Free Games</h2>
                    <div class="flex gap-x-4">
                        <button class="px-4 py-2 text-lg font-semibold bg-green-400 rounded-lg" x-on:click="settings.free = true" :class="settings.free ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Show</button>
                        <button class="px-4 py-2 text-lg font-semibold bg-red-400 rounded-lg" x-on:click="settings.free = false" :class="!settings.free ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Hide</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('data', () => ({
                search: '',
                loadProgress: 0,
                showSidebar: false,
                settings: {
                    dlc: true,
                    video: true,
                    unreleased: true,
                    free: true,
                },
                showResults: false,
                games: [],

                init() {
                    this.$watch('search', () => {
                        clearTimeout(this.timer);
                        this.timer = setTimeout(() => {
                            this.getGames();

                            // Show results and animate
                            this.showResults = true;

                            if (!this.games.length > 0) {
                                gsap.fromTo("#results", { height: "0vh" }, {
                                    height: "40vh",
                                    duration: 0.3,
                                });
                            }

                            // If search is empty, animate hiding results.
                            if (this.search == '') {
                                gsap.fromTo("#results", { height: "40vh" }, {
                                    height: "0vh",
                                    duration: 0.3,
                                }).then(() => {
                                    this.showResults = false;
                                });
                            }
                        }, 500);
                    });

                    this.$watch('loadProgress', (value, old) => {
                        gsap.fromTo(".loading", { width: `${old}%` }, {
                            width: `${value}%`,
                            duration: 1,
                        });

                        // Reset loadProgress to 0 after 2 seconds of being 100
                        if (value === 100) {
                            clearTimeout(this.loadingTimeout);

                            this.loadingTimeout = setTimeout(() => {
                                gsap.fromTo(".loading", { opacity: 0 }, {
                                    opacity: 1,
                                    duration: 0.4,
                                }).then(() => {
                                    this.loadProgress = 0;
                                });
                            }, 2000);
                        }
                    });
                },

                showSettings() {
                    this.showSidebar = true;

                    gsap.fromTo("#sidebar", { width: "0%" }, {
                        width: "20%",
                        duration: 1,
                        ease: "elastic.out(1, 0.6)"
                    });
                },

                hideSettings() {
                    gsap.fromTo("#sidebar", { width: "20%" }, {
                        width: "0%",
                        duration: .25,
                    }).then(() => {
                        this.showSidebar = false;
                    });
                },

                async getGames() {
                    if (this.search.length > 0) {
                        this.loadProgress = 0;

                        this.games = [];

                        try {
                            const response = await fetch(`/api/steam/search/${this.search}`);
                            const games = await response.json();

                            var gamesLength = games.length;

                            // Add new games to games array (up to a maximum length of 5)
                            for (const game of games) {
                                const optionsString = JSON.stringify(this.settings);

                                const response = await fetch(`/api/steam/search/get/${game.id}?options=${encodeURIComponent(optionsString)}`);

                                const item = await response.json();

                                this.loadProgress += 100 / gamesLength;

                                if (item.errors) {
                                    continue;
                                }

                                this.games.push(item);

                                // Animate element
                                Alpine.nextTick(() => {
                                    gsap.fromTo(`#game${item.id}`, { opacity: 0 }, {
                                        duration: 0.5,
                                        ease: "sine.in",
                                        opacity: 1,
                                    });
                                });
                            }

                            this.loadProgress = 100;
                        } catch (error) {
                            console.error(error);
                            return [];
                        }
                    } else {
                        this.games = [];
                    }
                }
            }));
        })

    </script>
</x-app-layout>

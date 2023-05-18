<x-app-layout>
    <div
        x-data="data"
        class="relative min-h-screen overflow-hidden bg-center sm:flex sm:justify-center selection:bg-blue-500 selection:text-white"
    >
        <div class="absolute top-0 w-full h-[.2vh]">
            <div class="loading h-full bg-blue-600 w-[0%]">
            </div>
        </div>

        <div class="flex flex-col w-full mx-auto sm:p-6 gap-y-12 lg:p-8">
            <div id="search-wrapper" class="mx-auto sm:w-1/2">
                <div class="flex justify-center">
                    <x-logo />
                </div>

                <div x-on:mouseover="showIntro = false" class="relative flex flex-row w-full text-white border-gray-500 rounded-lg border-1">
                    <input
                        type="text"
                        class="w-full py-4 text-xl border border-gray-800 rounded-lg shadow-xl bg-slate-700 border-l-1"
                        x-model.debounce.500ms="search"
                        />
                    <p id="introduction-text" x-show="showIntro" class="absolute left-4 text-xl top-[50%] -translate-y-[50%]"></p>
                </div>
            </div>

            <div class="min-w-full px-2 sm:px-8">
                <ul id="results" class="min-w-full min-h-full py-1 mt-2 text-white grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    <template x-for="game in games" :key="game.steam_app_id">
                        <li x-on:mouseover="modalData = game" class="flex flex-row flex-grow overflow-hidden cursor-pointer duration-300 ease-in-out hover:scale-105 max-h-32" :id="`game${game.steam_app_id}`" style="opacity: 0">
                            <img x-show="game.image != null" x-bind:src="game.image.image_url" class="object-contain w-full" />
                        </li>
                    </template>

                    <div id="modal" class="absolute w-[400px] px-2 py-4 bg-[#16253B]/90 backdrop-brightness-200 backdrop-blur-3xl rounded-md" style="opacity: 0">
                        <ul class="flex flex-col px-4 gap-y-4">
                            <template x-for="modifier in modalData.modifiers">
                                <li>
                                    <p class="px-2 py-1 rounded-xl" :class="`bg-${modifier.color}-600`" x-text="modifier.title"></p>
                                </li>
                            </template>
                        </ul>
                    </div>
                </ul>
            </div>
        </div>

        <!-- Sidebar show button -->
        <div x-on:click="showSettings" class="absolute cursor-pointer flex items-center group right-0 top-0 w-12 h-[100vh] hover:bg-gradient-to-r hover:from-transparent hover:to-slate-700 duration-300 ease-in-out opacity-25 hover:opacity-75">
            <span class="mx-auto text-white group-hover:scale-110">
                <i class="fa-solid fa-chevron-left fa-xl"></i>
            </span>
        </div>

        <!-- Settings Sidebar -->
        <div x-show="showSidebar" id="sidebar" class="absolute shadow-xl right-0 flex flex-col gap-y-12 top-0 w-0 h-[100vh] bg-slate-700 text-white">
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
                        <button class="px-4 py-2 text-lg font-semibold bg-green-400 rounded-lg" x-on:click="settings.movie = true" :class="settings.movie ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Show</button>
                        <button class="px-4 py-2 text-lg font-semibold bg-red-400 rounded-lg duration-150 ease-in hover:scale-105" x-on:click="settings.movie = false" :class="!settings.movie ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Hide</button>
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

                <div class="flex flex-col px-6 mr-auto">
                    <h2 class="text-xl font-bold">Music</h2>
                    <div class="flex gap-x-4">
                        <button class="px-4 py-2 text-lg font-semibold bg-green-400 rounded-lg" x-on:click="settings.music = true" :class="settings.music ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Show</button>
                        <button class="px-4 py-2 text-lg font-semibold bg-red-400 rounded-lg" x-on:click="settings.music = false" :class="!settings.music ? 'duration-150 ease-in hover:scale-105' : 'opacity-25'">Hide</button>
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
                    movie: true,
                    unreleased: true,
                    free: true,
                    music: true,
                },
                showIntro: true,
                showModal: false,
                modalData: {
                    "title": null,
                },
                games: [],

                init() {
                    gsap.registerPlugin(TextPlugin);

                    this.infoModal();

                    this.syncSettings();

                    this.$watch('search', () => {
                        clearTimeout(this.timer);
                        this.timer = setTimeout(() => {
                            this.getGames();
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

                    this.introductionText();
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
                            const numberOfGames = 18;

                            const response = await fetch(`/api/steam/search/${this.search}?limit=${numberOfGames * 2}`);

                            const games = await response.json();

                            // Add new games to games array (up to a maximum length of 5)
                            for (const game of games) {
                                if (this.games.length >= numberOfGames) {
                                    break;
                                }

                                const optionsString = JSON.stringify(this.settings);

                                const response = await fetch(`/api/steam/search/get/${game.id}?options=${encodeURIComponent(optionsString)}`);

                                const item = await response.json();

                                this.loadProgress += 100 / numberOfGames;

                                if (item.errors) {
                                    continue;
                                }

                                this.games.push(item);

                                // Animate element
                                Alpine.nextTick(() => {
                                    gsap.fromTo(`#game${item.steam_app_id}`, { opacity: 0 }, {
                                        duration: 0.5,
                                        ease: "sine.in",
                                        opacity: 1,
                                        delay: 0.5,
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
                },

                syncSettings() {
                    this.settings.dlc = localStorage.dlc == "true";
                    this.settings.movie = localStorage.movie == "true";
                    this.settings.unreleased = localStorage.unreleased == "true";
                    this.settings.free = localStorage.free == "true";
                    this.settings.music = localStorage.music == "true";

                    this.$watch('settings', () => {
                        this.getGames();

                        localStorage.dlc = this.settings.dlc;
                        localStorage.movie = this.settings.movie;
                        localStorage.unreleased = this.settings.unreleased;
                        localStorage.free = this.settings.free;
                        localStorage.music = this.settings.music;
                    });
                },

                introductionText() {
                    gsap.fromTo("#introduction-text",
                        {
                            text: {
                                value: ""
                            },
                        },
                        {
                            text: {
                                value: "Welcome to steam.priceprober.com"
                            },
                            duration: 1,
                            ease: "none"
                        }
                    ).then(() => {
                        const timeline = gsap.timeline({
                            delay: 1
                        });

                        const reverse = gsap.fromTo("#introduction-text",
                            {
                                text: {
                                    value: "",
                                },
                            },
                            {
                                text: {
                                    value: "Welcome to steam.priceprober.com"
                                },
                                duration: 1,
                                ease: "none"
                            }
                        );

                        timeline.add(reverse.reverse(0));
                    });
                },

                infoModal() {
                    const modal = document.querySelector('#modal');
                    const wrapper = document.querySelector('#results');

                    function moveModal(e) {
                        const mouseX = e.pageX;
                        const mouseY = e.pageY;
                        const windowWidth = window.innerWidth;
                        const modalWidth = modal.offsetWidth;
                        const modalOffsetX = mouseX + 20 + modalWidth > windowWidth ? mouseX - modalWidth - 20 : mouseX + 20;

                        TweenLite.to(modal, 0.3, { left: modalOffsetX, top: mouseY + 20 });
                    }

                    wrapper.addEventListener('mouseenter', function() {
                        TweenLite.to(modal, 0.4, { scale: 1, autoAlpha: 1 });
                        wrapper.addEventListener('mousemove', moveModal);
                    });

                    wrapper.addEventListener('mouseleave', function() {
                        TweenLite.to(modal, 0.4, { scale: 0.1, autoAlpha: 0 });
                        wrapper.removeEventListener('mousemove', moveModal);
                    });
                }
            }));
        })

    </script>
</x-app-layout>

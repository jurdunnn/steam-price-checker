<x-app-layout>
    <div
        x-data="data"
        class="relative min-h-screen bg-gray-100 bg-center sm:flex sm:justify-center bg-dots-darker dark:bg-gray-900 selection:bg-red-500 selection:text-white"
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
                            <li class="flex flex-row flex-grow px-4 overflow-hidden" :id="`game${game.id}`">
                                <img x-show="game.image != null" x-bind:src="game.image.image_url" class="object-contain w-48" />

                                <div class="flex flex-col justify-between py-6 ml-4">
                                    <p x-show="game.title != null" class="font-semibold" x-text="game.title"></p>

                                    <!--
                                        Unfortunately an alpinejs bug occurred here, preventing a loop from being possible.

                                        Though annoying, it's not massively a problem, as the output below would have needed
                                        to be limited to ~3 modifiers anyway.
                                    -->
                                    <div x-show="game.modifiers.length > 0" class="flex overflow-hidden flex-row max-w-full text-sm gap-x-1">
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
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('data', () => ({
                search: '',
                loadProgress: 0,
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
                                    height: "50vh",
                                    duration: 0.3,
                                });
                            }

                            // If search is empty, animate hiding results.
                            if (this.search == '') {
                                gsap.fromTo("#results", { height: "50vh" }, {
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
                                this.loadProgress = 0;
                            }, 2000);
                        }
                    });
                },

                async getGames() {
                    if (this.search.length > 0) {
                        this.loadProgress = 0;

                        try {
                            const response = await fetch(`/api/steam/search/${this.search}`);
                            const games = await response.json();

                            // Remove games from this.games if they are already present in the newly fetched games
                            this.games = this.games.filter((game) => !games.some((g) => g.id === game.id));

                            // Remove games which do not match search to search.
                            this.games = this.games.filter((game) => game.title.includes(this.search));

                            // Add new games to games array (up to a maximum length of 5)
                            for (const game of games) {

                                const response = await fetch(`/api/steam/search/get/${game.id}`);
                                const item = await response.json();

                                // Shift first game to make room for more
                                if (this.games.length >= 5) {
                                    this.games.shift(item);
                                }

                                this.loadProgress = this.loadProgress + (100 / games.length);

                                this.games.push(item);

                                // Animate element
                                Alpine.nextTick(() => {
                                    gsap.from(`#game${item.id}`, {
                                        duration: 1,
                                        ease: "sine.out",
                                        opacity: 0.5
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

<div class="mediable fixed inset-0 h-full w-full z-50 will-change-transform" x-data="initMediableBrowser()" x-show="state.show" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90">

    <div class="absolute inset-0 bg-black bg-opacity-40" @click="show = false"></div>

    <div @class(['bg-white rounded-lg shadow-lg flex justify-start fixed top-8 left-8 right-8 bottom-8 overflow-hidden'=> !$fullScreen, 'bg-white rounded-none shadow-none flex justify-start fixed top-0 left-0 right-0 bottom-0 overflow-hidden' => $fullScreen])>

        <div class="flex h-full bg-white flex-grow">
            <div class="relative flex flex-col justify-between w-full h-full overflow-hidden">

                <div class="bg-gray-100 h-[70px] max-h-[70px] min-h-[70px] shadow-lg w-full">
                    @include("mediable::tailwind.includes.header")
                </div>

                <div class="bg-white h-full overflow-hidden flex justify-between border-t border-b border-gray-300 w-full">

                    <div class="flex flex-col w-full">
                        @include("mediable::tailwind.includes.toolbar")
                        <div class="relative p-0 m-0 h-full w-full">
                            <div @class(["absolute top-0 left-0 bottom-0 right-0 h-full w-full p-0 m-0 overflow-auto opacity-0 invisible transition-opacity duration-300 delay-200", "opacity-100 !visible z-10"=> $thumbMode || $tableMode])>
                                @include("mediable::tailwind.includes.attachments")
                            </div>
                            <div @class(["absolute top-0 left-0 bottom-0 right-0 h-full w-full p-0 m-0 overflow-auto opacity-0 invisible transition-opacity duration-300 delay-200", "opacity-100 !visible z-10"=> $uploadMode])>
                                @include("mediable::tailwind.includes.uploads")
                            </div>
                        </div>
                        @include("mediable::tailwind.includes.pager")
                    </div>

                    @if(!$this->uploadMode && $this->showSidebar)
                    <div class="hidden relative lg:flex bg-gray-200 border-l border-gray-300 w-[267px] min-w-[267px] max-w-[267px]">
                        @include("mediable::tailwind.includes.sidebar")
                    </div>
                    @endif

                </div>

                <div class="bg-gray-100 h-[60px] max-h-[60px] min-h-[60px] w-full">
                    @include("mediable::tailwind.includes.footer")
                </div>

            </div>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('initMediableBrowser', () => ({

        state: @entangle('state').live,

        init() {
            Livewire.on('mediable:alert', event => this.alert(event?.detail));
            Livewire.on('mediable:insert', event => this.insert(event?.selected));
            Livewire.on('audio:start', event => this.audioStart(event?.id));
            Livewire.on('audio:pause', event => this.audioPause(event?.id));
        },

        audioStart(id) {
            const audio = document.getElementById('audioPlayer' + id);

            if (audio) {
                audio.play();
            }
        },

        audioPause(id) {
            const audio = document.getElementById('audioPlayer' + id);

            if (audio) {
                audio.pause();
            }
        },

        insert(selected) {
            if (!Array.isArray(selected)) {
                return;
            }

            let insert = [];
            for (let i = 0; i < selected.length; i++) {
                let mediaType = selected[i].file_type.toLowerCase();

                let fileTitle = (selected[i].file_title) ? selected[i].file_title : selected[i].file_name;

                if (mediaType.indexOf('image') != -1) {
                    insert.push('<a href="' + selected[i].file_url + '"><img src="' + selected[i].file_url + '" alt="' + fileTitle + '"></a>\n');
                } else if (mediaType.indexOf('audio') != -1) {
                    insert.push('<audio controls autoplay><source src="' + selected[i].file_url + '" type="' + selected[i].file_type + '"></audio>\n');
                } else if (mediaType.indexOf('video') != -1) {
                    insert.push('<video controls autoplay><source src="' + selected[i].file_url + '" type="' + selected[i].file_type + '"></video>\n');
                } else {
                    insert.push('<a href="' + selected[i].file_url + '">' + fileTitle + '</a>');
                }
            }

            if (insert.length) {
                const el = document.getElementById(this.state.elementId);
                if (el) {
                    Mediable.insertAtCursor(el, insert.join(' '));
                }
            }
        },

        alert(event) {
            console.warn('alert:warning', event);
        }
    }))
</script>
@endscript